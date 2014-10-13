<?php
  require_once "simple_html_dom.php";
  
  /**
  * @class SPFTemplate
  **/
  class SPFTemplate {
    public function render($content) {
      
    }
  }
  
  /**
  * @class SPFElement
  **/
  class SPFElement {
    private $html = null;
    private $attributes = array();
    
    public function setHTML($html) {
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
      return $attributes;
    }
  }
  
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
    
    /**
    * Creating a SPF response.
    *
    * @method createSPFResponse
    * @return {Object} Returns the SPF response.
    **/
    public function createSPFResponse() {
      $response = array();
      
      // Stylesheet
      if ($this->stylesheet != null) {
        $response["head"] = $this->stylesheet;
      }
      
      // JavaScript
      if ($this->javascript != null) {
        $response["foot"] = $this->javascript;
      }
      
      // Title
      if ($this->title != null) {
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
        if ($html != null) {
          $body[$id] = $html;
        }
      }
      if (count($body) > 0) {
        $response["body"] = $body;
      }
      
      return $response;
    }
  }
  
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
      $response = $content::createSPFResponse();
      
      return json_encode($response);
    }
    
    private function renderHTML($content) {
      return $this->template->render($content);
    }
    
    public function render($content) {
      if (self::isSPFRequest()) {
        echo self::renderSPF($content);
      } else {
        echo self::renderHTML($content);
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
      self::setSPFChunkedHeader();
      
      echo $this->template->render($content);
    }
    
    public function renderChunked($content) {
      if (self::isSPFRequest()) {
        self::renderChunkedSPF($content);
      } else {
        self::renderChunkedHTML($content);
      }
    }
    
    public function addChunkedFunction($func) {
      array_push($this->chunkedFunctions, $func);
    }
    
    public function redirect($url) {
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
    
    private function getReferer() {
      $headers = self::parseRequestHeader();
      
      if (isset($headers["HTTP_X_SPF_REFERER"])) {
        $referer = $headers["HTTP_X_SPF_REFERER"];
      } else {
        $referer = $headers["HTTP_REFERER"];
      }
      return $referer;
    }
    
    private function isSPFRequest() {
      $headers = self::parseRequestHeader();
      return (isset($_GET["spf"]) || isset($headers["HTTP_X_SPF_REQUEST"]));
    }
    
    private function setJSONHeader() {
      header("Content-Type: application/javascript");
    }
    
    private function setSPFMultipartHeader() {
      header("X-SPF-Response-Type: multipart");
    }
    
    private function setSPFChunkedHeader() {
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