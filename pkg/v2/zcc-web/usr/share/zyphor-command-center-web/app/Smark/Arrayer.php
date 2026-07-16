<?php

namespace App\Smark;

/**
 * flattenArray($array)
 * uniqueMultidimensionalArray($array, $key)
 */

class Arrayer
{
    // Flattens a multidimensional array into a single-dimensional array
    public static function flattenArray($array) {
        $result = []; // Initialize an empty array to store the flattened values

        // Use array_walk_recursive to iterate through each element of the array
        array_walk_recursive($array, function($a) use (&$result) {
            $result[] = $a; // Append each value to the result array
        });

        return $result; // Return the flattened array
    }

    // Removes duplicates from a multidimensional array based on a specific key
    public static function uniqueMultidimensionalArray($array, $key) {
        $temp_array = []; // Initialize an empty array to store unique elements
        $i = 0; // Initialize an index counter
        $key_array = []; // Initialize an empty array to track seen keys

        // Iterate through each element of the array
        foreach ($array as $val) {
            // Check if the value of the specified key has not been encountered
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key]; // Add the key to the seen keys array
                $temp_array[$i] = $val; // Add the unique element to the temporary array
            }
            $i++; // Increment the index counter
        }

        return $temp_array; // Return the array with unique elements
    }
}
