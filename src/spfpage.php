<?php

/**
* @class SPFPage
**/
class SPFPage {
  /**
  * The title of the page.
  *
  * @private
  * @property title
  * @type String
  **/
  private $title = null;
  
  /**
  * The elements on the page.
  *
  * @private
  * @property elements
  * @type SPFElement[]
  **/
  private $elements = array();
  
  private $stylesheet = null; // Bottom of the head node
  private $javascript = null; // Bottom of the body node
  
  /**
  * Set the title of the page.
  *
  * @method setTitle
  * @param {String} title The title of the page.
  **/
  public function setTitle($title) {
    $this->title = $title;
  }
  
  /**
  * Add an element to the page.
  *
  * @method setElement
  * @param {String} id The ID of the element.
  * @param {SPFElement} element The element instance.
  **/
  public function setElement($id, $element) {
    $this->elements[$id] = $element;
  }
  
  /**
  * Get the title of the page.
  *
  * @method getTitle
  * @return {String} Returns the title.
  **/
  public function getTitle() {
    return $this->title;
  }
  
  /**
  * Get an element by its ID
  *
  * @method getElementById
  * @param {String} id The ID of the element.
  * @return {SPFElement} Returns the element with the given ID.
  **/
  public function getElementById($id) {
    return $this->elements[$id];
  }
  
  /**
  * Get the style sheet for the page.
  *
  * @method getStyleSheet
  * @return {String} Returns the style sheet as HTML.
  **/
  public function getStyleSheet() {
    return $this->stylesheet;
  }
  
  /**
  * Get the JavaScript for the page.
  *
  * @method getJavaScript
  * @return {String} Returns the JavaScript as HTML.
  **/
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