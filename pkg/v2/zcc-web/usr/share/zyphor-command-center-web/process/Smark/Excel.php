<?php

namespace App\Smark;

/**
 * downloadExcel($excelArray, $source)
 * downloadExcelAs($filename, $excelArray, $source)
 * _downloadExcel($excelArray, $source)
 * _downloadExcelAs($filename, $excelArray, $source)
 */

use CodexWorld\PhpXlsxGenerator;

class Excel
{
    // Downloads an Excel file with a generated filename
    public static function downloadExcel($excelArray, $source)
    {
        require 'excel/PhpXlsxGenerator.php'; // Include the PhpXlsxGenerator library

        // Initialize an empty array to store associative arrays
        $sourceArray = [];

        // Convert each model object in the source to an associative array
        foreach ($source as $s) {
            $sourceArray[] = $s->toArray(); // Convert the model to an associative array
        }

        // Append the associative arrays from source to the excelArray
        foreach ($sourceArray as $key => $value) {
            array_push($excelArray, $value);
        }

        // Generate an Excel file from the array
        $xlsx = PhpXlsxGenerator::fromArray($excelArray);

        // Generate a unique filename with a .xlsx extension
        $filename = uniqid('', true).'.xlsx';

        // Download the generated Excel file
        return $xlsx->downloadAs($filename);
    }

    // Downloads an Excel file with a specified filename
    public static function downloadExcelAs($filename, $excelArray, $source)
    {
        require 'excel/PhpXlsxGenerator.php'; // Include the PhpXlsxGenerator library

        // Initialize an empty array to store associative arrays
        $sourceArray = [];

        // Convert each model object in the source to an associative array
        foreach ($source as $s) {
            $sourceArray[] = $s->toArray(); // Convert the model to an associative array
        }

        // Append the associative arrays from source to the excelArray
        foreach ($sourceArray as $key => $value) {
            array_push($excelArray, $value);
        }

        // Generate an Excel file from the array
        $xlsx = PhpXlsxGenerator::fromArray($excelArray);

        // Download the generated Excel file with the specified filename
        return $xlsx->downloadAs($filename.'.xlsx');
    }

    // Downloads an Excel file with a generated filename (alternative method)
    public static function _downloadExcel($excelArray, $source)
    {
        require 'excel/PhpXlsxGenerator.php'; // Include the PhpXlsxGenerator library

        // Append the associative arrays from source to the excelArray
        foreach ($source as $key => $value) {
            array_push($excelArray, $value);
        }

        // Generate an Excel file from the array
        $xlsx = PhpXlsxGenerator::fromArray($excelArray);

        // Generate a unique filename with a .xlsx extension
        $filename = uniqid('', true).'.xlsx';

        // Download the generated Excel file
        return $xlsx->downloadAs($filename);
    }

    // Downloads an Excel file with a specified filename (alternative method)
    public static function _downloadExcelAs($filename, $excelArray, $source)
    {
        require 'excel/PhpXlsxGenerator.php'; // Include the PhpXlsxGenerator library

        // Append the associative arrays from source to the excelArray
        foreach ($source as $key => $value) {
            array_push($excelArray, $value);
        }

        // Generate an Excel file from the array
        $xlsx = PhpXlsxGenerator::fromArray($excelArray);

        // Download the generated Excel file with the specified filename
        return $xlsx->downloadAs($filename.'.xlsx');
    }
}

// Define the Excel header (first row)
// $excelArray = [
//     ['ID', 'Name', 'Email', 'Created At']
// ];

// // Get all users from the database
// $users = User::select('id', 'name', 'email', 'created_at')->get();

// // Call your custom Excel generator
// return Excel::downloadExcel($excelArray, $users);
