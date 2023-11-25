<?php

class Response{
    public $code; 
    public $data;

    public function __construct($code, $data){

        $this->code = $code;
        $this->data = $data;
    }
    public function respond(){

        http_response_code($this->code);
        echo json_encode($this->data);
    }
}