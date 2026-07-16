<?php

namespace App\Smark;

/**
 * send($pageAccessToken, $recipientId, $message)
 */

class Messenger {
    public static function send($pageAccessToken, $recipientId, $message)
    {
        // Messenger API credentials
        $pageAccessToken = $pageAccessToken;
        $recipientId = $recipientId; // Your PSID or user ID

        // Message payload
        $messageData = [
            'recipient' => [ 'id' => $recipientId ],
            'message' => [ 'text' => $message ]
        ];

        // Initialize cURL
        $ch = curl_init('https://graph.facebook.com/v19.0/me/messages?access_token=' . $pageAccessToken);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($messageData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute request
        $response = curl_exec($ch);
        curl_close($ch);
    }
}

// $reciepients = [
//     '25088341xxxxxxxxx',
//     '244154675xxxxxxxx',
// ];

// foreach ($reciepients as $key => $reciepient) {
//     Messenger::send(
//         'xxxxxx',
//         $reciepient,
//         'xxxxxx'
//     );

//     // Optional: short delay to avoid rate limits
//     usleep(500000); // 0.5s delay
// }
