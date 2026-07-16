<?php

namespace App\Smark;

/**
 * jsonRead($json_filename)
 * jsonPush($json_filename, $data_to_be_inserted)
 * jsonUnshift($json_filename, $data_to_be_inserted)
 * jsonDelete($json_filename, $data_key_to_be_deleted, $data_value_to_be_deleted)
 * jsonUpdate($json_filename, $data_key_to_be_updated, $data_value_to_be_updated, $key_to_insert_new_updated_data, $new_updated_data)
 * handleError($message)
 */

class JSON
{
    // Reads and decodes JSON data from a file
    public static function jsonRead($json_filename){
        // Check if the file exists
        if(file_exists($json_filename)){
            // Get the file extension
            $check_if_the_file_extension_is_json = explode('.', $json_filename);
            $end_of_the_exploded_json_file = strtolower(end($check_if_the_file_extension_is_json));
            // Ensure the file extension is .json
            if($end_of_the_exploded_json_file != 'json'){
                self::handleError("File extension error. It must be a .json file.");
            } else {
                // Decode and return the JSON content as an associative array
                return json_decode(file_get_contents($json_filename), true);
            }
        } else {
            self::handleError("The file you are attempting to access either does not exist in the specified directory, or there has been an error related to the filename extension, preventing the system from recognizing the file type and subsequently accessing its contents.");
        }
    }

    // Appends data to a JSON file
    public static function jsonPush($json_filename, $data_to_be_inserted){
        // Check if the file exists
        if(file_exists($json_filename)){
            // Get the file extension
            $check_if_the_file_extension_is_json = explode('.', $json_filename);
            $end_of_the_exploded_json_file = strtolower(end($check_if_the_file_extension_is_json));
            // Ensure the file extension is .json
            if($end_of_the_exploded_json_file != 'json'){
                self::handleError("File extension error. It must be a .json file.");
            } else {
                // Check if the JSON file is empty and initialize it if necessary
                if(empty(file_get_contents($json_filename))){
                    file_put_contents($json_filename, '[]');
                }
                // Decode the JSON content, append the new data, and encode it back
                $json_file_decoded = json_decode(file_get_contents($json_filename), true);
                array_push($json_file_decoded, $data_to_be_inserted);
                $json_file_encoded = json_encode($json_file_decoded, JSON_PRETTY_PRINT);
                return file_put_contents($json_filename, $json_file_encoded);
            }
        } else {
            // Handle file not existing scenario
            self::handleError("The file you are attempting to access either does not exist in the specified directory, or there has been an error related to the filename extension, preventing the system from recognizing the file type and subsequently accessing its contents.");
        }
    }

    // Inserts data at the beginning of a JSON file
    public static function jsonUnshift($json_filename, $data_to_be_inserted){
        // Check if the file exists
        if(file_exists($json_filename)){
            // Get the file extension
            $check_if_the_file_extension_is_json = explode('.', $json_filename);
            $end_of_the_exploded_json_file = strtolower(end($check_if_the_file_extension_is_json));
            // Ensure the file extension is .json
            if($end_of_the_exploded_json_file != 'json'){
                self::handleError("File extension error. It must be a .json file.");
            } else {
                // Check if the JSON file is empty and initialize it if necessary
                if(empty(file_get_contents($json_filename))){
                    file_put_contents($json_filename, '[]');
                }
                // Decode the JSON content, prepend the new data, and encode it back
                $json_file_decoded = json_decode(file_get_contents($json_filename), true);
                array_unshift($json_file_decoded, $data_to_be_inserted);
                $json_file_encoded = json_encode($json_file_decoded, JSON_PRETTY_PRINT);
                return file_put_contents($json_filename, $json_file_encoded);
            }
        } else {
            // Handle file not existing scenario
            self::handleError("The file you are attempting to access either does not exist in the specified directory, or there has been an error related to the filename extension, preventing the system from recognizing the file type and subsequently accessing its contents.");
        }
    }

    // Deletes data from a JSON file based on a key-value pair
    public static function jsonDelete($json_filename, $data_key_to_be_deleted, $data_value_to_be_deleted){
        // Check if the file exists
        if(file_exists($json_filename)){
            // Get the file extension
            $check_if_the_file_extension_is_json = explode('.', $json_filename);
            $end_of_the_exploded_json_file = strtolower(end($check_if_the_file_extension_is_json));
            // Ensure the file extension is .json
            if($end_of_the_exploded_json_file != 'json'){
                self::handleError("File extension error. It must be a .json file.");
            } else {
                // Decode the JSON content, remove the matching data, and encode it back
                $json_file_decoded = json_decode(file_get_contents($json_filename), true);
                foreach ($json_file_decoded as $key => $value) {
                    if($value[$data_key_to_be_deleted] === $data_value_to_be_deleted){
                        array_splice($json_file_decoded, $key, 1);
                        $json_file_encoded = json_encode($json_file_decoded, JSON_PRETTY_PRINT);
                        return file_put_contents($json_filename, $json_file_encoded);
                    }
                }
            }
        } else {
            // Handle file not existing scenario
            self::handleError("The file you are attempting to access either does not exist in the specified directory, or there has been an error related to the filename extension, preventing the system from recognizing the file type and subsequently accessing its contents.");
        }
    }

    // Updates data in a JSON file based on a key-value pair
    public static function jsonUpdate($json_filename, $data_key_to_be_updated, $data_value_to_be_updated, $key_to_insert_new_updated_data, $new_updated_data){
        // Check if the file exists
        if(file_exists($json_filename)){
            // Get the file extension
            $check_if_the_file_extension_is_json = explode('.', $json_filename);
            $end_of_the_exploded_json_file = strtolower(end($check_if_the_file_extension_is_json));
            // Ensure the file extension is .json
            if($end_of_the_exploded_json_file != 'json'){
                self::handleError("File extension error. It must be a .json file.");
            } else {
                // Decode the JSON content, update the matching data, and encode it back
                $json_file_decoded = json_decode(file_get_contents($json_filename), true);
                foreach ($json_file_decoded as $key => $value) {
                    if($value[$data_key_to_be_updated] === $data_value_to_be_updated){
                        $json_file_decoded[$key][$key_to_insert_new_updated_data] = $new_updated_data;
                        $json_file_encoded = json_encode($json_file_decoded, JSON_PRETTY_PRINT);
                        return file_put_contents($json_filename, $json_file_encoded);
                    }
                }
            }
        } else {
            // Handle file not existing scenario
            self::handleError("The file you are attempting to access either does not exist in the specified directory, or there has been an error related to the filename extension, preventing the system from recognizing the file type and subsequently accessing its contents.");
        }
    }

    // Handles errors by displaying an error message in HTML format
    private static function handleError($message) {
        // Output an HTML page with the error message
        echo <<<HTML
        <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Error</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f8d7da;
                        color: #721c24;
                        padding: 20px;
                        border: 1px solid #f5c6cb;
                        border-radius: 5px;
                    }
                    h1 {
                        color: #c7254e;
                    }
                </style>
            </head>
                <body>
                    <h1>Error</h1>
                    <p>$message</p>
                </body>
            </html>
        HTML;
        exit; // Stop execution after displaying the error
    }
}
