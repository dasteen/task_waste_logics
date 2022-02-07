<?php

$input = [
    "submit_url" => "https://something/submit.php",
    "auth_url" => "https://home/auth.php"
];
function query($sql) {
// don’t write implementation, imagine it is already done
// runs $sql in your DB
// returns 2-dimensional array of data
}

function secure_key($service_type, $customer_id) {
// don’t write implementation, imagine it is already done
// returns a string
// * the key may contain non-alphanumeric chars
}
function order_submit($url) {
// don’t write implementation, imagine it is already done
// makes a GET http request
// returns invoice_id (integer)
// * may throw an exception on error
}
//
//
// your code here
//
//

$json = "{'json': 'test'}";

echo $json;
return $json;
