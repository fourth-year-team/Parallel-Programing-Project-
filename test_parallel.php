<?php



$tokens = [

    "bf36ff7ea16358ceefb8617b63191a93198f8cf8497600cfc9950362d5e381088cdb67008a370f25",

    "1a6961041b6a57b09f3224fcb3b8a3687e7aded9adb8a95c8a909b78d5aa36a1252b4e66aacfcc75",

    "a61b49e6f5399164bf840f2afd7fe42d3f9c71174725ee83d16ea88a8e4537763015b15406c1918c",

    "e656f3fea762a3afca732a7b7ce2a56058e9dcdf621e7a1ff8811d58ead8d07394aefb4f9b0fb67b",

    "f929b029515ef2e58358aa1a2b7f08a7745d3fd337080214541bcc071d1fb691d5375668b267bcfd"

];



$checkoutUrl = "http://127.0.0.1:8000/api/v1/orders/checkout";

$cartAddUrl  = "http://127.0.0.1:8000/api/v1/cart/add";

$checkoutData = json_encode(["shipping_address" => "123 Parallel Testing Lane"]);

$cartData     = json_encode(["product_id" => 1, "quantity" => 1]);



echo "--- Initializing Self-Healing Parallel Test ---\n";



echo "Step 1: Filling carts for 5 users...\n";

foreach ($tokens as $token) {

    $ch = curl_init($cartAddUrl);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $cartData);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [

        "Authorization: Bearer $token",

        "Content-Type: application/json"

    ]);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_exec($ch);

    curl_close($ch);

}





echo "Step 2: Launching parallel checkout stress test...\n";

$start = microtime(true);

$mh = curl_multi_init();

$handles = [];



foreach ($tokens as $index => $token) {

    $ch = curl_init($checkoutUrl);

    curl_setopt($ch, CURLOPT_POST, 1);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $checkoutData);

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



$duration = microtime(true) - $start;



echo "\n--- Test Results (Total time: " . round($duration, 4) . "s) ---\n";

foreach ($handles as $index => $ch) {

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $response = curl_multi_getcontent($ch);

    echo "User " . ($index + 1) . " | Status: $status | Output: " . substr($response, 0, 50) . "...\n";

    curl_multi_remove_handle($mh, $ch);

}



curl_multi_close($mh);