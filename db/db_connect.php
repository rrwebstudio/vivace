<?php

function connect_db(){
    $filename = 'db.ini';
    $config = parse_ini_file($filename);
    $connect = new mysqli("database",$config['MYSQL_USER'],$config['MYSQL_PASSWORD'],$config['MYSQL_DATABASE']);
    if(!$connect){
        die("Failed to connect to Database"); 
    }
    return $connect;
}