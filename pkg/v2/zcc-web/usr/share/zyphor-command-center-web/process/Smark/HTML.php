<?php

namespace App\Smark;

/**
 * renderHTML($code)
 * withURL($string)
 * generateQRCode($data)
 * generateBarCode($data)
 * filamentMonths()
 * filamentYears($startYear)
 * readMarkdown()
 * generateBootstrapForm($action, $method, $buttonText, $inputs = [])
 */

use chillerlan\QRCode\QRCode;
use Parsedown;
use Picqer\Barcode\BarcodeGeneratorHTML;
use VStelmakh\UrlHighlight\UrlHighlight;

class HTML
{
    // Renders HTML content using heredoc syntax
    public static function renderHTML($code) {
        // Output the provided HTML code
        echo <<<HTML
            $code
        HTML;
    }

    // Highlights URLs in a string
    public static function withURL($string)
    {
        $urlHighlight = new UrlHighlight(); // Create an instance of UrlHighlight
        return $urlHighlight->highlightUrls($string); // Highlight URLs in the provided string
    }

    // Generates a QR code image
    public static function generateQRCode($data)
    {
        // Render the QR code with the provided data
        $out = (new QRCode())->render($data); // Output is a data URI (base64-encoded image)

        // Return an HTML <img> tag to display the QR code
        return '<img alt="QRCode" src="'.$out.'" />';
    }

    // Generates a barcode image
    public static function generateBarCode($data)
    {
        $generator = new BarcodeGeneratorHTML(); // Create an instance of BarcodeGeneratorHTML
        // Generate the barcode in HTML format (TYPE_CODE_128)
        return $generator->getBarcode($data, $generator::TYPE_CODE_128);
    }

    // Renders a dropdown menu for selecting months
    public static function filamentMonths()
    {
        // Output the HTML for a dropdown menu with months
        echo <<<HTML
            <select id="options" name="options" required class="fi-select-input rounded-md block w-full border border-gray-400 bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:dark:bg-gray-900 [&_option]:bg-white [&_option]:dark:bg-gray-900 ps-3">
                <option value="" disabled selected>Select an option</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        HTML;
    }

    // Renders a dropdown menu for selecting years
    public static function filamentYears($startYear) {
        $currentYear = date("Y"); // Get the current year
        $numberOfYears = $currentYear - $startYear + 1; // Calculate the number of years to display

        $options = ''; // Initialize an empty string for options

        // Generate options for each year from startYear to currentYear
        for ($i = 0; $i < $numberOfYears; $i++) {
            $year = $startYear + $i;
            // Mark the current year as selected
            $selected = ($year == $currentYear) ? ' selected' : '';
            $options .= "<option value=\"$year\"$selected>$year</option>\n";
        }

        // Output the HTML for a dropdown menu with years
        echo <<<HTML
            <select id="options" name="options" required class="fi-select-input rounded-md block w-full border border-gray-400 bg-transparent py-1.5 pe-8 text-base text-gray-950 transition duration-75 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] dark:text-white dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] sm:text-sm sm:leading-6 [&_optgroup]:bg-white [&_optgroup]:dark:bg-gray-900 [&_option]:bg-white [&_option]:dark:bg-gray-900 ps-3">
                <option value="" disabled selected>Select an option</option>
                $options
            </select>
        HTML;
    }

    // Reads markdown to html format.
    public static function readMarkdown($markdownInput) {
        $parsedown = new Parsedown();
        return $parsedown->text($markdownInput);
    }

    public static function generateBootstrapForm($action, $method, $buttonText, $buttonClass, $buttonId, $inputs = []) {
        $formHtml = "<form action=\"" . htmlspecialchars($action) . "\" method=\"" . htmlspecialchars($method) . "\" enctype=\"multipart/form-data\">\n";

        foreach ($inputs as $input) {
            $type = htmlspecialchars($input['type'] ?? 'text');
            $placeholder = htmlspecialchars($input['placeholder'] ?? '');
            $classes = htmlspecialchars(implode(' ', array_map('trim', explode(',', $input['classes'] ?? 'form-control'))));
            $id = htmlspecialchars($input['id'] ?? '');
            $name = htmlspecialchars($input['name'] ?? '');
            $options = $input['options'] ?? []; // For select, radio, checkbox
            $label = htmlspecialchars($input['label'] ?? ucfirst($name));

            $formHtml .= "<div class=\"mb-3\">\n";

            if ($type !== 'hidden' && $type !== 'submit') {
                $formHtml .= "<label for=\"$id\" class=\"form-label\">$label</label>\n";
            }

            switch ($type) {
                case 'select':
                    $formHtml .= "<select class=\"$classes\" id=\"$id\" name=\"$name\">\n";
                    foreach ($options as $value => $text) {
                        $formHtml .= "<option value=\"" . htmlspecialchars($value) . "\">" . htmlspecialchars($text) . "</option>\n";
                    }
                    $formHtml .= "</select>\n";
                    break;

                case 'radio':
                case 'checkbox':
                    foreach ($options as $value => $text) {
                        $formHtml .= <<<HTML
                            <div class="form-check">
                                <input class="form-check-input" type="$type" name="{$name}" id="{$id}_{$value}" value="{$value}">
                                <label class="form-check-label" for="{$id}_{$value}">{$text}</label>
                            </div>\n
                        HTML;
                    }
                    break;

                case 'textarea':
                    $formHtml .= "<textarea class=\"$classes\" id=\"$id\" name=\"$name\" placeholder=\"$placeholder\"></textarea>\n";
                    break;

                default:
                    $formHtml .= "<input type=\"$type\" class=\"$classes\" id=\"$id\" name=\"$name\" placeholder=\"$placeholder\">\n";
                    break;
            }

            $formHtml .= "</div>\n";
        }

        $formHtml .= <<<HTML
                <button type="submit" class="btn btn-primary $buttonClass" id="$buttonId">$buttonText</button>
            </form>
        HTML;

        return $formHtml;

        // Usage

        // echo HTML::generateBootstrapForm(
        //     '/submit.php',
        //     'POST',
        //     [
        //         [
        //             'type' => 'text',
        //             'placeholder' => 'Full Name',
        //             'classes' => 'form-control',
        //             'id' => 'full_name',
        //             'name' => 'full_name',
        //             'label' => 'Your Full Name'
        //         ],
        //         [
        //             'type' => 'email',
        //             'placeholder' => 'Enter email',
        //             'classes' => 'form-control',
        //             'id' => 'email',
        //             'name' => 'email'
        //         ],
        //         [
        //             'type' => 'select',
        //             'id' => 'country',
        //             'name' => 'country',
        //             'classes' => 'form-select',
        //             'options' => [
        //                 'ph' => 'Philippines',
        //                 'us' => 'United States',
        //                 'uk' => 'United Kingdom'
        //             ],
        //             'label' => 'Select Country'
        //         ],
        //         [
        //             'type' => 'radio',
        //             'id' => 'gender',
        //             'name' => 'gender',
        //             'options' => [
        //                 'male' => 'Male',
        //                 'female' => 'Female'
        //             ],
        //             'label' => 'Gender'
        //         ],
        //         [
        //             'type' => 'checkbox',
        //             'id' => 'hobbies',
        //             'name' => 'hobbies[]',
        //             'options' => [
        //                 'reading' => 'Reading',
        //                 'traveling' => 'Traveling'
        //             ],
        //             'label' => 'Hobbies'
        //         ],
        //         [
        //             'type' => 'textarea',
        //             'placeholder' => 'Write your message...',
        //             'id' => 'message',
        //             'name' => 'message',
        //             'classes' => 'form-control',
        //             'label' => 'Message'
        //         ],
        //         [
        //             'type' => 'file',
        //             'id' => 'resume',
        //             'name' => 'resume',
        //             'label' => 'Upload Resume'
        //         ],
        //         [
        //             'type' => 'date',
        //             'id' => 'birth_date',
        //             'name' => 'birth_date',
        //             'label' => 'Birth Date'
        //         ]
        //     ]
        // );

    }

}
