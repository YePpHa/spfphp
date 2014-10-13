<?php

/**
* @class SPFTemplate
**/
class SPFTemplate {
  private $html = array();
  private $flags = array();
  
  public function start() {
    $this->html = array();
    $this->flags = array();
    
    ob_start();
  }
  
  public function insertFlag($flag) {
    $html = ob_get_contents();
    ob_clean();
    
    array_push($this->html, $html);
    array_push($this->flags, $flag);
  }
  
  public function stop() {
    $html = ob_get_contents();
    ob_end_clean();
    
    array_push($this->html, $html);
  }
  
  public function render($content) {
    $html = "";
    
    $len = count($this->html);
    $flagsLen = count($this->flags);
    
    for ($i = 0; $i < $len; $i++) {
      $html .= $this->html[$i];
      if ($i < $flagsLen) {
        $flag = $this->flags[$i];
        $html .= self::getFlag($flag, $content);
      }
    }
    
    return $html;
  }
  
  private static $htmlFlag = "#";
  private function getFlag($flag, $content) {
    if ($flag === "title") {
      $title = $content->getTitle();
      if ($title !== null) {
        return $title;
      }
    } else if ($flag === "javascript") {
      $js = $content->getJavaScript();
      if ($js !== null) {
        return $js;
      }
    } else if ($flag === "stylesheet") {
      $stylesheet = $content->getStyleSheet();
      if ($stylesheet !== null) {
        return $stylesheet;
      }
    } else if (self::startsWith($flag, self::$htmlFlag)) {
      $id = substr($flag, strlen(self::$htmlFlag));
      $element = $content->getElementById($id);
      
      $html = $element->getHTML();
      if ($html !== null) {
        return $html;
      }
    }
    return "";
  }
  
  private static function startsWith($haystack, $needle) {
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
  }
}

?>