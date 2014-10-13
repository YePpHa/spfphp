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
  
  function __construct($template) {
    $this->template = $template;
  }
  
  private function renderSPF($content) {
    self::setJSONHeader();
    $response = $content->createSPFResponse();
    
    return json_encode($response);
  }
  
  private function renderHTML($content) {
    return $this->template->render($content);
  }
  
  public function render($content) {
    if (self::isSPFRequest()) {
      echo $this->renderSPF($content);
    } else {
      echo $this->renderHTML($content);
    }
  }
  
  private function renderChunkedSPF($content) {
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
          echo call_user_func($this->chunkedFunctions[$i], $content);
        } else {
          throw new Exception("Function " . $this->chunkedFunctions[$i] . " is not an existing function!");
        }
      } else {
        /* PHP 5.3.0 */
        echo $this->chunkedFunctions[$i]($content);
      }
      
      // Flush
      ob_flush();
      flush();
    }
    
    echo $this->multipart_end;
  }
  
  private function renderChunkedHTML($content) {
    $this->setSPFChunkedHeader();
    
    echo $this->template->render($content);
  }
  
  public function renderChunked($content) {
    if (self::isSPFRequest()) {
      $this->renderChunkedSPF($content);
    } else {
      $this->renderChunkedHTML($content);
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