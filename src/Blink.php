<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;
// use DoSomething\Gateway\Resources\BlinkCampaign;
// use DoSomething\Gateway\Resources\BlinkCampaignCollection;

class Blink extends RestApiClient
{
    use AuthorizesWithBlink;

    /**
     * Configuration array.
     *
     * @var string
     */
    protected $config;

    /**
     * Default headers applied to every request.
     *
     * @var array
     */
    protected $defaultHeaders;

    /**
     * Create a new Blink API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Save configuration.
        $this->config = $config;

        // Set response header.
        if (! empty($config['apiKey'])) {
            $this->apiKey = $config['apiKey'];
        }
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a Post request Blink /events/user-create endpoint.
     *
     * To notify Blink that user has been created.
     *
     * @param string $id - Signup
     * @return bool
     */
    public function userCreate()
    {
        // $payload = [
        //     'id' => $id,
        //     'source' => $source,
        // ];
        // $response = $this->post('v1/signups/', $payload);

        // return $this->responseSuccessful($response);
    }
}
