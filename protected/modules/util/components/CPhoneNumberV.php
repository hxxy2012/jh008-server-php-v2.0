<?php

class CPhoneNumberV extends CValidator {
    
	/**
     * 验证是否为有效的手机号码
     * @param type $object
     * @param type $attribute
     */
	public function validateAttribute($object, $attribute)
    {
		$value=$object->$attribute;
        if (empty($value)) {
            return;
        }
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|14[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|17[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$/", $value)) {
            $this->addError($object, $attribute, '{attribute}');
		}
	}
    
}

?>
