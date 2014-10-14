<?php

/**
* @class SPFTemplate
**/
class SPFTemplate {
  /**
  * An array with the HTML parts.
  *
  * @private
  * @property html
  * @type String[]
  **/
  private $html = array();
  /**
  * An array with the flags.
  *
  * @private
  * @property flags
  * @type String[]
  **/
  private $flags = array();
  
  /**
  * The elements on the page.
  *
  * @private
  * @property elements
  * @type SPFElement[]
  **/
  private $elements = array();
  
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
  * Get an element by its ID
  *
  * @method getElementById
  * @param {String} id The ID of the element.
  * @return {SPFElement} Returns the element with the given ID.
  **/
  public function getElementById($id) {
    if (isset($this->elements[$id])) {
      return $this->elements[$id];
    } else {
      return null;
    }
  }
  
  /**
  * Get every element from template.
  *
  * @method getElements
  * @return {SPFElement[]} Returns elements.
  **/
  public function getElements() {
    return $this->elements;
  }
  
  /**
  * Start the template by outputting HTML (print or echo).
  *
  * @method start
  **/
  public function start() {
    $this->html = array();
    $this->flags = array();
    
    ob_start();
  }
  
  /**
  * Insert a flag while creating the template. The flag can be
  *   `title` The title of the page
  *   `stylesheet` The style sheets that will be added at the bottom of the head.
  *   `javascript` The javascript that will be added at the bottom of the body.
  *   `#{ID}` The place where the element with the {ID} will put it's HTML.
  **/
  public function insertFlag($flag) {
    $html = ob_get_contents();
    ob_end_clean();
    ob_start();
    
    array_push($this->html, $html);
    array_push($this->flags, $flag);
  }
  
  
  /**
  * Finish creating the template.
  *
  * @method stop
  **/
  public function stop() {
    $html = ob_get_contents();
    ob_end_clean();
    
    array_push($this->html, $html);
  }
  
  /**
  * Render the page in full HTML with the given page.
  *
  * @method render
  * @param {SPFPage} page The page that will be rendered in HTML.
  * @return {String} Returns the HTML of the rendered page.
  **/
  public function render($page) {
    $html = "";
    
    $len = count($this->html);
    $flagsLen = count($this->flags);
    
    for ($i = 0; $i < $len; $i++) {
      $html .= $this->html[$i];
      if ($i < $flagsLen) {
        $flag = $this->flags[$i];
        $html .= self::getFlag($flag, $page);
      }
    }
    
    return $html;
  }
  
  /**
  * The element ID prefix.
  *
  * @private
  * @static
  * @property htmlFlag
  * @type String
  **/
  private static $htmlFlag = "#";
  /**
  * Get the flag content.
  *
  * @priate
  * @method getFlag
  * @param {String} flag The name of the flag.
  * @param {SPFPage} page The page that will be used to get the content of the flag.
  * @return {String} Returns the flag content.
  **/
  private function getFlag($flag, $page) {
    if ($flag === "title") {
      $title = $page->getTitle();
      if ($title !== null) {
        return $title;
      }
    } else if ($flag === "javascript") {
      $js = $page->getJavaScript();
      if ($js !== null) {
        return $js;
      }
    } else if ($flag === "stylesheet") {
      $stylesheet = $page->getStyleSheet();
      if ($stylesheet !== null) {
        return $stylesheet;
      }
    } else if (self::startsWith($flag, self::$htmlFlag)) {
      $id = substr($flag, strlen(self::$htmlFlag));
      $element = $page->getElementById($id);
      
      if ($element === null) {
        $element = $this->getElementById($id);
      }
      
      $html = $element->getHTML();
      if ($html !== null) {
        return $html;
      }
    }
    return "";
  }
  
  /**
  * Check if a string starts with a specific string
  *
  * @private
  * @static
  * @method startsWith
  * @param {String} haystack The haystack that will be used to check if it starts with needle.
  * @param {String} needle The needle that will be checked if it's in the start of the haystack.
  * @return {Boolean} Returns true of the needle is in the start of the haystack and false if not.
  **/
  private static function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
}

?>