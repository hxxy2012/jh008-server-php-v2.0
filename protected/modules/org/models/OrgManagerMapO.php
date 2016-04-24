<?php

class OrgManagerMapO extends OrgManagerMap {
    
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
     * 获取社团管理员关联
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function get($orgId, $uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.u_id', $uid);
        return $this->find($cr);
    }
    
    
    /**
     * 添加管理员
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function addO($orgId, $uid)
    {
        $model = $this->get($orgId, $uid);
        if (!empty($model)) {
            if (ConstStatus::NORMAL == $model->status) {
                return TRUE;
            }
            $model->status = ConstStatus::NORMAL;
            return $model->update();
        }
        $model = new OrgManagerMap();
        $model->org_id = $orgId;
        $model->u_id = $uid;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 删除管理员
     * 
     * @param type $orgId 社团id
     * @param type $uid 用户id
     */
    public function delO($orgId, $uid)
    {
        $model = $this->get($orgId, $uid);
        if (empty($model)) {
            return TRUE;
        }
        if (ConstStatus::DELETE == $model->status) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }

    
    /**
     * 社团管理员
     * 
     * @param type $orgId 社团id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function managersO($orgId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfoO::model()->profileO($v->u_id, NULL);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
}

?>
