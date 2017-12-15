<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;

class Gladiator extends RestApiClient
{
    use AuthorizesWithApiKey;

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
     * Create a new Gladiator API client.
     * @param array $config
     * @param array $overrides
     */
    public function __construct($config = [], $overrides = [])
    {
        // Save configuration.
        $this->config = $config;

        // Set response header.
        if (! empty($config['gladiator_api_key'])) {
            $this->apiKeyHeader = 'X-DS-Gladiator-API-Key';
            $this->apiKey = $config['gladiator_api_key'];
        }
        parent::__construct($config['url'], $overrides);
    }

    /**
     * Send a POST request Gladiator to add user to a contest.
     *
     * @param  string $userId
     * @param  string $campaignId
     * @param  string $campaignRunId
     *
     * @return bool
     */
    public function storeUserInContest($userId, $campaignId, $campaignRunId)
    {
        $response = $this->post('v1/users', [
            'id' => $userId,
            'term' => 'id',
            'campaign_id' => $campaignId,
            'campaign_run_id' => $campaignRunId,
        ]);

        return $this->responseSuccessful($response);
    }

    /**
     * Send a POST request Gladiator unsubscribe users from competition messages.
     *
     * @param  string $userId
     * @param  string $competitionId
     *
     * @return bool
     */
    public function unsubscribeUser($userId, $competition_id)
    {
        $response = $this->post('v1/unsubscribe', [
            'northstar_id' => $userId,
            'competition_id' => $competition_id,
        ]);

        return $response;
    }

    /**
     * Determine if the response was successful or not.
     *
     * @param mixed $json
     * @return bool
     */
    public function responseSuccessful($json)
    {
        return ! empty($json['data']) || ! empty($json['message']);
    }
}
