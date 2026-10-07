<?php

namespace App\Smark;

/**
 * compute($method, $nums)
 * isEven($num)
 * linearRegression($xValues, $yValues, $result)
 * calculateTotalPrice($items, $discountThreshold, $discountRate, $taxRate)
 * calculateBMI($weight, $height)
 * generateReceiptNumber()
 * factorial($number)
 * fibonacci($n)
 * calculateQuadraticRoots($a, $b, $c)
 * gcd($a, $b)
 * matrixMultiply($matrixA, $matrixB)
 * gaussianElimination($matrix)
 * convertToMoneyFormat($price)
 */

class Math
{
    // Method to perform arithmetic operations based on the given method
    public static function compute($method, $nums): float
    {
        // Check if the method is 'add'
        if ($method === 'add') {
            return array_sum($nums); // Return the sum of all numbers in the array
        }

        // Check if the method is 'minus'
        if ($method === 'minus') {
            // Initialize a variable to hold the result
            $result = $nums[0]; // Start with the first number
            // Iterate through the array starting from the second element
            for ($i = 1; $i < count($nums); $i++) {
                $result -= $nums[$i]; // Subtract each subsequent number from the result
            }
            return $result; // Return the final result
        }

        // Check if the method is 'multiply'
        if ($method === 'multiply') {
            // Initialize a variable to hold the result, starting with 1 (since multiplying by 0 would yield 0)
            $result = 1;
            // Iterate through the array and multiply each element with the current result
            foreach ($nums as $number) {
                $result *= $number; // Multiply each number with the result
            }
            return $result; // Return the final result
        }

        // Check if the method is 'divide'
        if ($method === 'divide') {
            // Initialize a variable to hold the result, starting with the first number
            $result = $nums[0];

            // Iterate through the array starting from the second element
            for ($i = 1; $i < count($nums); $i++) {
                // Check if the current number is not zero before dividing
                if ($nums[$i] != 0) {
                    $result /= $nums[$i]; // Divide the result by the current number
                } else {
                    // Handle division by zero (if necessary for your use case)
                    echo "Error: Division by zero encountered."; // Output an error message
                    break; // Exit the loop
                }
            }
            return $result; // Return the final result
        }
        return 0; // Return 0 if no valid method is found
    }

    // Method to check if a number is even
    public static function isEven($num): bool
    {
        return ($num %2 == 0) ? true : false; // Return true if the number is even, otherwise false
    }

    // Method to perform linear regression
    public static function linearRegression($xValues, $yValues, $result)
    {
        // Calculate number of data points
        $n = count($xValues);

        // Calculate sums
        $sumX = array_sum($xValues); // Sum of x values
        $sumY = array_sum($yValues); // Sum of y values

        // Calculate sum of squares
        $sumXSquared = 0; // Initialize sum of squares of x values
        $sumXY = 0; // Initialize sum of x * y values
        for ($i = 0; $i < $n; $i++) {
            $sumXSquared += ($xValues[$i] * $xValues[$i]); // Add x^2 to the sum
            $sumXY += ($xValues[$i] * $yValues[$i]); // Add x * y to the sum
        }

        // Calculate slope (m)
        $slope = ($n * $sumXY - $sumX * $sumY) / ($n * $sumXSquared - $sumX * $sumX);

        // Calculate y-intercept (b)
        $intercept = ($sumY - $slope * $sumX) / $n;

        // Return the requested result
        if ($result === 'slope') {
            return $slope; // Return the slope
        } elseif ($result === 'intercept') {
            return $intercept; // Return the intercept
        } else {
            // Return an associative array with slope and intercept
            return array(
                'slope' => $slope,
                'intercept' => $intercept
            );
        }
    }

    // Method to calculate the total price of items with discount and tax
    public static function calculateTotalPrice($items, $discountThreshold, $discountRate, $taxRate) {
        $subtotal = 0;
        // Calculate subtotal
        foreach ($items as $item) {
            $subtotal += $item['price'] * $item['quantity']; // Add price * quantity for each item
        }
        // Apply discount if subtotal exceeds the threshold
        if ($subtotal > $discountThreshold) {
            $discount = $subtotal * $discountRate; // Calculate discount
            $subtotal -= $discount; // Subtract discount from subtotal
        }
        // Apply tax
        $tax = $subtotal * $taxRate; // Calculate tax
        $total = $subtotal + $tax; // Calculate total price including tax
        return $total; // Return the total price
    }

    // Method to calculate BMI (Body Mass Index)
    public static function calculateBMI($weight, $height) {
        // Check if weight and height are positive numbers
        if ($weight <= 0 || $height <= 0) {
            return "Weight and height must be positive numbers."; // Return an error message if inputs are invalid
        }
        // Calculate BMI
        $bmi = $weight / ($height * $height); // BMI formula
        // Return the BMI value rounded to two decimal places
        return round($bmi, 2);
    }

    // Method to generate a unique receipt number
    public static function generateReceiptNumber() {
        $prefix = "REC"; // Prefix for the receipt number
        $date = date("Ymd"); // Current date in YYYYMMDD format
        $uniqueId = uniqid(); // Generate a unique identifier

        // Combine the prefix, date, and unique identifier to form the receipt number
        $receiptNumber = $prefix . $date . strtoupper($uniqueId);

        return $receiptNumber; // Return the receipt number
    }

    // Method to calculate factorial of a number
    public static function factorial($number) {
        if ($number < 2) {
            return 1; // Base case: factorial of 0 or 1 is 1
        } else {
            return ($number * self::factorial($number - 1)); // Recursive case
        }
    }

    // Method to calculate Fibonacci sequence number
    public static function fibonacci($n) {
        if ($n <= 1) return $n; // Base cases: Fibonacci(0) = 0, Fibonacci(1) = 1
        return self::fibonacci($n - 1) + self::fibonacci($n - 2); // Recursive case
    }

    // Method to calculate the roots of a quadratic equation
    public static function calculateQuadraticRoots($a, $b, $c) {
        // Check if 'a' is not zero
        if ($a == 0) {
            return "Coefficient 'a' cannot be zero in a quadratic equation."; // Return an error message if 'a' is zero
        }

        // Calculate the discriminant
        $discriminant = ($b * $b) - (4 * $a * $c);

        // Check if the discriminant is positive, zero, or negative
        if ($discriminant > 0) {
            // Two real and distinct roots
            $root1 = (-$b + sqrt($discriminant)) / (2 * $a);
            $root2 = (-$b - sqrt($discriminant)) / (2 * $a);
            return ["root1" => $root1, "root2" => $root2]; // Return both roots
        } elseif ($discriminant == 0) {
            // One real root (double root)
            $root = -$b / (2 * $a);
            return ["root" => $root]; // Return the single root
        } else {
            // Complex roots
            $realPart = -$b / (2 * $a);
            $imaginaryPart = sqrt(-$discriminant) / (2 * $a);
            return [
                "root1" => "$realPart + {$imaginaryPart}i", // Return the first complex root
                "root2" => "$realPart - {$imaginaryPart}i"  // Return the second complex root
            ];
        }
    }

    // Method to calculate the Greatest Common Divisor (GCD) of two numbers
    public static function gcd($a, $b) {
        // Ensure both inputs are non-negative integers
        if ($a < 0 || $b < 0) {
            return "Invalid input. Please enter non-negative integers."; // Return an error message if inputs are invalid
        }

        // Apply the Euclidean algorithm
        while ($b !== 0) {
            $temp = $b; // Store the current value of 'b'
            $b = $a % $b;  // Calculate the remainder of 'a' divided by 'b'
            $a = $temp;     // Update 'a' to be the previous 'b'
        }

        return $a; // 'a' now contains the GCD
    }

    // Method to multiply two matrices
    public static function matrixMultiply($matrixA, $matrixB) {
        // Get the dimensions of the matrices
        $rowsA = count($matrixA);
        $colsA = count($matrixA[0]);
        $rowsB = count($matrixB);
        $colsB = count($matrixB[0]);

        // Check if the matrices can be multiplied
        if ($colsA !== $rowsB) {
            return "Matrix multiplication not possible: Number of columns in Matrix A must equal the number of rows in Matrix B."; // Return an error message if multiplication is not possible
        }

        // Initialize the resulting matrix with zeros
        $result = array_fill(0, $rowsA, array_fill(0, $colsB, 0));

        // Perform matrix multiplication
        for ($i = 0; $i < $rowsA; $i++) {
            for ($j = 0; $j < $colsB; $j++) {
                for ($k = 0; $k < $colsA; $k++) {
                    $result[$i][$j] += $matrixA[$i][$k] * $matrixB[$k][$j]; // Multiply and accumulate results
                }
            }
        }

        return $result; // Return the resulting matrix
    }

    // Method to perform Gaussian Elimination and solve a system of linear equations
    public static function gaussianElimination($matrix) {
        $n = count($matrix); // Number of equations

        // Forward elimination
        for ($i = 0; $i < $n; $i++) {
            // Search for maximum in this column
            $maxRow = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($matrix[$k][$i]) > abs($matrix[$maxRow][$i])) {
                    $maxRow = $k; // Update maxRow if a larger value is found
                }
            }

            // Swap maximum row with current row (pivot)
            $temp = $matrix[$maxRow];
            $matrix[$maxRow] = $matrix[$i];
            $matrix[$i] = $temp;

            // Make the elements below the pivot equal to zero
            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $matrix[$k][$i] / $matrix[$i][$i]; // Calculate the factor to zero out elements
                for ($j = $i; $j < $n + 1; $j++) {
                    $matrix[$k][$j] -= $factor * $matrix[$i][$j]; // Perform elimination
                }
            }
        }

        // Back substitution
        $solutions = array_fill(0, $n, 0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $solutions[$i] = $matrix[$i][$n] / $matrix[$i][$i]; // Calculate solution for current variable
            for ($k = $i - 1; $k >= 0; $k--) {
                $matrix[$k][$n] -= $matrix[$k][$i] * $solutions[$i]; // Update remaining equations
            }
        }

        return $solutions; // Return the solutions of the system
    }

    // Method that converts integer into money format. Ex: 20000 = 20,000.00
    public static function convertToMoneyFormat($price) {
        // Ensure the price is a valid number
        if (!is_numeric($price)) {
            return false; // or handle the error as per your need
        }

        // If there's no decimal part, set it to .00
        if (strpos($price, '.') === false) {
            $price .= '.00';
        }

        // Format the number as money (comma-separated thousands and 2 decimal places)
        $formattedPrice = number_format($price, 2, '.', ',');

        return $formattedPrice;
    }
}
