<?php

namespace App\Smark;

/**
 * createPaymentLink($secretKey, $amount, $description, $remarks)
 */

class Payment
{
    /**
     * Create a PayMongo payment link
     *
     * @param string $secretKey   Your PayMongo secret key
     * @param float  $amount      Amount in pesos (not centavos)
     * @param string $description Payment description
     * @param string $remarks     Custom remarks (stored as metadata)
     * @return string|false       Checkout URL or false on error
     */
    public static function createPaymentLink($secretKey, $amount, $description, $remarks)
    {
        $amount = floatval($amount) * 100; // convert to centavos
        if ($amount <= 0) {
            die("Invalid amount.");
        }

        $data = [
            "data" => [
                "attributes" => [
                    "amount" => $amount,
                    "currency" => "PHP",
                    "description" => $description,
                    "metadata" => [
                        "remarks" => $remarks
                    ]
                ]
            ]
        ];

        $ch = curl_init("https://api.paymongo.com/v1/links");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Basic " . base64_encode($secretKey . ":")
            ]
        ]);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            die("cURL error: " . curl_error($ch));
        }
        curl_close($ch);

        $response = json_decode($result, true);

        if (isset($response['data']['attributes']['checkout_url'])) {
            return $response['data']['attributes']['checkout_url'];
        } else {
            file_put_contents("paymongo_error.log", print_r($response, true) . "\n", FILE_APPEND);
            return false;
        }
    }
}

// create-payment --------------------------------------------------------------------------------------------

// use Smark\Smark\Payment;

// $secretKey = "";
// $amount = $_POST['amount'] ?? 100; // example
// $description = "Premium Subscription";
// $remarks = "User #1234";

// $checkoutUrl = Payment::createPaymentLink($secretKey, $amount, $description, $remarks);

// if ($checkoutUrl) {
//     header("Location: " . $checkoutUrl);
//     exit();
// } else {
//     echo "Error creating payment link. Check log for details.";
// }

// callback --------------------------------------------------------------------------------------------------

// // Read raw POST body
// $raw = file_get_contents("php://input");

// // Decode JSON
// $data = json_decode($raw, true);

// // Log raw data for debugging
// file_put_contents("webhook_raw.log", $raw . "\n", FILE_APPEND);

// if (!$data || !isset($data['data']['attributes']['type'])) {
//     echo "❌ Invalid webhook payload";
//     exit;
// }

// // Extract event type (like payment.paid)
// $eventType = $data['data']['attributes']['type'];

// if ($eventType === "payment.paid") {
//     // ✅ Payment successful
//     $payment = $data['data']['attributes']['data']['attributes'] ?? [];
//     $amount = ($payment['amount'] ?? 0) / 100;
//     $desc   = $payment['description'] ?? 'No description';

//     // Example: Save to DB or log
//     file_put_contents("payments.log", "✅ Payment success: ₱{$amount} - {$desc}\n", FILE_APPEND);
// } else {
//     // Log other events
//     file_put_contents("payments.log", "ℹ️ Event received: {$eventType}\n", FILE_APPEND);
// }

// http_response_code(200);
// echo "✅ Webhook processed";

// index ---------------------------------------------------------------------------------------------------

// <!DOCTYPE html>
// <html lang="en">
// <head>
//     <meta charset="UTF-8">
//     <meta name="viewport" content="width=device-width, initial-scale=1.0">
//     <title>PayMongo Payment</title>

//     <style>
//         @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500&display=swap');

//          * {
//             margin: 0;
//             padding: 0;
//             font-family: 'Poppins', sans-serif;
//          }

//          body {
//             display: flex;
//             justify-content: center;
//             align-items: center;
//             height: 100vh;
//             background-image: linear-gradient(to right, #43e97b 0%, #38f9d7 100%);
//          }

//          .container {
//             padding: 30px;
//             border-radius: 10px;
//             text-align: center;
//             box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
//             background-color: #fff;
//          }

//          form {
//             display: flex;
//             flex-direction: column;
//          }

//          h2 {
//             font-size: 30px;
//             margin-bottom: 20px;
//          }

//          input {
//             text-align: center;
//             font-size: 20px;
//             padding: 5px;
//             border: none;
//             border-bottom: 1px solid;
//             outline: none;
//          }

//          button {
//             font-size: 18px;
//             padding: 7px;
//             background-color: #009039;
//             border: none;
//             color: #fff;
//             border-radius: 4px;
//             cursor: pointer;
//          }

//          button:hover {
//             background-color: #00bf4c;
//          }
//     </style>
// </head>
// <body>
//     <div class="container">
//         <h2>Complete Your Payment (min. 100)</h2>
//         <form action="create-payment.php" method="POST">
//             <label for="amount">Amount (PHP):</label>
//             <input type="number" name="amount" required><br>
//             <button type="submit">Pay Now</button>
//         </form>
//     </div>
// </body>
// </html>
