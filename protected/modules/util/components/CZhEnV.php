<?php

class CZhEnV extends CValidator {
    public $min = 0;
    public $max = 0;
    public $chr = 'UTF8';
    public $isDiff = FALSE;


    /**
	 * 验证中英混排长度（中文也算一个字符）
	 * 
	 * @param object $object
	 * @param string $attribute
	 * 
	 * @return 
	 */
	public function validateAttribute($object, $attribute) 
    {
		$value = $object->$attribute;
        if (empty($value)) {
            return;
        }
        $zhEnLen = 0;
        if ($this->isDiff) {
            $zhEnLen = (strlen($value) + mb_strlen($value, $this->chr)) / 2; 
        }  else {
            $zhEnLen = mb_strlen($value, $this->chr);
        }
        
		if($zhEnLen < $this->min || $zhEnLen > $this->max) {
            $this->addError($object,$attribute, '{attribute}$zhEnLen');
		}
	}
	
}

