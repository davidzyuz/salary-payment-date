<?php

namespace salpay\helpers;

class CommandLineHelper
{
    public $argument;


    public function parseLine(array $line)
    {
        $chunks = explode(' ', $line);
        $this->command = $chunks[0];
        $this->options = array_filter($chunks, function ($el) {

        })
    }
}