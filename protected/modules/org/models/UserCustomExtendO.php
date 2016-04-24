<?php

class UserCustomExtendO extends UserCustomExtend {
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    
    /**
     * 处理自定义字段
     * 
     * @param array $names 名称数组
     */
    public function dealNamesO(array $names)
    {
        $ids = array();
        foreach ($names as $v) {
            $model = $this->getByNameO(trim($v));
            if (!empty($model)) {
                array_push($ids, $model->id);
                continue;
            }
            $model = new UserCustomExtend();
            $r = $this->createO($v, $model);
            if ($r) {
                array_push($ids, $model->id);
            }
        }
        return $ids;
    }
    
    
    /**
     * 根据名称或者字段
     * 
     * @param type $name 名称
     */
    public function getByNameO($name) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.subject', $name);
        return $this->find($cr);
    }
    
    
    /**
     * 创建字段
     * 
     * @param type $name 名称
     */
    public function createO($name, $model = NULL)
    {
        if (empty($model)) {
            $model = new UserCustomExtend();
        }
        $model->subject = trim($name);
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}

?>
