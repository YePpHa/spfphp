<?php

/**
* @class SPF
**/
class SPF {
  private static $multipart_begin = "[\r\n";
  private static $multipart_delim = ",\r\n";
  private static $multipart_end = "]\r\n";
  
  private $template;
  
  private $chunkedFunctions = array();
  
  /**
  * @constructor
  * @param {SPFTemplate} template The template that the rendering will use.
  **/
  function __construct($template) {
    $this->template = $template;
  }
  
  /**
  * Render the SPF response in a JSON string.
  *
  * @private
  * @method renderSPF
  * @param {SPFPage} page The page that the SPF response should be generated from.
  * @return {String} Returns a JSON string which is the SPF response.
  **/
  private function renderSPF($page) {
    self::setJSONHeader();
    $response = $page->createSPFResponse();
    
    return json_encode($response);
  }
  
  /**
  * Render the page as HTML.
  *
  * @private
  * @method renderHTML
  * @param {SPFPage} page The page that will be rendered.
  * @return {String} Returns the rendered HTML.
  **/
  private function renderHTML($page) {
    return $this->template->render($page);
  }
  
  /**
  * Render the page as requested and output it.
  *
  * @method render
  * @param {SPFPage} page The oage that will be rendered.
  **/
  public function render($page) {
    if (self::isSPFRequest()) {
      echo $this->renderSPF($page);
    } else {
      echo $this->renderHTML($page);
    }
  }
  
  private function renderChunkedSPF($page) {
    self::setSPFChunkedHeader();
    self::setJSONHeader();
    self::setSPFMultipartHeader();
    
    
    echo $this->multipart_begin;
    
    for ($i = 0; i < count($this->chunkedFunctions); $i++) {
      if ($i > 0) {
        echo $this->multipart_delim;
      }
      if (is_string($this->chunkedFunctions[$i])) {
        if (function_exists($this->chunkedFunctions[$i])) {
          echo call_user_func($this->chunkedFunctions[$i], $page);
        } else {
          throw new Exception("Function " . $this->chunkedFunctions[$i] . " is not an existing function!");
        }
      } else {
        /* PHP 5.3.0 */
        echo $this->chunkedFunctions[$i]($page);
      }
      
      // Flush
      ob_flush();
      flush();
    }
    
    echo $this->multipart_end;
  }
  
  private function renderChunkedHTML($page) {
    $this->setSPFChunkedHeader();
    
    echo $this->template->render($page);
  }
  
  public function renderChunked($page) {
    if (self::isSPFRequest()) {
      $this->renderChunkedSPF($page);
    } else {
      $this->renderChunkedHTML($page);
    }
  }
  
  public function addChunkedFunction($func) {
    array_push($this->chunkedFunctions, $func);
  }
  
  public static function redirect($url) {
    if (self::isSPFRequest()) {
      self::setJSONHeader();
      $response = array(
        "redirect" => $url
      );
      echo json_encode($response);
    } else {
      header("Location: " . $url);
    }
    exit;
  }
  
  private static function getReferer() {
    $headers = self::parseRequestHeader();
    
    if (isset($headers["HTTP_X_SPF_REFERER"])) {
      $referer = $headers["HTTP_X_SPF_REFERER"];
    } else {
      $referer = $headers["HTTP_REFERER"];
    }
    return $referer;
  }
  
  private static function isSPFRequest() {
    $headers = self::parseRequestHeader();
    return (isset($_GET["spf"]) || isset($headers["HTTP_X_SPF_REQUEST"]));
  }
  
  private static function setJSONHeader() {
    header("Content-Type: application/javascript");
  }
  
  private static function setSPFMultipartHeader() {
    header("X-SPF-Response-Type: multipart");
  }
  
  private static function setSPFChunkedHeader() {
    header("Transfer-Encoding: chunked");
  }
  
  private static function parseRequestHeader() {
    $headers = array();
    foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) <> "HTTP_") {
        continue;
      }
      $header = str_replace(" ", "-", ucwords(str_replace("_", " ", strtolower(substr($key, 5)))));
      $headers[$header] = $value;
    }
    return $headers;
  }
}

?>