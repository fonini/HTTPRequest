<?php
require('HTTPRequest.class.php');

$request = new HTTPRequest('http://127.0.0.1');
echo $request->getContent(); // or just echo $request;

echo '<pre>';
print_r($request->getHeader()); // return an array with the response header
echo '</pre>';

