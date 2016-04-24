<?php

class ActCheckinAdmin extends ActCheckin {
    
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
     * 获取活动签到的用户列表
     * @param type $needUserInfo 是否需要完整的用户信息
     */
    public function getUserIds($actId, $startTime = NULL, $endTime = NULL, $needUserInfo = FALSE) 
    {
        $cr = new CDbCriteria();
        $cr->select = 't.u_id';
        $cr->compare('t.act_id', $actId);
        if (!empty($startTime)) {
            $cr->compare('t.create_time', '>=' . $startTime);
        }
        if (!empty($endTime)) {
            $cr->compare('t.create_time', '<=' . $endTime);
        }
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        
        $cr->with = array('fkUser.fkExtend');
        $cr->compare('fkUser.status', '<>' . ConstStatus::DELETE);
        if ($needUserInfo) {
            //排除资料不完整的用户（真实姓名和联系电话）
            $cr->addCondition('`fkExtend`.`real_name` is not null and LENGTH(trim(`fkExtend`.`real_name`))>0');
            $cr->addCondition('`fkExtend`.`contact_phone` is not null and LENGTH(trim(`fkExtend`.`contact_phone`))>0');
        }
        $rst = $this->findAll($cr);
        
        $userIds = array();
        foreach ($rst as $v) {
            array_push($userIds, $v->u_id);
        }
        return $userIds;
    }
    
}

?>
