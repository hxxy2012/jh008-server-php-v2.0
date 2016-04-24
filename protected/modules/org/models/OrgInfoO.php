<?php

class OrgInfoO extends OrgInfo {
    
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
     * 社团
     * 
     * @param type $uid 用户id
     */
    public function getByUidO($uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.own_id', $uid);
        $cr->compare('t.status', '>=' . ConstStatus::NORMAL);
        $cr->order = 't.id desc';
        return $this->find($cr);
    }
    
    
    /**
     * 插入社团
     * 
     * @param type $ownId 团长id
     */
    public function insOrgO($ownId, $model = NULL)
    {
        if (empty($model)) {
            $model = new OrgInfo();
        }
        if (empty($model->own_id)) {
            $model->own_id = $ownId;
        }
        $model->status = ConstStatus::NORMAL;
        $model->creat_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }

    
    /**
     * 基本资料
     * 
     * @param type $id 社团id
     * @param type $model
     */
    public function profileO($id, $model = NULL) 
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $org = array(
            'id' => $model->id,
            'name' => $model-> name,
            'intro' => $model->intro,
            'status' => $model->status,
            'contact_way' => $model->contact_way,
            'address' => $model->address,
        );
        $img = ImgInfo::model()->profile($model->logo_img_id);
        $org['logo_img_url'] = empty($img) ? NULL : $img['img_url'];
        return $org;
    }
    
    public function getHoldedActCount($orgId)
    {
        $sql = 'select count(*) as cnt from act_info where status = 0 and e_time < now() and org_id = :org_id';
        $countCommand = $this->getDbConnection()->createCommand($sql);
        $countCommand->bindParam(':org_id', $orgId, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return $rst['cnt'];
    }
    
    
    public function getEnrolledActCount($orgId)
    {
        $sql = 'SELECT COUNT(act_enroll.`id`) AS cnt FROM act_enroll, act_info WHERE act_enroll.`act_id` = act_info.`id` AND act_info.`org_id` = :org_id';
        $countCommand = $this->getDbConnection()->createCommand($sql);
        $countCommand->bindParam(':org_id', $orgId, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return $rst['cnt'];
    }
    
    
    public function getCheckedInActCount($orgId)
    {
        $sql = 'SELECT COUNT(act_checkin_user_map.`id`) AS cnt  FROM act_checkin_user_map, act_checkin_step, act_info WHERE act_checkin_user_map.`step_id`= act_checkin_step.`id` AND act_checkin_step.`act_id` = act_info.`id` AND act_checkin_step.`status` = 0  AND act_info.`org_id` = :org_id';
        $countCommand = $this->getDbConnection()->createCommand($sql);
        $countCommand->bindParam(':org_id', $orgId, PDO::PARAM_INT);
        $rst = $countCommand->queryRow();
        return $rst['cnt'];
    }
    
    
    public function  getActiveMembers($orgId)
    {
        $sql = 'SELECT act_checkin_user_map.`u_id` , COUNT(DISTINCT act_checkin_user_map.`step_id`) AS cnt FROM act_checkin_user_map, act_checkin_step, act_info WHERE act_checkin_user_map.`step_id`= act_checkin_step.`id` AND act_checkin_step.`act_id` = act_info.`id` AND act_checkin_step.`status` = 0  AND act_info.`org_id` = :org_id ORDER BY cnt DESC LIMIT 10';
        $countCommand = $this->getDbConnection()->createCommand($sql);
        $countCommand->bindParam(':org_id', $orgId, PDO::PARAM_INT);
        $members = $countCommand->queryAll();
        $rst = array();
        if(!empty($members)){
            foreach ($members as $member){
                $user = UserInfoO::model()->profileO($member['u_id']);
                $user['checked_in'] = $member['cnt'];
                $rst[] = $user;
            }
        }
        return $rst;
    }
    
    
    
    
}

?>
