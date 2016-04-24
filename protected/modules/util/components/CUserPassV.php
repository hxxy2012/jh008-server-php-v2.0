<?php

class CUserPassV extends CValidator {
    
	/**
     * 验证是否为有效的用户密码（以字母开头，长度在6~16之间，只能包含字符、数字和下划线）
     * @param type $object
     * @param type $attribute
     */
	public function validateAttribute($object, $attribute)
    {
		$value = $object->$attribute;
        if (empty($value)) {
            return;
        }
		if(!preg_match("/^[a-zA-Z\d_]{6,16}$/", $value)) {
            $this->addError($object, $attribute, '{attribute}');
		}
	}
    
}

?>
