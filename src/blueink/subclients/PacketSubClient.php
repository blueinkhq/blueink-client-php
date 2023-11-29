<?php
    namespace Blueink\ClientSDK;
    require_once __DIR__ . "/../Endpoints.php";
    use Blueink\ClientSDK\SubClient;
    class PacketSubClient extends SubClient{
        /**
         * Update a Packet
         * 
         * @param string $packet_id: the ID of Packet
         * @param array $data: Array of update data. E.g: ['json' => []], ['body' => []]
         * 
         * @return mixed response patch request
         */
        public function update(string $packet_id, array $data) {
            $url = parent::buildURL(PacketEndpoints::UPDATE($packet_id));
            
            return parent::$request->patch($url, $data);
        }
        /**
         * Create an embedded signing URL
         * 
         * @param string $packet_id: the ID of the Packet
         * 
         * @return mixed response of the request
         */
        public function embedURL(string $packet_id) {
            $url = parent::buildURL(PacketEndpoints::embedURL($packet_id));

            return parent::$request->post($url);
        }
        /**
         * Retrieve COE
         * 
         * @param string $packet_id: the ID of the Packet
         * 
         * @return mixed response of the request
         */
        public function retrieveCOE(string $packet_id) {
            $url = parent::buildURL(PacketEndpoints::retrieveCOE($packet_id));

            return parent::$request->get($url);
        }
        /**
         * Send a reminder to this Packet
         * 
         * @param string $packet_id: the ID of the Packet
         * 
         * @return mixed response of the request
         */
        public function remind(string $packet_id) {
            $url = parent::buildURL(PacketEndpoints::REMIND($packet_id));

            return parent::$request->post($url);
        }
    }