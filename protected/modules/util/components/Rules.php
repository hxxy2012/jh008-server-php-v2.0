<?php
/**
 * 参数验证
 */
class Rules extends CModel
{
    
    public $values = array();
    
    public $rules = array();

    
    function __construct($values,$rules)
    {
        if (isset($values))
            $this->values = $values;
        if (isset($rules))
            $this->rules = $rules;
    }
    
    
    public function attributeNames()
    {
        $names = array();
        foreach ($this->values as $key => $value){
            $names[] = $key;
        }
        return $names;
    }
    
    
    public function rules()
	{
		return $this->rules;
	}
    
    
	public function attributeLabels()
	{
		return $this->values;
	}
    
    
    public static function instance($values, $rules) 
    {
        return new Rules($values, $rules);
    }
    
    
    function __isset($name) {
        if (array_key_exists($name, $this->values)) {
            return isset($this->values[$name]);
        }
        parent::__isset($name);
    }
    
    
    function __set($name, $value) 
    {
        if (array_key_exists($name, $this->values)) {
            $this->values[$name] = $value;
        }
        parent::__set($name, $value);
    }
    
    
    function __get($name) 
    {
        if (array_key_exists($name, $this->values)) {
            return $this->values[$name];
        }
        parent::__get($name);
    }
    
    
    /**
     * 给model对应字段赋值
     * @param type $model
     */
    public function setModelAttris($model) 
    {
        foreach ($this->values as $key => $value) {
            if (isset($value) && array_key_exists($key, $model->attributes)) {
                $model->$key = $value;
            }
        }
    }
    
    
    public function __toString() 
    {
        return json_encode($this->values);
    }
}

