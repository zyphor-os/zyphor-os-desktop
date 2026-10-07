<?php

namespace App\Smark;

/**
 * send (
  *     $senderMail = 'your_email@gmail.com';
  *     $senderAppPassword = 'your_app_password';
  *     $setFrom = 'your_email@gmail.com';
  *     $setFromName = 'Your Name';
  *     $receiverEmails = ['recipient1@example.com', 'recipient2@example.com']; // Array of recipients
  *     $replyToEmail = 'replyto@example.com';
  *     $replyToName = 'Reply To Name';
  *     $subject = 'Test Email with Multiple Recipients';
  *     $body = '<p>This is a test email sent to multiple recipients with attachments.</p>';
  *     $attachments = ['path/to/file1.txt', 'path/to/file2.jpg']; // Optional attachments
  *  )

  * sendFromForm (
  *     $senderMail = 'your_email@gmail.com';
  *     $senderAppPassword = 'your_app_password';
  *     $setFrom = 'your_email@gmail.com';
  *     $setFromName = 'Your Name';
  *     $receiverEmails = ['recipient1@example.com', 'recipient2@example.com']; // Array of recipients
  *     $replyToEmail = 'replyto@example.com';
  *     $replyToName = 'Reply To Name';
  *     $subject = 'Test Email with Multiple Recipients';
  *     $body = '<p>This is a test email sent to multiple recipients with attachments.</p>';
  *     $attachments = ['path/to/file1.txt', 'path/to/file2.jpg']; // Optional attachments
  *  )
  *
  * <label for="receiverEmails">Receiver Emails (comma separated):</label>
  * <input type="text" name="receiverEmails" required><br>
  *
  * <label for="attachments">Attachments:</label>
  * <input type="file" name="attachments[]" multiple><br> <!-- Allows multiple file uploads -->
 */

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
    public static function send(
        $senderMail,
        $senderAppPassword,
        $setFrom,
        $setFromName,
        $receiverEmails, // Changed to accept multiple recipients
        $replyToEmail,
        $replyToName,
        $subject,
        $body,
        $attachments = [] // Optional parameter for attachments
    ) {
        // Create an instance of PHPMailer; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $senderMail;                            // SMTP username
            $mail->Password   = $senderAppPassword;                    // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Enable implicit TLS encryption
            $mail->Port       = 465;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom($setFrom, $setFromName);

            // Check if $receiverEmails is an array and add each recipient
            if (is_array($receiverEmails)) {
                foreach ($receiverEmails as $email) {
                    $mail->addAddress($email); // Add a recipient
                }
            } else {
                // If it's not an array, add the single email
                $mail->addAddress($receiverEmails);
            }

            // Add reply-to address
            $mail->addReplyTo($replyToEmail, $replyToName);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            // Attach files if provided
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    // Check if the file exists before adding it
                    if (file_exists($attachment)) {
                        $mail->addAttachment($attachment); // Add attachments
                    } else {
                        throw new Exception("Attachment file does not exist: {$attachment}");
                    }
                }
            }

            // Send the email
            return $mail->send();
        } catch (Exception $e) {
            // Handle errors here (e.g., log the error message)
            return false; // or throw new Exception($mail->ErrorInfo);
        }
    }

    public static function sendFromForm(
        $senderMail,
        $senderAppPassword,
        $setFrom,
        $setFromName,
        $receiverEmails, // Accept multiple recipients
        $replyToEmail,
        $replyToName,
        $subject,
        $body,
        $attachments = [] // Optional parameter for attachments
    ) {
        // Load Composer's autoloader
        // (Assuming the autoload.php file is correctly included elsewhere in your application)

        // Create an instance of PHPMailer; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = $senderMail;                            // SMTP username
            $mail->Password   = $senderAppPassword;                    // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // Enable implicit TLS encryption
            $mail->Port       = 465;                                    // TCP port to connect to

            // Recipients
            $mail->setFrom($setFrom, $setFromName);

            // Check if $receiverEmails is an array and add each recipient
            if (is_array($receiverEmails)) {
                foreach ($receiverEmails as $email) {
                    $mail->addAddress($email); // Add a recipient
                }
            } else {
                // If it's not an array, add the single email
                $mail->addAddress($receiverEmails);
            }

            // Add reply-to address
            $mail->addReplyTo($replyToEmail, $replyToName);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            // Attach files if provided
            if (!empty($attachments['tmp_name'])) {
                foreach ($attachments['tmp_name'] as $key => $tmpName) {
                    // Check if the file was uploaded without errors
                    if (isset($attachments['error'][$key]) && $attachments['error'][$key] === UPLOAD_ERR_OK) {
                        // Use the original name of the file
                        $fileName = $attachments['name'][$key];
                        // Add attachment
                        $mail->addAttachment($tmpName, $fileName); // Add attachments
                    } else {
                        throw new Exception("Error uploading file: " . $attachments['name'][$key]);
                    }
                }
            }

            // Send the email
            return $mail->send();
        } catch (Exception $e) {
            // Handle errors here (e.g., log the error message)
            return false; // or throw new Exception($mail->ErrorInfo);
        }
    }
}
