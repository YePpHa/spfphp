<?php

/**
* @class SPFPage
**/
class SPFPage {
  private $title = null;
  
  private $elements = array();
  
  private $stylesheet = null; // Bottom of the head node
  private $javascript = null; // Bottom of the body node
  
  public function setTitle($title) {
    $this->title = $title;
  }
  
  public function setElement($id, $element) {
    $this->elements[$id] = $element;
  }
  
  public function getTitle() {
    return $this->title;
  }
  
  public function getElementById($id) {
    return $this->elements[$id];
  }
  
  public function getStyleSheet() {
    return $this->stylesheet;
  }
  
  public function getJavaScript() {
    return $this->javascript;
  }
  
  /**
  * Creating a SPF response.
  *
  * @method createSPFResponse
  * @return {Object} Returns the SPF response.
  **/
  public function createSPFResponse() {
    $response = array();
    
    // Stylesheet
    if ($this->stylesheet !== null) {
      $response["head"] = $this->stylesheet;
    }
    
    // JavaScript
    if ($this->javascript !== null) {
      $response["foot"] = $this->javascript;
    }
    
    // Title
    if ($this->title !== null) {
      $response["title"] = $this->title;
    }
    
    // Attributes
    $attr = array();
    foreach ($this->elements as $id => $element) {
      $attributes = $element->getAttributes();
      if (count($attributes) > 0) {
        $attr[$id] = $attributes;
      }
    }
    if (count($attr) > 0) {
      $response["attr"] = $attr;
    }
    
    // Body content
    $body = array();
    foreach ($this->elements as $id => $element) {
      $html = $element->getHTML();
      if ($html !== null) {
        $body[$id] = $html;
      }
    }
    if (count($body) > 0) {
      $response["body"] = $body;
    }
    
    return $response;
  }
}

?>