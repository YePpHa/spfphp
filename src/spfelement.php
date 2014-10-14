<?php

/**
* @class SPFElement
**/
class SPFElement {
  /**
  * The HTML of the element.
  *
  * @private
  * @property html
  * @type String
  **/
  private $html = null;
  
  /**
  * The attributes of the element in a key, value pair.
  *
  * @private
  * @property attributes
  * @type Array
  **/
  private $attributes = array();
  
  /**
  * Set the HTML of the element.
  *
  * @method setHTML
  * @param {String} html The HTML of the element.
  **/
  public function setHTML($html) {
    $this->html = $html;
  }
  
  /**
  * Starting the capturing of every output of HTML.
  *
  * @method startHTML
  **/
  public function startHTML() {
    ob_start();
  }
  
  /**
  * Stop the capturing of every output and setting the HTML to the result.
  *
  * @method stopHTML
  **/
  public function stopHTML() {
    $html = ob_get_contents();
    ob_end_clean();
    
    $this->html = $html;
  }
  
  /**
  * Get the HTML of the element.
  *
  * @method getHTML
  * @return {String} Returns the HTML of the element.
  **/
  public function getHTML() {
    return $this->html;
  }
  
  /**
  * Set the attribute with a key, value pair.
  *
  * @method setAttribute
  * @param {String} key The name of the attribute.
  * @param {String} value The value of the attribute.
  **/
  public function setAttribute($key, $value) {
    $this->attributes[$key] = $value;
  }
  
  /**
  * Get an attribute by it's name.
  *
  * @method getAttribute
  * @param {String} key The name of the attribute.
  * @return {String} Returns the value of the attribute.
  **/
  public function getAttribute($key) {
    return $this->attributes[$key];
  }
  
  /**
  * Remove an existing attribute on the element.
  *
  * @method removeAttribute
  * @param {String} key The name of the attribute.
  **/
  public function removeAttribute($key) {
    unset($this->attributes[$key]);
  }
  
  /**
  * Get every attribute on the element.
  *
  * @method getAttributes
  * @return {Array} Returns every attribute on the element in a key, value pair array.
  **/
  public function getAttributes() {
    return $this->attributes;
  }
}

?>