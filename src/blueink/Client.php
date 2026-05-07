<?php

namespace Blueink\ClientSDK;

/**
 * Top-level Blueink API client. Holds shared auth + HTTP configuration and
 * exposes resource-specific subclients (bundles, persons, packets, templates,
 * envelope_templates, webhooks).
 */
class Client
{
    private string $private_api_key;
    private string $base_url;
    private RequestHelper $request_helper;

    public BundleSubClient $bundles;
    public PersonSubClient $persons;
    public PacketSubClient $packets;
    public TemplateSubClient $templates;
    public EnvelopeTemplateSubClient $envelope_templates;
    public WebhookSubClient $webhooks;

    /**
     * @param string|null $private_api_key  Blueink private API key. Falls back to the
     *                                      BLUEINK_PRIVATE_API_KEY environment variable.
     * @param string|null $base_url         API base URL. Falls back to BLUEINK_API_URL,
     *                                      then to DEFAULT_BASE_URL.
     * @param bool        $raise_exceptions When false, 4XX/5XX responses are returned as
     *                                      NormalizedResponse instead of throwing.
     */
    public function __construct(
        ?string $private_api_key = null,
        ?string $base_url = null,
        bool $raise_exceptions = true
    ) {
        $private_api_key = $private_api_key ?: (getenv(ENV_BLUEINK_PRIVATE_API_KEY) ?: null);
        if (!$private_api_key) {
            throw new \InvalidArgumentException(
                'A Blueink Private API Key must be provided on Client initialization '
                . 'or specified via the ' . ENV_BLUEINK_PRIVATE_API_KEY . ' environment variable.'
            );
        }
        $this->private_api_key = $private_api_key;

        $base_url = $base_url ?: (getenv(ENV_BLUEINK_API_URL) ?: DEFAULT_BASE_URL);
        $this->base_url = $base_url;

        $this->request_helper = new RequestHelper($this->private_api_key, $raise_exceptions);
        $this->bundles   = new BundleSubClient($this->base_url, $this->request_helper);
        $this->persons   = new PersonSubClient($this->base_url, $this->request_helper);
        $this->packets   = new PacketSubClient($this->base_url, $this->request_helper);
        $this->templates = new TemplateSubClient($this->base_url, $this->request_helper);
        $this->envelope_templates = new EnvelopeTemplateSubClient($this->base_url, $this->request_helper);
        $this->webhooks  = new WebhookSubClient($this->base_url, $this->request_helper);
    }
}
