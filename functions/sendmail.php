<?php

function send_mail($address, $subject, $body) {

    $headers = 'From: noreply@vivace.rrwebstudio.com' . "\r\n";
    $headers  .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    'Reply-To: rose@rrwebstudio.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    $send_mail = mail($address, $subject, $body, $headers);

    return $send_mail;

}

function redirect($url) {
    header('Location: '.$url);
    die();
}