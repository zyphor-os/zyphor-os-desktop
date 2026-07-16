<?php

namespace App\Smark;

/**
 * $filename
 * upload($request, $path)
 * removeFile($path)
 *
 * $_filename
 * _upload($filename_input, $file_path, $filename_valid_extension)
 *
 */

class File
{
    public static $filename; // Static property to store the filename of the uploaded file

    // Uploads a file to a specified path
    public static function upload($request, $path)
    {
        // Generate a unique filename with an extension based on the uploaded file's extension
        $filename = uniqid('', true).'.'.$request->extension();

        self::$filename = $filename; // Store the generated filename in the static property
        // Store the file at the specified path with the generated filename
        return $request->storeAs($path, $filename);
    }

    // Removes a file from the specified path
    public static function removeFile($path)
    {
        // Delete the file at the specified path
        return unlink($path);
    }

    public static $_filename; // Static property for an alternative filename

    // Uploads a file with custom validation and handling
    public static function _upload($filename_input, $file_path, $filename_valid_extension)
    {
        // Check if any of the required arguments are empty
        if (empty($filename_input) || empty($file_path) || empty($filename_valid_extension)) {
            echo "Invalid or incomplete argument."; // Output an error message if arguments are missing
        } else {
            // Ensure the file path ends with a '/'
            $check_file_path_if_valid = str_split($file_path);
            if (end($check_file_path_if_valid) != '/') {
                $file_path = $file_path.'/'; // Append '/' to the end of the file path if missing
            }

            // Extract the file extension from the filename input
            $filename_upload_extension = explode('.', $filename_input['name']);
            $filename_upload_extension_lowercase = strtolower(end($filename_upload_extension));

            // Check if there was an error during file upload
            if ($filename_input['error'] === 0) {
                // Validate the file extension
                if (!in_array($filename_upload_extension_lowercase, $filename_valid_extension)) {
                    echo "Invalid file extension"; // Output an error message if the extension is not allowed
                } else {
                    // Generate a new unique filename with the same extension
                    $new_filename_generated = uniqid('file', true).'.'.$filename_upload_extension_lowercase;
                    $new_file_upload_path = $file_path.$new_filename_generated;

                    self::$filename = $new_filename_generated; // Store the new filename in the static property
                    // Move the uploaded file to the new path
                    return move_uploaded_file($filename_input['tmp_name'], $new_file_upload_path);
                }
            } else {
                echo "There was an error uploading the file"; // Output an error message if there was an upload error
            }
        }
    }
}
