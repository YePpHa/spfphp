<?php

require_once "../build/spf.php";

// Create a template
$template = new SPFTemplate();

// Create a page
$page = new SPFPage();

// Give the page a title
$page->setTitle("My SPF page");

// Add an element
$element = new SPFElement();

// Give said element an attribute
$element->setAttribute("class", "my-element");

// Change the content of the element
$element->setHTML("This is my very own page");

// Add the element to the page
$page->addElement($element);

// Create an instance of SPF
$spf = new SPF($template);

// Render the page
$spf->render($page);

?>