<?php

namespace App\Smark;

/**
 * encrypter($data, $key)
 * decrypter($data, $key)
 */

class Encryption
{
    // Encrypts data using a specified key
    public static function encrypter($data, $key) {
        $cipher = "aes-256-cbc"; // Specify the encryption method (AES-256-CBC)

        // Generate a random initialization vector (IV) based on the cipher method
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        // Encrypt the data using the specified cipher method, key, and IV
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv);

        // Combine the IV and encrypted data, then encode it in base64 for storage
        $encrypted = base64_encode($iv . $encrypted);

        return $encrypted; // Return the base64-encoded encrypted data
    }

    // Decrypts data using a specified key
    public static function decrypter($data, $key) {
        $cipher = "aes-256-cbc"; // Specify the encryption method (AES-256-CBC)

        // Decode the base64-encoded encrypted data
        $data = base64_decode($data);

        // Extract the IV and encrypted data from the decoded data
        $iv_length = openssl_cipher_iv_length($cipher); // Get the length of the IV
        $iv = substr($data, 0, $iv_length); // Extract the IV
        $encrypted = substr($data, $iv_length); // Extract the encrypted data

        // Decrypt the encrypted data using the specified cipher method, key, and IV
        $decrypted = openssl_decrypt($encrypted, $cipher, $key, 0, $iv);

        return $decrypted; // Return the decrypted data
    }
}
