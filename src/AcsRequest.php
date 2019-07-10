<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
namespace JimChen\AliyunCore;

abstract class AcsRequest
{
	protected  $version;
	protected  $product;
	protected  $actionName;
	protected  $regionId;
	protected  $acceptFormat;
	protected  $method;
	protected  $protocolType = "http";
	protected  $content;
	
	protected $queryParameters = array();
	protected $headers = array();
	
	protected $locationServiceCode;
	protected $locationEndpointType;
    /**
     * @var array The original parameters of the request object.
     */
    protected $requestParameters = array();
    /**
     * @var string
     */
    protected $stringToBeSigned = '';

	function  __construct($product, $version, $actionName, $locationServiceCode = null, $locationEndpointType = "openAPI")
	{
	    $this->headers["x-sdk-client"] = "php/2.0.0";
	    $this->product = $product;
	    $this->version = $version;
	    $this->actionName = $actionName;
	    
	    $this->locationServiceCode = $locationServiceCode;
	    $this->locationEndpointType = $locationEndpointType;
	}
	
	public abstract function composeUrl($iSigner, $credential, $domain);
	
	public function getVersion()
	{
		return $this->version;
	}
	
	public function setVersion($version)
	{
		$this->version = $version;
	}
	
	public function getProduct()
	{
		return $this->product;
	}
	
	public function setProduct($product)
	{
		$this->product = $product;
	}
	
	public function getActionName()
	{
		return $this->actionName;
	}
	
	public function setActionName($actionName)
	{
		$this->actionName = $actionName;
	}
	
	public function getAcceptFormat()
	{
		return	$this->acceptFormat;
	}
	
	public function setAcceptFormat($acceptFormat)
	{
		$this->acceptFormat = $acceptFormat;
	}
	
	public function getQueryParameters()
	{
		return $this->queryParameters;
	}
	
	public function getHeaders()
	{
		return $this->headers;
	}
	
	public function getMethod()
	{
		return $this->method;
	}
	
	public function setMethod($method)
	{
		$this->method = $method;
	}
	
	public function getProtocol()
	{
		return $this->protocolType;
	}
	
	public function setProtocol($protocol)
	{
		$this->protocolType = $protocol;
	}
	
	public function getRegionId()
	{
		return $this->regionId;
	}
	public function setRegionId($region)
	{
		$this->regionId = $region;
	}
	
	public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }
        
        
    public function addHeader($headerKey, $headerValue)
    {
        $this->headers[$headerKey] = $headerValue;
    } 
	
	public function getLocationServiceCode()
	{
		return $this->locationServiceCode;
	}

	public function getLocationEndpointType()
	{
		return $this->locationEndpointType;
	}

    /**
     * Magic method for get parameters.
     *
     * @param string $name
     * @param mixed  $arguments
     *
     * @return $this
     */
    public function __call($name, $arguments)
    {
        if (\strpos($name, 'get', 0) !== false) {
            $parameterName = $this->propertyNameByMethodName($name);
            return isset($this->requestParameters[$parameterName])
                ? $this->requestParameters[$parameterName]
                : null;
        }
        return $this;
    }

    /**
     * @param string $methodName
     *
     * @return string
     */
    protected function propertyNameByMethodName($methodName)
    {
        return \mb_strcut($methodName, 3);
    }

    /**
     * @return string
     */
    public function stringToBeSigned()
    {
        return $this->stringToBeSigned;
    }
}