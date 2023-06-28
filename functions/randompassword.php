<?php

function randomPassword(int $passwordLength = 8): string {
    $alphanum = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
    $pass = array();
     $alphaLength = strlen($alphanum) - 1;
     for ($i = 0; $i < $passwordLength; $i++) {
         $n = random_int(0, $alphaLength);
         $pass[] = $alphanum[$n];
     }
     return implode($pass);
 }