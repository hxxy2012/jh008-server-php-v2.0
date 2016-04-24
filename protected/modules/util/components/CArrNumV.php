<?php

class CArrNumV extends CValidator {
    
    //最小的长度
    public $minLen = 0;
    
    //最大的长度
    public $maxLen = 12;
    
    //参数的范围
    public $inArr = array();
    
    public $minNum;
    
    public $maxNum;

    /**
     * 验证是否为整形数组
     * @param type $object
     * @param type $attribute
     */
	public function validateAttribute($object, $attribute)
    {
		$value = $object->$attribute;
        if (empty($value)) {
            return;
        }
        if (!is_array($value)) {
            $this->addError($object, $attribute, $value . ' is not array');
        }
        if (count($value) < $this->minLen) {
            $this->addError($object, $attribute, json_encode($value) . ' is not >= minLen');
        }
        if (count($value) > $this->maxLen) {
            //$this->addError($object, $attribute, '{attribute} is not <= maxLen');
            $this->addError($object, $attribute, json_encode($value) . ' is not <= maxLen');
        }
        
        foreach ($value as $k => $v) {
            if (!is_numeric($v)) {
                $this->addError($object, $attribute, $v . ' is not num');
            }
            
            if (!empty($this->inArr) && !in_array($v, $this->inArr)) {
                $this->addError($object, $attribute, $v . ' is not in inArr');
            }
            
            if (!empty($this->minNum) && $v < $this->minNum) {
                $this->addError($object, $attribute, $v . ' is not >= minNum');
            }
            
            if (!empty($this->maxNum) && $v > $this->maxNum) {
                $this->addError($object, $attribute, $v . ' is not <= maxNum');
            }
        }
	}
    
}

?>
