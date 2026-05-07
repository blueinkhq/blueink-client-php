<?php

namespace Blueink\ClientSDK;

class PacketSubClient extends SubClient
{
    public function update(string $packet_id, array $data): NormalizedResponse
    {
        $url = $this->buildURL(PacketEndpoints::update($packet_id));

        return $this->request->patch($url, ['json' => $data]);
    }

    /**
     * Create an embedded signing URL for the given Packet.
     */
    public function embedURL(string $packet_id): NormalizedResponse
    {
        return $this->request->post($this->buildURL(PacketEndpoints::embedURL($packet_id)));
    }

    /**
     * Retrieve a Certificate of Evidence (COE) for the Packet.
     */
    public function retrieveCOE(string $packet_id): NormalizedResponse
    {
        return $this->request->get($this->buildURL(PacketEndpoints::retrieveCOE($packet_id)));
    }

    /**
     * Send a reminder to the Packet recipient.
     */
    public function remind(string $packet_id): NormalizedResponse
    {
        return $this->request->put($this->buildURL(PacketEndpoints::remind($packet_id)));
    }
}
