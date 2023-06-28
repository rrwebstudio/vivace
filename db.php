<?php

// Connect to mySQL DB
$connect_db = connect_db();
// Check connection
if ($connect_db->connect_error) {
    die("Connection failed: " . $connect_db->connect_error);
}