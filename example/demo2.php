<?php

require_once "../build/spf.php";

require_once "template.php";

// Create a page
$page = new SPFPage();

// Give the page a title
$page->setTitle("Page 2 of awesomeness");

// Add an element
$element = new SPFElement();

// Give said element an attribute
$element->setAttribute("class", "my-element-2");

// Change the content of the element
$element->setHTML("This is my very own page #2");

// Add the element to the page
$page->setElement("content", $element);

// Create an instance of SPF
$spf = new SPF($template);

// Render the page
$spf->render($page);

?>