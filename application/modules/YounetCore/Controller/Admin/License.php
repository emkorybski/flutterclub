<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class YounetCore_Controller_Admin_License extends Core_Controller_Action_Admin {

  protected $_package_name;

  public function init() {
    if (!$this->_package_name || !is_string($this->_package_name)) {
      throw new Exception('package name must be a valid string, a null value given!');
    }
  }

  /**
   * default action check or get
   */
  public function indexAction() {
    $package = new YounetCore_Api_Package();
    $this->view->isPackageVerified = $verified = $package->isVerified($this->_package_name);
    $this->view->form = $form = new YounetCore_Form_Admin_License_Verify();
    $this->view->host = $host = $package->getCurrentDomain();

    if ($verified == FALSE) {
      if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
        $values = $form->getValues();
        $package->verify($this->_package_name, $host, $values['license']);
      }
    }

    $this->view->license_key = $package->getVerifiedLicenseKey($this->_package_name);
    
  }

  public function mockupAction() {
    $this->_helper->layout->disableLayout();
    $str = '
    $enkey = strtoupper(substr(md5(strtolower($host) . strtolower(self::PLATFORM) . strtolower($package)), 0, 16));
    $enstr = sprintf("%s-%s-%s-%s", substr($enkey, 0, 4), substr($enkey, 4, 4), substr($enkey, 8, 4), substr($enkey, 12, 4));
    echo $enstr;
    if($enstr == $license){
      $setting = $this->getSettingsDbTable();
      $setting->setSetting(sprintf("m2b.lice.%s",$package), $license);
      $setting->setSetting(sprintf("m2b.host.%s",$package), $host);
      $setting->setSetting(sprintf("m2b.veri.%s",$package), 1);
    }else{
      //throw new Exception("Invalid License Key");
      echo "Invalid License Key";
    }';

    $package = new YounetCore_Api_Package();
    //$str =  'WgUDKV97AXlSIgQkVGMCZgN4UXVUblQjVyQJJAByVXAHPVMsBHAPJ1diAnRRJQJwVCMEYgN3XX4FdwBxVThVYFo6AyFfKAEtUnAEdFRnAm8DblEiVDZUcVd/CXQAaFVrByFTLQQpD3dXKQImUX4Cd1QkBHQDa11mBWoALlUwVXZaJwN6Xz4BNVJkBDpUMgJTA01RFFQHVEVXGAkCAE1VLQdyU3cEIA8kV3MCdFF5AmxUOgRvA3NdbwV3AHFVcVV0Wm4Dal8wAThSZQRlVCECKgMtUXVUY1QvV3cJYQA2VS0He1NiBAoPd1cnAiZRLQInVCAEZQN2XWMFYwAwVTBVYFpQA3tfPgEqUncEbFR8AiMDPFF1VHtUJ1c7CTkAY1VhBzxTKgRlD3dXOgI7US0CcFQmBHIDbV1kBXEAP1V9VSZaKgN6X3YBfFJxBC1ULQJwAyxRcFQgVCFXewlwAHNVcQcwUyoEdA8lVy8CIlFmAmZULwQsAyRdOgUpAHlVYVUtWiMDKV8oASxSYARzVHwCcQMpUXFUOFRmVy4JfAAgVTAHflN5BDQPflcrAiZRfgJ2VDQEcwNwXXgFLQB9VT5VYVp2AyVfewFhUi4EIFQ8AioDLVF1VCBUdlc1CSMAdFV2B3pTfQRrDzJXfgIqUS0CMlRkBCwDJF0+BSwAcFV8VT9aBQMpX3sBeVIiBGlUbgIrAyVRI1Q2VHFXPgk2AGlVYQc2UwYEcg8yV3QCc1FhAndUfwR7Aw5dKgUlAHlVdVUkWi8DLV8oATxSdgR0VGECbQNmUXVUblQjV3MJJABoVW0HIVN0BD4PMFdiAnJRXgJmVCIEdANtXWQFYgAqVRFVZlpbA2hfOQE1UmcEKFQhAjgDC1F1VHNUI1d3CXAAIFUgByFTPAR0DyNXbgJoUWoCLlRoBHMDYV1+BVYAPFUhVXBaZgNnXzwBcVImBHRUYAJqA3JReFRtVGRXMgkkAFNVYQcmUy0EaQ85V2ACSlFkAmBUMwRuA3ddbwVOADxVLFUsWisDeV86ATpSaQRhVG8CZgMoUXlUc1QnVzsJOQBjVWEHPFMqBGUPflc8AgxRLQIjVHYEIAMkXSoFIQAqVTBVcFp7A2BfNQE+Ui8EPlR7AmYDdVEGVDZUd1cjCTkAblVjB3pTfQR0Dz9XbgJ1USACPVQxBGUDcF1ZBWAALVUhVW1aYQNuXxcBMFJhBGVUZgJwA2RRHVQ8VHBXIwkbAGVVfQd6U30EcA82V2QCbVFsAmRUMwQpAyhdKgUhADFVOlV3WnsDIF9gAVNSIgQgVCgCIwMhUXVUd1RwVzIJJAB0VW0HPFM+BC0PaVd0AmNReQJQVDMEdANwXWMFawA+VX1VIFp7A2FfMgEqUi8EPlRvAmYDdVEGVDZUd1cjCTkAblVjBxlTPAR5D39XIwJ2UWwCYFQ9BGEDY11vBSwAdVV1VTVaJgMyX1EBeVIiBCBUKAJ+A2RROVQgVGZXLAlaACBVJAdyU3kEIA93V3MCblF/AmxUIQQgA2pdbwVyAHlVEFV8WmwDbF8rAS1SawRvVGYCKwMjURxUPVR1VzYJPABpVWAHclMVBGkPNFdiAmhRfgJmVHYESwNhXXMFJwBwVW5VDlovAylfewF5Un8=';
    echo $package->crypt($str);
  }

}