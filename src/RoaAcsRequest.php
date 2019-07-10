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

use JimChen\AliyunCore\Auth\AbstractCredential;
use JimChen\AliyunCore\Auth\BearerTokenCredential;
use JimChen\AliyunCore\Auth\Credential;
use JimChen\AliyunCore\Auth\ISigner;

abstract class RoaAcsRequest extends AcsRequest
{
    protected $uriPattern;
    private $pathParameters = array();
    private $domainParameters = array();
    private $dateTimeFormat = "D, d M Y H:i:s \G\M\T";
    private static $headerSeparator = "\n";
    private static $querySeprator = "&";

    /**
     * @var string
     */
    protected $method = 'RAW';
    /**
     * @var string
     */
    protected $acceptFormat = 'JSON';

    public function composeUrl($iSigner, $credential, $domain)
    {
        $this->headers['x-acs-version'] = &$this->version;

        /**
         * @var ISigner $iSigner
         * @var Credential $credential
         */
        $this->prepareHeader($iSigner, $credential);

        $signString = $this->getMethod() . self::$headerSeparator;
        if (isset($this->headers["Accept"])) {
            $signString = $signString . $this->headers["Accept"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Content-MD5"])) {
            $signString = $signString . $this->headers["Content-MD5"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Content-Type"])) {
            $signString = $signString . $this->headers["Content-Type"];
        }
        $signString = $signString . self::$headerSeparator;

        if (isset($this->headers["Date"])) {
            $signString = $signString . $this->headers["Date"];
        }
        $signString = $signString . self::$headerSeparator;

        $uri = $this->replaceOccupiedParameters();
        $signString = $signString . $this->buildCanonicalHeaders();
        $queryString = $this->buildQueryString($uri);
        if (substr($queryString, -1) === '?') {
            $queryString = substr($queryString, 0, -1);
        }
        $signString .= $queryString;
        $this->headers["Authorization"] = "acs " . $credential->getAccessKeyId() . ":"
            . $iSigner->signString($signString, $credential->getAccessSecret());
        $requestUrl = $this->getProtocol() . "://" . $domain . $uri . $this->concatQueryString();

        return $requestUrl;
    }

    private function concatQueryString()
    {
        $sortMap = $this->queryParameters;
        if (null == $sortMap || count($sortMap) == 0) {
            return "";
        }
        $queryString = "";
        ksort($sortMap);
        foreach ($sortMap as $sortMapKey => $sortMapValue) {
            $queryString = $queryString . $sortMapKey;
            if (isset($sortMapValue)) {
                $queryString = $queryString . "=" . urlencode($sortMapValue);
            }
            $queryString .= self::$querySeprator;
        }

        if (count($sortMap) > 0) {
            $queryString = substr($queryString, 0, strlen($queryString) - 1);
        }

        return '?' . $queryString;
    }

    private function prepareHeader($iSigner, $credential)
    {
        /**
         * @var ISigner $iSigner
         * @var AbstractCredential $credential
         */
        $this->headers["Date"] = gmdate($this->dateTimeFormat);
        if (null == $this->acceptFormat) {
            $this->acceptFormat = "RAW";
        }
        $this->headers["Accept"] = $this->formatToAccept($this->getAcceptFormat());
        $this->headers["x-acs-signature-method"] = $iSigner->getSignatureMethod();
        $this->headers["x-acs-signature-version"] = $iSigner->getSignatureVersion();
        if ($iSigner->getSignatureType() != null) {
            $this->headers['x-acs-signature-type'] = $iSigner->getSignatureType();
        }
        $this->headers['x-acs-region-id'] = $this->regionId;
        $content                          = $this->getDomainParameter();
        if ($content != null) {
            $this->headers['Content-MD5'] = base64_encode(md5(json_encode($content), true));
        }
        if ($this->acceptFormat === 'JSON') {
            $this->headers['Content-Type'] = 'application/json;charset=utf-8';
        } else {
            $this->headers['Content-Type'] = 'application/octet-stream;charset=utf-8';
        }
        if ($credential->getSecurityToken() != null) {
            $this->headers['x-acs-security-token'] = $credential->getSecurityToken();
        }
        if ($credential instanceof BearerTokenCredential) {
            $this->headers['x-acs-bearer-token'] = $credential->getBearerToken();
        }
    }

    private function replaceOccupiedParameters()
    {
        $result = $this->uriPattern;
        foreach ($this->pathParameters as $pathParameterKey => $apiParameterValue) {
            $target = "[" . $pathParameterKey . "]";
            $result = str_replace($target, $apiParameterValue, $result);
        }

        return $result;
    }

    private function buildCanonicalHeaders()
    {
        $sortMap = array();
        foreach ($this->headers as $headerKey => $headerValue) {
            $key = strtolower($headerKey);
            if (strpos($key, "x-acs-") === 0) {
                $sortMap[$key] = $headerValue;
            }
        }
        ksort($sortMap);
        $headerString = "";
        foreach ($sortMap as $sortMapKey => $sortMapValue) {
            $headerString = $headerString . $sortMapKey . ":" . $sortMapValue . self::$headerSeparator;
        }

        return $headerString;
    }

    private function splitSubResource($uri)
    {
        $queIndex = strpos($uri, "?");
        $uriParts = array();
        if (null != $queIndex) {
            $uriParts[] = substr($uri, 0, $queIndex);
            $uriParts[] = substr($uri, $queIndex + 1);
        } else {
            $uriParts[] = $uri;
        }

        return $uriParts;
    }

    private function buildQueryString($uri)
    {
        $uriParts = $this->splitSubResource($uri);
        $sortMap = $this->queryParameters;
        if (isset($uriParts[1])) {
            $sortMap[$uriParts[1]] = null;
        }
        $queryString = $uriParts[0];
        if (count($sortMap) > 0) {
            $queryString = $queryString . "?";
        }
        ksort($sortMap);
        foreach ($sortMap as $sortMapKey => $sortMapValue) {
            $queryString = $queryString . $sortMapKey;
            if (isset($sortMapValue)) {
                $queryString = $queryString . "=" . $sortMapValue;
            }
            $queryString = $queryString . self::$querySeprator;
        }
        if (count($sortMap) > 0) {
            $queryString = substr($queryString, 0, strlen($queryString) - 1);
        }

        return $queryString;
    }

    private function formatToAccept($acceptFormat)
    {
        if ($acceptFormat == "JSON") {
            return "application/json";
        } elseif ($acceptFormat == "XML") {
            return "application/xml";
        }

        return "application/octet-stream";
    }

    public function getPathParameters()
    {
        return $this->pathParameters;
    }

    public function putPathParameter($name, $value)
    {
        $this->pathParameters[$name] = $value;
    }

    public function getDomainParameter()
    {
        return $this->domainParameters;
    }

    public function putDomainParameters($name, $value)
    {
        $this->domainParameters[$name] = $value;
    }

    public function getUriPattern()
    {
        return $this->uriPattern;
    }

    public function setUriPattern($uriPattern)
    {
        return $this->uriPattern = $uriPattern;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        $this->headers["x-acs-version"] = $version;
    }
}
