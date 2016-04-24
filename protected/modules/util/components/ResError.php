<?php

class ResError extends CException
{
    
    public $status;
    public $msg;


    public function __construct($status, $msg) 
    {
        $this->atatus = $status;
        $this->msg = $msg;
        //parent::__construct($msg, $status);
        Yii::app()->res->output($status, $msg);
        exit();
    }
    
    
    
}

?>
