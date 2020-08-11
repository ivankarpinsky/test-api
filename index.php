<?php

define("BASE_URL", "http://api.cpanomer1.affise.com");

function api_request($resource) {
    $full_url = BASE_URL . "$resource";

    $options = array(
        CURLOPT_URL => $full_url,
        CURLOPT_HTTPHEADER => array('API-Key: e60a98867d363b0d43b9e7c58ec498ed'),
        CURLOPT_RETURNTRANSFER => true,
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);

    $content = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ( $status > 399 ) {
        throw new Exception("Exception $status: $content");
    }

    return json_decode($content, true);
}

$offers = api_request('/3.0/partner/offers', 'GET');
$offers = $offers['offers'];

foreach($offers as $offer) {
    $conversions = api_request("/3.0/stats/conversions?offer={$offer['id']}", 'GET');
    $conversions = $conversions['conversions'];

    $countries = implode(',', $offer['countries']);
    echo "Countries: {$countries}<br>";

    if (!$conversions) {
        echo "No conversions<br>";
    } else
    foreach ($conversions as $conversion) {
        if ($conversion['offer_id']==$offer['id']) {
            echo "From: {$conversion['country']}<br>ClickID: {$conversion['clickid']}<br>";
        }
    }

}