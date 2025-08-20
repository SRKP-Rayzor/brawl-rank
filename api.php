<?php
include 'config.php';

function callBrawlAPI($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . BRAWL_API_TOKEN
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (!$response) return null;
    return json_decode($response, true);
}
?>