<?php

class Template {

    public $page;
    public $user_id;     

    public function __construct($page, $user) {
        $this->page = $page;        
        $this->user_id = $user;  
    }    
}