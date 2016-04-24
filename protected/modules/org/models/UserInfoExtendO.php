<?php

class UserInfoExtendO extends UserInfoExtend {
    
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
     * 基本信息
     * 
     * @param type $uid 用户id
     * @param type $model 用户数据
     */
    public function profileO($uid, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->find('u_id=:uid', array(':uid' => $uid));
        }
        
        if (empty($model)) {
            return NULL;
        }
        
        return array(
            'nick_name' => $model->nick_name,
            'sex' => $model->sex,
            'birth' => date('Y-m-d', strtotime($model->birth)),
            'real_name' => $model->real_name,
        );
    }
    
}

?>
