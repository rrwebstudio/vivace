<?php

function send_mail($address, $subject, $body) {

    $to = $address;

    $subject = $subject;

    $headers  = "From: " . strip_tags('noreply@vivace.rrwebstudio.com') . "\r\n";
    $headers .= "Reply-To: " . strip_tags('mike_ricafrente@yahoo.com') . "\r\n";
    $headers .= "CC: rose@rrwebstudio.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $message = $body;

    $send_mail = mail($to, $subject, $message, $headers);

    return $send_mail;

}

function redirect($url) {
    header('Location: '.$url);
    die();
}