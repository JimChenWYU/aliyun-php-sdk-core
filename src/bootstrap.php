<?php

/*
 * This file is part of the jimchen/aliyun-php-sdk-core.
 *
 * (c) JimChen <imjimchen@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

require __DIR__.'/Regions/EndpointConfig.php';

//config http proxy
/**
 *
 */
defined('ENABLE_HTTP_PROXY') or define('ENABLE_HTTP_PROXY', false);
/**
 *
 */
defined('HTTP_PROXY_IP') or define('HTTP_PROXY_IP', '127.0.0.1');
/**
 *
 */
defined('HTTP_PROXY_PORT') or define('HTTP_PROXY_PORT', '8888');
