<?php

/*
 * This file is part of the /imchen/aliyun-php-sdk-core.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace UnitTest;

use UnitTest\Ecs\Request\DescribeRegionsRequest;
use UnitTest\BatchCompute\ListImagesRequest;

class DefaultAcsClientTest extends BaseTest
{
    public function testDoActionRPC()
    {
        $request = new DescribeRegionsRequest();
        $response = @$this->client->doAction($request);
        $response = json_decode($response->getBody());

        $this->assertNotNull($response->RequestId);
        $this->assertNotNull($response->Regions->Region[0]->LocalName);
        $this->assertNotNull($response->Regions->Region[0]->RegionId);
    }

    public function testDoActionROA()
    {
        $request = new ListImagesRequest();
        $response = @$this->client->doAction($request);
        $response = json_decode($response->getBody());
        $this->assertNotNull($response);
    }
}
