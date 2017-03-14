# HTTPRequest #

A simple class that retrieves remote content on servers without cURL and/or other limitations, like allow_url_fopen disabled.


## Usage ##
````php
<?php
require('HTTPRequest.class.php');

$request = new HTTPRequest('http://www.google.com');

// Get the content
echo $request->getContent(); // or just: echo $request;

// Get headers
echo '<pre>';
print_r($request->getHeaders());
echo '</pre>';
````
