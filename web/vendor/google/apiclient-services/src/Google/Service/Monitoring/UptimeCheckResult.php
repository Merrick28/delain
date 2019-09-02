<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_Monitoring_UptimeCheckResult extends Google_Model
{
  public $checkPassed;
  public $contentMismatch;
  public $errorCode;
  public $httpStatus;
  protected $monitoredResourceType = 'Google_Service_Monitoring_MonitoredResource';
  protected $monitoredResourceDataType = '';
  public $requestLatency;

  public function setCheckPassed($checkPassed)
  {
    $this->checkPassed = $checkPassed;
  }
  public function getCheckPassed()
  {
    return $this->checkPassed;
  }
  public function setContentMismatch($contentMismatch)
  {
    $this->contentMismatch = $contentMismatch;
  }
  public function getContentMismatch()
  {
    return $this->contentMismatch;
  }
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  public function setHttpStatus($httpStatus)
  {
    $this->httpStatus = $httpStatus;
  }
  public function getHttpStatus()
  {
    return $this->httpStatus;
  }
  /**
   * @param Google_Service_Monitoring_MonitoredResource
   */
  public function setMonitoredResource(Google_Service_Monitoring_MonitoredResource $monitoredResource)
  {
    $this->monitoredResource = $monitoredResource;
  }
  /**
   * @return Google_Service_Monitoring_MonitoredResource
   */
  public function getMonitoredResource()
  {
    return $this->monitoredResource;
  }
  public function setRequestLatency($requestLatency)
  {
    $this->requestLatency = $requestLatency;
  }
  public function getRequestLatency()
  {
    return $this->requestLatency;
  }
}
