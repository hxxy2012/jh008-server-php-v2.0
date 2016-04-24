<?php

class ActEnrollO extends ActEnroll {
    
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
     * 活动成员数
     * 
     * @param type $actId 活动id
     */
    public function countMembersO($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->compare('t.u_id', '>' . 0);
        return $this->count($cr);
    }

    
    /**
     * 活动成员列表
     * 
     * @param type $actId 活动id
     */
    public function membersO($actId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->compare('t.u_id', '>' . 0);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        //$cr->offset = ($page - 1) * $size;
        //$cr->limit = $size;
        $rst = $this->findAll($cr);
        $groups = ActGroupO::model()->getGroupO($actId);
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfoO::model()->profileO($v->u_id, NULL);
            if (empty($user)) {
                continue;
            }
            $user['group_id'] = $v->group_id;
            if(isset($groups[$v->group_id])){
                $user['group_name'] = $groups[$v->group_id]['name'];
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
    
    /**
     * 设置报名状态
     * @param type $enrollId 报名号
     * @param type $status  状态
     */
    public function setEnrollState($enrollId, $status)
    {
        $enrollInfo = $this->findByPk($enrollId);
        if($enrollInfo){
            $enrollInfo->status = $status;
            return $enrollInfo->update();
        }
        return false;
    }
    
    
    /*
     * 活动成员列表
     */
     public function enrollUserList($actId)
    {
        $cr = new CDbCriteria();
        //$cr->select  = 't.id, t.u_id, t.phone';
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        //$cr->order = 't.id asc';
        $rst = $this->findAll($cr);
 
        //$array = CHtml::listData($this->findAll($cr), 'u_id', 'phone');
        return array_map(function($record){
            //if($record->attributes['u_id'] > 0){
                return $record->attributes;
            //}
        },$rst);
        
        /*
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->select  = 't.phone';
        $cr->distinct = true;
        $rst = $this->findAll($cr);
        
        $ret['phoneList'] =  array_map(function($record){
            if(!empty($record->attributes['phone'])){
                return $record->attributes['phone'];
            }
        },$rst);
        */
    }
    
    /*
     * 活动成员电话列表，发短信用
     */
    public function enrollPhoneList($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->select  = 't.phone';
        $cr->distinct = true;
        $rst = $this->findAll($cr);
        
        return array_map(function($record){
            if(!empty($record->attributes['phone'])){
                return $record->attributes['phone'];
            }
        },$rst);
    }
    
}

?>
