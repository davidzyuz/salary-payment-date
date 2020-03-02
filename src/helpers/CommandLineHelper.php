<?php

namespace salpay\helpers;

class CommandLineHelper
{
    /**
     * Returns a filename parsed from command line parameters
     * @param array $cmdInput
     * @return mixed
     */
    public function parseCmdInput(array $cmdInput): string
    {
        if (count($cmdInput) <= 1) {
            return 'default';
        }
        return $cmdInput[1];
    }
}