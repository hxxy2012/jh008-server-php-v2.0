<?php

class ActCheckinStepO extends ActCheckinStep {
    
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
     * 活动签到的用户数
     * 
     * @param type $actId
     */
    public function countCheckinUsersO($actId)
    {
        $countSql = "SELECT COUNT(DISTINCT acu.u_id) AS count FROM act_checkin_step AS ac LEFT JOIN act_checkin_user_map AS acu ON ac.id=acu.step_id WHERE ac.act_id=:actId AND ac.status=:status";
        $countCommand = $this->getDbConnection()->createCommand($countSql);
        $countCommand->bindParam(':actId', $actId, PDO::PARAM_INT);
        $countCommand->bindValue(':status', ConstStatus::NORMAL, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return $rst['count'];
    }
    

    /*
     * 获取活动签到码
     */
    public function actCheckinList($actId)
    {
        return array_map(function($recode){ 
            return array_merge($recode->attributes, array('qrcode_info' => '{"filter":"checkin_id","value":'.$recode->attributes['id'].'}'));
        }, $this->findAll('act_id = :act_id AND status = :status', array('act_id' => $actId, 'status' => ConstStatus::NORMAL)));
    }
    
    public function  addCheckin($actId, $subject, $need_sure = 0)
    {
        $model = new ActCheckinStepO();
        $model->act_id = $actId;
        $model->subject = $subject;
        $model->status = 0;
        $model->need_sure = $need_sure;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = $model->create_time;
        if($model->save()){
            return array_merge($model->attributes, array('qrcode_info' => '{"filter":"checkin_id","value":'.$model->attributes['id'].'}'));
        }
        return false;
    }
    
    /*
     * 修改签到码
     */
    public function  modifyCheckin($id, $subject, $need_sure = null)
    {
        $checkin = $this->findByPk($id);
        if($checkin){
            $checkin->subject = $subject;
            if(isset($need_sure)){
                $checkin->need_sure = $need_sure;
            }
            $checkin->modify_time = date('Y-m-d H:i:s');
            if($checkin->update()){
                return array_merge($checkin->attributes, array('qrcode_info' => '{"filter":"checkin_id","value":'.$checkin->attributes['id'].'}'));
            }
        }
        return false;
    }
    
    /*
     * 删除签到码
     */
     public function  delCheckin($id)
    {
        $checkin = $this->findByPk($id);
        if($checkin){
            $checkin->status = -1;
            $checkin->modify_time = date('Y-m-d H:i:s');
            return $checkin->update();
        }
        return false;
    }
    
    
    public function countCheckinStep($id)
    {
        $countSql = "SELECT COUNT(DISTINCT u_id) AS cnt FROM act_checkin_user_map WHERE step_id = :id";
        $countCommand = $this->getDbConnection()->createCommand($countSql);
        $countCommand->bindParam(':id', $id, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return $rst['cnt'];
    }
    
    
     public function actCheckinDA($actId)
    {
        $rst =  array_map(function($recode){ 
            return array_merge($recode->attributes, 
                array('checked_in' => $this->countCheckinStep($recode->attributes['id']),
            ));
        }, $this->findAll('act_id = :act_id AND status = :status', array('act_id' => $actId, 'status' => ConstStatus::NORMAL)));
        return $rst;
    }
    
    public function countActSex($actId, $sex)
    {
        $countSql = "SELECT COUNT(DISTINCT acu.`u_id`) AS cnt FROM act_checkin_user_map AS acu LEFT JOIN act_checkin_step AS ac ON ac.id=acu.step_id LEFT JOIN act_enroll AS ae ON acu.`u_id`=ae.`u_id` AND ac.`act_id`=ae.`act_id` WHERE ac.`act_id` = :actId AND ac.status = :status AND ae.`sex` = :sex";
        
        $countCommand = $this->getDbConnection()->createCommand($countSql);
        $countCommand->bindParam(':actId', $actId, PDO::PARAM_INT);
        $countCommand->bindValue(':status', ConstStatus::NORMAL, PDO::PARAM_INT);
        $countCommand->bindParam(':sex', $sex, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return intval($rst['cnt']);
    }
    
    


    
    
}

?>
