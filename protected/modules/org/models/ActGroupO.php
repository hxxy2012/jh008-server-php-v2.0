<?php

class ActGroupO extends ActGroup {
    
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
     * 创建活动分组
     * 
     * @param type $actId 活动id
     * @param type $name 名称
     * @param ActGroup $model
     */
    public function createGroupO($actId, $name, $model = NULL)
    {
        if (empty($model)) {
            $model = new ActGroup();
        }
        if (empty($model->act_id)) {
            $model->act_id = $actId;
        }
        if (empty($model->name)) {
            $model->name = $name;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 修改用户的活动分组id
     * 
     * @param type $uid 用户id
     * @param type $actId 活动id
     * @param type $groupId 分组id
     */
    public function modifyUserGroupId($uid, $actId, $groupId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $model = ActEnrollO::model()->find($cr);
        
        if (empty($model)) {
            return FALSE;
        }
        
        $model->group_id = $groupId;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    /*
     * 获取活动分组
     */
    public function getGroupO($actId)
    {
        $groups  = $this->findAll('act_id = :act_id AND status = 0', array('act_id' => $actId));
        $list = array();
        foreach ($groups as $group){
            $list[$group->attributes['id']] = $group->attributes;
        }
        return $list;
    }
    
    
}

?>
