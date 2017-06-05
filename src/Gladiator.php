<?php

namespace DoSomething\Gateway;

use DoSomething\Gateway\Common\RestApiClient;

class Gladiator extends RestApiClient
{
    use AuthorizesWithGladiator;

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
    public function storeUserInContest(string $userId, $campaignId, $campaignRunId)
    {
        $response = $this->post('v1/users', [
            'id' => $userId,
            'term' => 'id',
            'campaign_id' => $legacyCampaignId,
            'campaign_run_id' => $legacyCampaignRunId,
        ]);

        // TODO: throw an exception if the post returns a validation error.
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
    public function unsubscribeUser(string $userId, $competition_id)
    {
        $response = $this->post('v1/unsubscribe', [
            'northstar_id' => $userId,
            'competition_id' => $competition_id,
        ]);

        // TODO: throw an exception if the post returns a validation error.
        return $this->responseSuccessful($response);
    }
}
