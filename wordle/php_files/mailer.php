<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../../vendor/autoload.php";

function initMailer() {
    $mail = new PHPMailer(true);

    // Set up SMTP
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = 'smtp.gmail.com';  // Use Gmail SMTP server
    $mail->Username = 'joeabihabib123@gmail.com';  // Your Gmail address
    $mail->Password = 'kuxr jnpd rntu olic';  // Your Gmail app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->isHtml(true);

    return $mail;
}

