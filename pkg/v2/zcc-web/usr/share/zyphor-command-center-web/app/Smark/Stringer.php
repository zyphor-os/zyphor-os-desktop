<?php

namespace App\Smark;

/**
 * toCamelCase($string)
 * truncateString($string, $length)
 * sanitizeInput($input)
 * generateSlug($string)
 * -- update May 15, 2025
 * snakeToCamelCase($string)
 * camelToSnakeCase($string)
 * contains($haystack, $needle)
 * startsWith($string, $start)
 * endsWith($string, $end)
 * limitWords($string, $limit)
 * removeWhitespace($string)
 * onlyAlphanumeric($string)
 * maskString($string, $visibleStart = 2, $visibleEnd = 2)
 * randomString($length = 10)
 * isPalindrome($string)
 * removeAccents($string)
 * titleCase($string)
 * wordCount($string)
 */

class Stringer
{
    // Convert a string to CamelCase
    public static function toCamelCase($string) {
        // Convert the entire string to lowercase
        $result = strtolower($string);

        // Use regular expression to find all alphanumeric sequences
        preg_match_all('/[a-zA-Z0-9]+/', $result, $matches);

        // Initialize result string for CamelCase
        $result = '';
        // Iterate through each matched sequence
        foreach ($matches[0] as $match) {
            // Capitalize the first letter of each sequence and append to result
            $result .= ucfirst($match);
        }
        // Return the result in CamelCase format (lowercase first letter)
        return lcfirst($result);
    }

    // Truncate a string to a specified length, appending '...' if truncated
    public static function truncateString($string, $length) {
        // Check if the string length exceeds the specified length
        if (strlen($string) > $length) {
            // Return the truncated string with '...' appended
            return substr($string, 0, $length) . '...';
        }
        // Return the original string if not truncated
        return $string;
    }

    // Sanitize input by removing HTML tags and converting special characters
    public static function sanitizeInput($input) {
        // Convert special characters to HTML entities and remove HTML tags
        return htmlspecialchars(strip_tags($input));
    }

    // Generate a URL-friendly slug from a string
    public static function generateSlug($string) {
        // Convert string to lowercase, trim whitespace, and replace non-alphanumeric characters with dashes
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }

    // Convert snake_case to camelCase
    public static function snakeToCamelCase($string) {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    // Convert camelCase to snake_case
    public static function camelToSnakeCase($string) {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }

    // Check if a string contains a specific substring
    public static function contains($haystack, $needle) {
        return strpos($haystack, $needle) !== false;
    }

    // Check if a string starts with a specific substring
    public static function startsWith($string, $start) {
        return strncmp($string, $start, strlen($start)) === 0;
    }

    // Check if a string ends with a specific substring
    public static function endsWith($string, $end) {
        return substr($string, -strlen($end)) === $end;
    }

    // Limit a string to a specified number of words
    public static function limitWords($string, $limit) {
        $words = explode(' ', $string);
        return implode(' ', array_slice($words, 0, $limit)) . (count($words) > $limit ? '...' : '');
    }

    // Remove all whitespace characters from a string
    public static function removeWhitespace($string) {
        return preg_replace('/\s+/', '', $string);
    }

    // Remove all characters except letters and numbers
    public static function onlyAlphanumeric($string) {
        return preg_replace('/[^A-Za-z0-9]/', '', $string);
    }

    // Reverse the characters in a string
    public static function reverseString($string) {
        return strrev($string);
    }

    // Mask the middle part of a string with asterisks
    public static function maskString($string, $visibleStart = 2, $visibleEnd = 2) {
        $length = strlen($string);
        if ($length <= $visibleStart + $visibleEnd) {
            return str_repeat('*', $length);
        }
        $maskedLength = $length - ($visibleStart + $visibleEnd);
        return substr($string, 0, $visibleStart) . str_repeat('*', $maskedLength) . substr($string, -$visibleEnd);
    }

    // Generate a random alphanumeric string of given length
    public static function randomString($length = 10) {
        return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
    }

    // Check if a string is a palindrome
    public static function isPalindrome($string) {
        $cleaned = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $string));
        return $cleaned === strrev($cleaned);
    }

    // Remove accents/diacritical marks from characters
    public static function removeAccents($string) {
        return iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
    }

    // Convert a string to Title Case
    public static function titleCase($string) {
        return ucwords(strtolower($string));
    }

    // Count number of words in a string
    public static function wordCount($string) {
        return str_word_count(strip_tags($string));
    }
}
