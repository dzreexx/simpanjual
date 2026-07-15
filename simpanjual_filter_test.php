<?php
$ch = curl_init('http://0.0.0.0:8000/salesorder/all?start_date=2026-03-17&end_date=2026-03-17');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo strlen($response) . " bytes\n";
if (strpos($response, 'SO-') !== false) {
    echo "Found SO numbers in response.\n";
} else {
    echo "No SO numbers found (empty result).\n";
}
