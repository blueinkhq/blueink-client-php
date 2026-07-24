<?php

namespace Blueink\ClientSDK;

class VerifySubClient extends SubClient
{
    /**
     * Verify a signed PDF against the Blueink application (POST /verify/).
     *
     * @param array $data Payload containing the hash (sha256) of the document
     *                    to verify.
     */
    public function create(array $data): NormalizedResponse
    {
        $url = $this->buildURL(VerifyEndpoints::create());

        return $this->request->post($url, ['json' => $data]);
    }
}
