<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class YounetCore_Api_Package {

  protected $k = 'm2b';
  CONST VALIDATE_URL = 'http://licence.modules2buy.com/se4/';
  CONST PLATFORM = 'SOCIALENGINE4';

  function ed($t) {
    $r = md5($this->k);
    $c = 0;
    $v = "";
    for ($i = 0; $i < strlen($t); $i++) {
      if ($c == strlen($r))
        $c = 0;
      $v.= substr($t, $i, 1) ^ substr($r, $c, 1);
      $c++;
    }
    return $v;
  }

  function crypt($t) {
    srand((double) microtime() * 1000000);
    $r = md5(rand(0, 32000));
    $c = 0;
    $v = "";
    for ($i = 0; $i < strlen($t); $i++) {
      if ($c == strlen($r))
        $c = 0;
      $v.= substr($r, $c, 1) .
              (substr($t, $i, 1) ^ substr($r, $c, 1));
      $c++;
    }
    return base64_encode($this->ed($v));
  }

  function decrypt($t) {
    $t = $this->ed(base64_decode($t));
    $v = "";
    for ($i = 0; $i < strlen($t); $i++) {
      $md5 = substr($t, $i, 1);
      $i++;
      $v.= ( substr($t, $i, 1) ^ $md5);
    }
    return $v;
  }

  /**
   *
   * @var Core_Api_Settings
   */
  protected $_settings;

  public function getCurrentDomain() {
    return strtolower(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['HOST']) ? $_SERVER['HOST'] : ''));
  }

  public function isLocal($host = NULL) {
    if ($host === NULL) {
      $host = $this->getCurrentDomain();
    }

    if ($host == 'localhost') {
      return true;
    }

    return false;
  }

  /**
   * get verified setting name
   * @param  string $package
   * @return string
   */
  public function gvsn($package) {
    return sprintf('m2b.veri.%s', $package);
  }

  /**
   * get license key setting name for an package
   * @param    string  $package
   * @return   string
   */
  public function glksn($package) {
    return sprintf('m2b.lice.%s', $package);
  }

  /**
   * get host setting name for this package
   * @param    string  $package
   * @return   string
   */
  public function ghsn($package) {
    return sprintf('m2b.host.%s', $package);
  }

  /**
   * get setting api
   * @return Core_Api_Settings
   */
  public function getSettingsDbTable() {
    if ($this->_settings == NULL) {
      $this->_settings = Engine_Api::_()->getDbTable('settings', 'core');
    }
    return $this->_settings;
  }

  public function isVerified($package = '') {
    if ($this->isLocal()) {
      return true;
    }
    return $this->getSettingsDbTable()->getSetting(sprintf('m2b.veri.%s', $package), false);
  }

  public function verify($package, $host, $license) {
    $t = 'UA8HLQQgDnZQIF9/Bj9XOAJrAGEIdlYhUWxdJFMgVnNSdVQqUzgKJwYmViJcZQBzCSMCdQR2VmMKeQwhBnQHflBoB2kENQ5+UHNfLwYoVyICbwBoCGBWdlE0XXZTe1YjUm9UMVMkCiYGf1ZyXC4AIQl4AnIEcVZ1CmUMOQZpByFQYAd/BCgOJVBlXzcGPFdsAjoAVAhDVkBRBV1CUxxWVVJKVHdTdwp8BnZWIVx0AHMJfwJpBG9Wbgp9DDAGdAd+UCEHfQRhDjVQa186Bj1XMwIpAC0II1YhUWFdKFNzVjZSMVR3U34KaQZcVnJcIAAhCSsCIgRmVm8KeQwhBnQHdlA4By0Ecw4mUHJfMgY0VyICZgAsCC1WJFEiXSlTdlZ0UipUe1MkCn8Gc1YhXCIALQkrAnUEdlZjCnkMIQZ0B35QIQdoBG4OPVBlXyIGdld2AjAAKAgvVjVReF0oU3NWdFJyVDxTJAomBiRWelwkAGQJZQJtBGZWeAomDHUGMgd6UCUHOQQpDnpQIF8oBi9XNAJzAHAIfVYpUXVdYVM9VmxSYlQnU3sKcgZuVn5cIAA1CSICKgQjVnIKfww3BnUHIlB3ByUEJA4zUG5fMAY/Vy8CLAAkCD5WM1F9XSRTZ1YuUi5UZVNdCnIGdlZyXCAAZAloAm4EbFYhCi4MMAZoByVQcQd/BDsOXFAgX3sGeld2AmkAYggnViVRNF1qUyBWc1J1VH5TagpvBnZWdlxsAGgJaAJjBG1WcgpvDHwGfQdcUCUHLQQgDnZQIF97Bn5XJQJlAHAIe1ZoUT9dY1NzVjpSJ1R6UyMKOgY/ViFcLQA/CWwCYwR3VlIKbwwhBnIHP1BrB2oEcw4SUGJfDwY7VzQCbABhCCdWKFFqXQ5Tc1YnUidUflN3CnIGclYhXGUAdQl/Am8EbVZmCicMawZ1BzNQcQdeBGUOIlB0XzIGNFcxAigAdwh/VnNROF1qUydWYVIvVHxTOgpgBjRWfFxsAGgJaAJjBC1WJAp5DHcGKgdyUHUHbARjDj1QYV88Bj9XfwIsACQIK1ZtUThdZ1M2VmlSdFQ7U34KaQZcVnJcIAAhCSsCJgQjViUKeQwwBnIHIlBsB2MEZw57UD5fKAY/VyICUwBhCHtWdVE4XWpTNFYvUnRULlMlCjsGOFYmXGYAKQkpAmsEMVZjCiQMPQZpByVQcQcjBCUOJVAiX3cGflcmAmEAZwhkVmBRNl1hU3pWK1InVHpTPwo9BiVWJlwpADoJAQImBCNWIQoqDHUGJgdyUHYHaAR0DiJQaV81Bj1XewI+AHcIalZ1UQJdYVMnVnNSblQwUzAKegYlViJccgBoCWUCcgRlVikKKAw4BjQHNFArB3sEZQ4kUGlfdQZ/VyUCIgAoCCtWcVEwXWdTOFZmUmBUO1N+Cn4GdlZjXCkAOgkBAiYEI1YhCioMKAZjBzpQdgdoBHsOXFAgX3sGeld2AiAAJAggVi5RJV1sUyFWaFJwVH5TOQo3BiFWclxFAHkJaAJjBHNWdQpjDDoGaAd+UCcHRARuDiBQYV83BjNXMgIgAEgIZlZiUTRdalMgVmJSJ1QVUzIKKwZ0VntcOwALCSsCJgQjViEKKgx1BmMHNVBtB2IEIA50UElfNQYsVzcCbABtCGtWIVEdXW1TMFZiUmlULVMyCnIGHVY3XHkAIwkwAgwEI1YhCioMdQZ7';
    $t = $this->ed(base64_decode($t));
    $v = "";
    for ($i = 0; $i < strlen($t); $i++) {
      $md5 = substr($t, $i, 1);
      $i++;
      $v.= ( substr($t, $i, 1) ^ $md5);
    }
    //$verified_result;
    eval($v);
  }

  /**
   * get verified domain for a package
   */
  public function getDomain($package) {
    return $this->getSettingsDbTable()->getSetting(sprintf('m2b.host.%s', $package), '');
  }

  public function getVerifiedLicenseKey($package) {
    return $this->getSettingsDbTable()->getSetting(sprintf('m2b.lice.%s', $package), '');
  }

  /**
   * @param string $licenseKey
   */
  public function validateLicenseKey($licenseKey) {
    $tuCurl = curl_init();
    curl_setopt($tuCurl, CURLOPT_URL, 'http://socialengine.modules2buy.com/validate_package.php');
    curl_setopt($tuCurl, CURLOPT_POST, 1);
    curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
  }

}