<?php

use DoSomething\Gateway\Common\RestApiClient;

class RestApiClientTest extends TestCase
{
    /** @test */
    function makeRestApiClientForCustomResource()
    {
        $apiUrl = 'api.xavierinstitute.edu';

        $restClient = new RestApiClient('https://'.$apiUrl);

        $this->assertEquals($restClient->getGuzzleClient()->getConfig('base_uri')->getHost(), $apiUrl);
    }
}
