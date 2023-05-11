<?php

class JsonURL {
    public $cat;
    
    function get_json($cat) {
        return json_encode($cat);
    }
}