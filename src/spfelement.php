<?php

/**
* @class SPFElement
**/
class SPFElement {
  private $html = null;
  private $attributes = array();
  
  public function setHTML($html) {
    $this->html = $html;
  }
  
  public function startHTML() {
    ob_start();
  }
  
  public function stopHTML() {
    $html = ob_get_contents();
    ob_end_clean();
    
    $this->html = $html;
  }
  
  public function getHTML() {
    return $this->html;
  }
  
  public function setAttribute($key, $value) {
    $this->attributes[$key] = $value;
  }
  
  public function getAttribute($key) {
    return $this->attributes[$key];
  }
  
  public function removeAttribute($key) {
    unset($this->attributes[$key]);
  }
  
  public function getAttributes() {
    return $this->attributes;
  }
}

?>