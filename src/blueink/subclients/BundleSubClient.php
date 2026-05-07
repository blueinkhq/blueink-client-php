<?php

namespace Blueink\ClientSDK;

class BundleSubClient extends SubClient
{
    /**
     * Build the multipart payload required by Bundle uploads.
     *
     * Pulls files out of the BundleHelper-shaped data array (file_names,
     * file_types, files), strips them from the JSON payload, and returns:
     *   [bundle_request_json, [multipart parts ...]]
     *
     * @return array{0: string, 1: array}
     */
    private function prepareFiles(array $data): array
    {
        $file_names = $data['file_names'] ?? [];
        $file_types = $data['file_types'] ?? [];
        $files      = $data['files'] ?? [];
        unset($data['file_names'], $data['file_types'], $data['files']);

        $multipart = [
            [
                'name'     => 'bundle_request',
                'contents' => json_encode($data),
                'headers'  => ['Content-Type' => 'application/json'],
            ],
        ];

        foreach ($files as $i => $file) {
            $name = $file_names[$i] ?? basename(is_string($file) ? $file : "file_$i");
            $type = $file_types[$i] ?? 'application/octet-stream';
            $contents = is_resource($file) ? $file : (is_string($file) && is_file($file) ? fopen($file, 'r') : $file);
            $multipart[] = [
                'name'     => "files[$i]",
                'contents' => $contents,
                'filename' => $name,
                'headers'  => ['Content-Type' => $type],
            ];
        }

        return [json_encode($data), $multipart];
    }

    /**
     * Create a Bundle. When the data array contains files (file_names,
     * file_types, files) the request is sent as multipart/form-data, otherwise
     * a JSON request is used.
     */
    public function create(?array $data = null): NormalizedResponse
    {
        if (is_null($data)) {
            throw new \InvalidArgumentException('Bundle data is required');
        }

        $url = $this->buildURL(BundleEndpoints::create());
        $has_files = !empty($data['files']);

        if (!$has_files) {
            return $this->request->post($url, ['json' => $data]);
        }

        [, $multipart] = $this->prepareFiles($data);

        return $this->request->post($url, ['multipart' => $multipart]);
    }

    /**
     * Convenience wrapper for callers using BundleHelper to assemble a Bundle.
     */
    public function createFromBundleHelper(BundleHelper $bundle_helper): NormalizedResponse
    {
        return $this->create($bundle_helper->asData());
    }

    /**
     * Create a Bundle from an envelope template. Expects the payload shape
     * produced by BundleHelper::asDataForEnvelopeTemplate(), but accepts any
     * pre-built array.
     */
    public function createFromEnvelopeTemplate(?array $data = null): NormalizedResponse
    {
        if (is_null($data)) {
            throw new \InvalidArgumentException('Envelope template data is required');
        }

        $url = $this->buildURL(BundleEndpoints::createFromEnvelopeTemplate());

        return $this->request->post($url, ['json' => $data]);
    }

    /**
     * Convenience wrapper that serializes a BundleHelper configured with an
     * envelope template and posts it to create_from_envelope_template.
     */
    public function createFromEnvelopeTemplateHelper(BundleHelper $bundle_helper): NormalizedResponse
    {
        return $this->createFromEnvelopeTemplate($bundle_helper->asDataForEnvelopeTemplate());
    }

    /**
     * Returns a Paginated iterator that lazily fetches Bundle pages.
     */
    public function pagedList(int $page = 1, int $per_page = 50, bool $related_data = false, ?array $query_params = null): Paginated
    {
        $fn = function (array $args) use ($related_data) {
            return $this->list($args['page'], $args['per_page'], $related_data, $args['additional_data']);
        };

        return new Paginated($fn, $page, $per_page, $query_params);
    }

    /**
     * Return a list (single page) of Bundles.
     */
    public function list(?int $page = null, ?int $per_page = null, bool $related_data = false, ?array $additional_data = null): NormalizedResponse
    {
        $params = $this->buildParams($page, $per_page, $additional_data);
        $url = $this->buildURL(BundleEndpoints::list());
        $response = $this->request->get($url, ['query' => $params]);

        if ($related_data && is_array($response->data)) {
            foreach ($response->data as &$bundle) {
                $this->_attachAdditionalData($bundle);
            }
        }

        return $response;
    }

    public function retrieve(string $bundle_id, bool $related_data = false): NormalizedResponse
    {
        $url = $this->buildURL(BundleEndpoints::retrieve($bundle_id));
        $response = $this->request->get($url);

        if ($related_data && is_array($response->data)) {
            $this->_attachAdditionalData($response->data);
        }

        return $response;
    }

    public function cancel(string $bundle_id): NormalizedResponse
    {
        $url = $this->buildURL(BundleEndpoints::cancel($bundle_id));

        return $this->request->put($url);
    }

    public function listEvents(string $bundle_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(BundleEndpoints::listEvents($bundle_id)));
    }

    public function listFiles(string $bundle_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(BundleEndpoints::listFiles($bundle_id)));
    }

    public function listData(string $bundle_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(BundleEndpoints::listData($bundle_id)));
    }

    /**
     * Mutates the supplied bundle array to attach events, and (when the
     * bundle is COMPLETE) files and field data, by hitting the related
     * sub-resources.
     */
    public function _attachAdditionalData(array &$bundle): void
    {
        if (!isset($bundle['id'])) {
            return;
        }
        $bundle_id = $bundle['id'];

        $events_response = $this->listEvents($bundle_id);
        if ($events_response->status === 200) {
            $bundle['events'] = $events_response->data;
        }

        if (($bundle['status'] ?? null) === BUNDLE_STATUS['COMPLETE']) {
            $bundle['files'] = $this->listFiles($bundle_id)->data;
            $bundle['data']  = $this->listData($bundle_id)->data;
        }
    }
}
