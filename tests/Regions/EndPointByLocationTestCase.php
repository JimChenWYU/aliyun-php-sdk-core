<?php
/**
 * Created by PhpStorm.
 * User: zhangw
 * Date: 2017/7/17
 * Time: 下午4:57
 */

namespace UnitTest\Regions;

use JimChen\AliyunCore\Http\HttpResponse;
use JimChen\AliyunCore\Profile\DefaultProfile;
use JimChen\AliyunCore\Regions\LocationService;
use UnitTest\BaseTestCase;

class EndPointByLocationTestCase extends BaseTestCase
{
    /**
     * @var LocationService
     */
    private $locationService;

    private $clientProfile;

    private function initClient()
    {
        # 创建 DefaultAcsClient 实例并初始化
        $this->clientProfile = DefaultProfile::getProfile(
            "cn-shanghai",                   # 您的 Region ID
            "<your AK>",               # 您的 Access Key ID
            "<your Secret>"            # 您的 Access Key Secret
        );

        $this->locationService = new LocationService($this->clientProfile);
    }

    public function testFindProductDomain()
    {
        $this->initClient();

        $response = new HttpResponse();
        $response->setStatus(200);
        $response->setBody(json_encode(array(
            'Endpoints' => array(
                'Endpoint' => array(
                    array(
                        'Endpoint' => 'apigateway.cn-shanghai.aliyuncs.com'
                    )
                )
            )
        )));

        \Mockery::close();
        $mock = \Mockery::mock('alias:\JimChen\AliyunCore\Http\HttpHelper');
        $mock->shouldReceive('curl')
            ->once()
            ->withAnyArgs()
            ->andReturn($response);

        $domain = $this->locationService->findProductDomain("cn-shanghai", "apigateway", "openAPI", "CloudAPI");
        $this->assertEquals("apigateway.cn-shanghai.aliyuncs.com", $domain);
    }

    public function testFindProductDomainWithAddEndPoint()
    {
        DefaultProfile::addEndpoint("cn-shanghai", "cn-shanghai", "CloudAPI", "apigateway.cn-shanghai123.aliyuncs.com");
        $this->initClient();
        $domain = $this->locationService->findProductDomain("cn-shanghai", "apigateway", "openAPI", "CloudAPI");
        $this->assertEquals("apigateway.cn-shanghai123.aliyuncs.com", $domain);
    }

}
