<?php

/*
 * This file is part of the /imchen/aliyun-php-sdk-core.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace UnitTest;

use JimChen\AliyunCore\DefaultAcsClient;
use JimChen\AliyunCore\Profile\DefaultProfile;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public $client = null;

    public function setUp()
    {
        $iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "AccessKey", "AccessSecret");
        $this->client = new DefaultAcsClient($iClientProfile);
    }

    public function getProperty()
    {
        return DefaultProfile::getProfile("cn-hangzhou", "AccessKey", "AccessSecret");
    }
}
