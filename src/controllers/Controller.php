<?php

namespace salpay\controllers;

use salpay\helpers\CommandLineHelper;
use salpay\models\MainModel;

class Controller
{
    public $cmdInput;
    public function __construct($cmdInput)
    {
        $this->cmdInput = $cmdInput;
    }

    /**
     * Print message into console
     * @param string $message
     */
    public function render(string $message): void
    {
        $message .= "\n";
        fwrite(STDIN, $message);
    }

    /**
     * Creates a file or return false
     * @return bool
     */
    public function resolve(): bool
	{
	    $clHelper = new CommandLineHelper();
	    $argument = $clHelper->parseCmdInput($this->cmdInput);

	    $model = new MainModel();
	    try{
            $isGenerated = $model->createFile($argument);
        } catch (\Exception $e) {
	        $this->render($e->getMessage());
	        return false;
        }

	    if ($isGenerated) {
	        $message = "The file {$argument}.csv was successfully created in {$model->filename}";
	        $this->render($message);
	        return true;
        }
	}
}
