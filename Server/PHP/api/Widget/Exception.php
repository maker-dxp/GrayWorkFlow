<?php

class Widget_Exception extends Exception {
    public function __construct($message, $code = 0)
    {
        parent::__construct();
        $this->message = $message . "\n";
        $this->code = $code;
    }
}