<?php

/*
 * This file is part of the /imchen/aliyun-php-sdk-core.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace UnitTest;

use Faker\Factory;
use JimChen\AliyunCore\Http\HttpResponse;
use UnitTest\BatchCompute\ListImagesRequest;
use UnitTest\Ecs\Request\DescribeRegionsRequest;

class DefaultAcsClientTestCase extends BaseTestCase
{
    public function testDoActionRPC()
    {
        $request = new DescribeRegionsRequest();

        $faker = Factory::create();

        $response = new HttpResponse();
        $response->setStatus(200);
        $response->setBody(json_encode(array(
            'RequestId' => $faker->md5,
            'Regions'   => array(
                'Region' => array(
                    array(
                        'RegionId'  => $faker->numberBetween(1000, 9999),
                        'LocalName' => $faker->address,
                    )
                ),
            ),
        )));

        \Mockery::close();
        $mock = \Mockery::mock('alias:\JimChen\AliyunCore\Http\HttpHelper');
        $mock->shouldReceive('curl')
            ->once()
            ->withAnyArgs()
            ->andReturn($response);

        $response = @$this->client->doAction($request);
        $response = json_decode($response->getBody());

        $this->assertNotNull($response->RequestId);
        $this->assertNotNull($response->Regions->Region[0]->LocalName);
        $this->assertNotNull($response->Regions->Region[0]->RegionId);
    }

    public function testDoActionROA()
    {
        $request = new ListImagesRequest();

        $response = new HttpResponse();
        $response->setBody('{"foo": "bar"}');

        \Mockery::close();
        $mock = \Mockery::mock('alias:\JimChen\AliyunCore\Http\HttpHelper');
        $mock->shouldReceive('curl')
            ->once()
            ->withAnyArgs()
            ->andReturn($response);

        $response = @$this->client->doAction($request);
        $response = json_decode($response->getBody());
        $this->assertNotNull($response);
    }
}
