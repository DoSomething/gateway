<?php

namespace DoSomething\Gateway\Resources;

use DoSomething\Gateway\Common\ApiCollection;

class GambitCampaignCollection extends ApiCollection
{
    public function __construct($response)
    {
        parent::__construct($response, GambitCampaign::class);
    }
}
