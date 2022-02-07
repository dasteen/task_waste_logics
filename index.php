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

function submit_url($order, $input) {
    $final_params = [
        "order_id" => $order['order_id'],
        "prices" => $order['prices'],
        "amount" => $order['amount'],
    ];

    $auth_params['order_id'] = $order['order_id'];
    $auth_params['secure_key'] = secure_key($order['service_id'], $order['customer_id']);
    $final_params['auth_url'] = $input['auth_url'] .'?'. http_build_query($auth_params);

    return $input['submit_url'] .'?'. http_build_query($final_params);
}

$query = "
    SELECT o.order_id, o.service_id, o.customer_id, 
           ARRAY_TO_STRING(ARRAY_AGG(DISTINCT oc.price_entity_id ORDER BY oc.price_entity_id ASC), ',') AS prices, 
           SUM(oc.value) AS amount
    FROM orders AS o
    JOIN order_charges AS oc ON o.order_id = oc.order_id
    GROUP BY o.order_id, o.service_id, o.customer_id
    ORDER BY o.order_id
    LIMIT 3
    ";
$orders = query($query);

$services = [];
foreach ($orders as $order) {
    $service_id = $order['service_id'];
    $services[$service_id]['sum'] += $order['amount'];

    $final_url = submit_url($order, $input);
    try {
        $invoice_id = order_submit($final_url);
        $services[$service_id]['invoice_id'][] = $invoice_id;
    }
    catch (Exception $e) {
        $services[$service_id]['has_error'] = true;
    }
    $services[$service_id]['invoice_id'] = $services[$service_id]['invoice_id'] ?: [];
    $services[$service_id]['has_error'] = $services[$service_id]['has_error'] ?: false;
}

ksort($services);
foreach ($services as &$service) {
    sort($service['invoice_id']);
}

$json = json_encode($services);
return $json;
