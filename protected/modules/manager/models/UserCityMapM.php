<?php

class UserCityMapM extends UserCityMap {
    
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
     * 验证达人的城市
     * 
     * @param type $uid 用户id
     * @param type $cityId 城市id
     */
    public function checkVipCityM($uid, $cityId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        return empty($model) ? FALSE : TRUE;
    }
    
    
    /**
     * 用户与城市关联（有效的）
     * 
     * @param type $u_id 用户id
     * @param type $city_id 城市id
     */
    public function ucityM($u_id, $city_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $u_id);
        $cr->compare('t.city_id', $city_id);
        $cr->compare('t.status', ConstStatus::NORMAL);
        return $this->find($cr);
    }
    
    
    /**
     * 达人搜索
     * 
     * @param type $cityId 城市id
     * @param type $sex 性别
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function vipsM($cityId, $sex = NULL, $keyWords = NULL, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        
        if (!empty($sex) || !empty($keyWords)) {
            $cr->with = 'fkUser.fkExtend';
        }
        if (!empty($sex)) {
            $cr->compare('fkExtend.sex', $sex);
        }
        if (!empty($keyWords)) {
            $cr->compare('fkExtend.nick_name', $keyWords, TRUE);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfoM::model()->profileM(NULL, $v->u_id, $cityId);
            if (empty($user)) {
                continue;
            }
            $interview = VipInterviewM::model()->getM($v->u_id);
            if (!empty($interview) && ConstStatus::DELETE != $interview->status) {
                $user['interview_id'] = $interview->news_id;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
    
    /**
     * 更新用户城市关联
     * 
     * @param type $model 资讯活动关联数据
     * @param type $uid 用户id
     * @param type $cityId 城市id
     */
    public function updateM($model = NULL, $uid = NULL, $cityId = NULL)
    {
        if (empty($model)) {
            $model = new UserCityMap();
        }
        if (!empty($uid)) {
            $model->u_id = $uid;
        }
        if (!empty($cityId)) {
            $model->city_id = $cityId;
        }
        
        $m = $this->getM($model->u_id, $model->city_id);
        if (empty($m)) {
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        $m->status = ConstStatus::NORMAL;
        return $m->update();
    }
    
    
    /**
     * 取得数据模型
     * 
     * @param type $u_id 用户id
     * @param type $city_id 城市id
     */
    public function getM($u_id, $city_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $u_id);
        $cr->compare('t.city_id', $city_id);
        return $this->find($cr);
    }
    
    
    /**
     * 删除用户和城市关联
     * 
     * @param type $uid 用户id
     * @param type $cityId 城市id
     */
    public function delM($uid, $cityId)
    {
        $model = $this->getM($uid, $cityId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
