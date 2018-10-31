<?php

/*
 * This file is part of the /imchen/aliyun-php-sdk-core.
 *
 * (c) JimChen <18219111672@163.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use JimChen\AliyunCore\Http\HttpResponse;

if (!function_exists('tap')) {
    function tap($value, $callback = null)
    {
        if (is_null($callback)) {
            return new HigherOrderTapProxy($value);
        }
        call_user_func($callback, $value);

        return $value;
    }
}

if (!class_exists('HigherOrderTapProxy')) {
    class HigherOrderTapProxy
    {
        /**
         * The target being tapped.
         *
         * @var mixed
         */
        public $target;

        /**
         * Create a new tap proxy instance.
         *
         * @param  mixed $target
         * @return void
         */
        public function __construct($target)
        {
            $this->target = $target;
        }

        /**
         * Dynamically pass method calls to the target.
         *
         * @param  string $method
         * @param  array  $parameters
         * @return mixed
         */
        public function __call($method, $parameters)
        {
            call_user_func_array(array($this->target, $method), $parameters);

            return $this->target;
        }
    }
}

if (getenv('phpunit_running')) {
    $mock = \Mockery::mock('alias:\JimChen\AliyunCore\Http\HttpHelper');
    $mock->shouldReceive('curl')
        ->times(6)
        ->withAnyArgs()
        ->andReturn(tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Code' => 'Success',
            )));
        }), tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Code' => 'Success',
            )));
        }), tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Credentials' => array(
                    'AccessKeyId'     => '<your Ak>',
                    'AccessKeySecret' => '<your Secret>',
                    'SecurityToken'   => '<your ST>',
                ),
            )));
        }), tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Credentials' => array(
                    'AccessKeyId'     => '<your Ak>',
                    'AccessKeySecret' => '<your Secret>',
                    'SecurityToken'   => '<your ST>',
                ),
            )));
        }), tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Code'            => 'Success',
                'AccessKeyId'     => '<your Ak>',
                'AccessKeySecret' => '<your Secret>',
                'SecurityToken'   => '<your ST>',
            )));
        }), tap(new HttpResponse(), function (HttpResponse $response) {
            $response->setStatus(200);
            $response->setBody(json_encode(array(
                'Code'            => 'Success',
                'AccessKeyId'     => '<your Ak>',
                'AccessKeySecret' => '<your Secret>',
                'SecurityToken'   => '<your ST>',
            )));
        }));
}
