<?php
// mail_helper.php
// Utility for sending emails using PHPMailer and SMTP

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Use Composer autoloader for PHPMailer
require __DIR__ . '/vendor/autoload.php';

function send_email($to, $to_name, $subject, $body_html, $body_text = '') {
    $mail = new PHPMailer(true);
    try {
        // SMTP config (production ready)
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'baifempetuel0.2@gmail.com';
        $mail->Password = 'mceq hojx joal awrx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('admin@example.com', 'Admin');
        $mail->addAddress($to, $to_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body_html;
        $mail->AltBody = $body_text ?: strip_tags($body_html);

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optionally log error: $mail->ErrorInfo
        return false;
    }
}
