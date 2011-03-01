# HTTPRequest #

A simple class that retrieves remote content on servers without cURL and/or other limitations, like allow_url_fopen disabled.


## Usage ##

        <?php
        require('HTTPRequest.class.php');
        
        $request = new HTTPRequest('http://www.google.com');
		echo $request->getContent(); // or symply echo $request;

        echo '<pre>';
        print_r($request->getHeaders());
        echo '</pre>';

