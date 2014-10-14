<?php

/**
* @class SPF
**/
class SPF {
  /**
  * Used to correctly format the multipart SPF response.
  *
  * @private
  * @static
  * @property multipart_begin
  * @type String
  **/
  private static $multipart_begin = "[\r\n";
  /**
  * Used to correctly format the multipart SPF response.
  *
  * @private
  * @static
  * @property multipart_delim
  * @type String
  **/
  private static $multipart_delim = ",\r\n";
  /**
  * Used to correctly format the multipart SPF response.
  *
  * @private
  * @static
  * @property multipart_end
  * @type String
  **/
  private static $multipart_end = "]\r\n";
  
  /**
  * The template that will be used to render the page.
  *
  * @private
  * @property template
  * @type SPFTemplate
  **/
  private $template;
  
  /**
  * The chunked functions that will be called when the chunked page needs to be rendered.
  * These functions will then return their chunk in either HTML or a SPF response.
  *
  * @private
  * @property chunkedFunctions
  * @type Array
  **/
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
  
  /**
  * Render the chunked SPF responses and send them each when they are ready.
  *
  * @private
  * @method renderChunkedSPF
  * @param {SPFPage} page The page that will be rendered
  **/
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
  
  /**
  * Render the chunked HTML page.
  *
  * @private
  * @method renderChunkedHTML
  * @param {SPFPage} page The page that will be rendered.
  **/
  private function renderChunkedHTML($page) {
    $this->setSPFChunkedHeader();
    
    echo $this->template->render($page);
  }
  
  /**
  * Render the chunked page.
  *
  * @method renderChunked
  * @param {SPFPage} page The page that will be rendered.
  **/
  public function renderChunked($page) {
    if (self::isSPFRequest()) {
      $this->renderChunkedSPF($page);
    } else {
      $this->renderChunkedHTML($page);
    }
  }
  
  /**
  * Add a function to the queue that will be called when the page is rendered
  *
  * @method addChunkedFunction
  * @param {String|Function} func The function that will be called.
  **/
  public function addChunkedFunction($func) {
    array_push($this->chunkedFunctions, $func);
  }
  
  /**
  * Redirect the page to another page.
  *
  * @static
  * @method redirect
  * @param {String} url The url of the new page.
  **/
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
  
  /**
  * Get the referer.
  *
  * @private
  * @static
  * @method getReferer
  * @return {String} Returns the referer.
  **/
  private static function getReferer() {
    $headers = self::parseRequestHeader();
    
    if (isset($headers["HTTP_X_SPF_REFERER"])) {
      $referer = $headers["HTTP_X_SPF_REFERER"];
    } else {
      $referer = $headers["HTTP_REFERER"];
    }
    return $referer;
  }
  
  /**
  * Checking if the client has requested the page as SPF.
  *
  * @private
  * @static
  * @method isSPFRequest
  * @return {Boolean} Returns true if the client requested SPF or false if not.
  **/
  private static function isSPFRequest() {
    $headers = self::parseRequestHeader();
    return (isset($_GET["spf"]) || isset($headers["HTTP_X_SPF_REQUEST"]));
  }
  
  /**
  * Set the content type of the page to JSON.
  *
  * @private
  * @static
  * @method setJSONHeader
  **/
  private static function setJSONHeader() {
    header("Content-Type: application/javascript");
  }
  
  /**
  * Set the SPF response type to multipart.
  *
  * @private
  * @static
  * @method setSPFMultipartHeader
  **/
  private static function setSPFMultipartHeader() {
    header("X-SPF-Response-Type: multipart");
  }
  
  /**
  * Set the transfer encoding to chunked.
  *
  * @private
  * @static
  * @method setSPFChunkedHeader
  **/
  private static function setSPFChunkedHeader() {
    header("Transfer-Encoding: chunked");
  }
  
  /**
  * Parse the header of the client request.
  *
  * @private
  * @static
  * @method parseRequestHeader
  * @return {Object} Returns key, value pairs where the key is the name of the header entry and the value is the value of said header entry.
  **/
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