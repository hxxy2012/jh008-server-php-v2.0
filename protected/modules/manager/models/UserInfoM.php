<?php

class UserInfoM extends UserInfo {
    
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
     * 搜索列表中的用户
     * 
     * @param type $uid
     */
    public function suserM($model = NULL, $uid = NULL, $cityId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($uid);
        }
        if (empty($model)) {
            return NULL;
        }
        $suser = array();
        $suser['id'] = $model->id;
        
        //用户扩展信息
        $extendArr = UserInfoExtend::model()->fullProfile($model->id);
        $suser['nick_name'] = $extendArr['nick_name'];
        $suser['real_name'] = $extendArr['real_name'];
        $suser['contact_phone'] = $extendArr['contact_phone'];
        $suser['address'] = $extendArr['address'];
        
        if (!empty($cityId)) {
            //查询在某个城市该用户是否为达人
            $ucity = UserCityMap::model()->get($model->id, $cityId);
            if (empty($ucity)) {
                $suser['is_vip'] = 0;
            }  else {
                $suser['is_vip'] = 1;
            }
        }
        
        return $suser;
    }
    
    
    /**
     * 基本信息
     * 
     * @param type $model 用户数据
     * @param type $uid 用户id
     * @param type $cityId 城市id
     */
    public function profileM($model = NULL, $uid = NULL, $cityId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($uid);
        }
        if (empty($model)) {
            return NULL;
        }
        $baseArr = array(
            'id' => $model->id,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($model->id),
            'status' => $model->status,
        );
        
        if (!empty($cityId)) {
            //查询在某个城市该用户是否为达人
            $ucity = UserCityMapM::model()->ucityM($model->id, $cityId);
            if (empty($ucity)) {
                $baseArr['is_vip'] = 0;
            }  else {
                $baseArr['is_vip'] = 1;
            }
        }
        
        //用户扩展信息
        $extendArr = UserInfoExtend::model()->fullProfile($model->id);
        
        if (empty($extendArr)) {
            $extendArr = array();
        }
        
        $extendArr['shared_num'] = 
                ActShareM::model()->shareActNumM($model->id)
                + 
                NewsShareM::model()->shareNewsNumM($model->id);
        $extendArr['loved_num'] = 
                ActLovUserMapM::model()->lovActNumM($model->id)
                + 
                NewsLovUserMapM::model()->lovNewsNumM($model->id);
        $extendArr['enroll_num'] = ActEnrollM::model()->enrollActNumM($model->id);
        $extendArr['checkin_num'] = ActCheckinM::model()->checkinActNumM($model->id);
        $extendArr['dynamic_num'] = UserDynamicM::model()->dynamicNumM($model->id);
        
        return array_merge($baseArr, $extendArr);
    }
    
    
    /**
     * 详细信息
     * 
     * @param type $model 用户数据
     * @param type $uid 用户id
     * @param type $cityId 城市id
     */
    public function fullProfileM($model = NULL, $uid = NULL, $cityId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($uid);
        }
        if (empty($model)) {
            return NULL;
        }
        $baseArr = array(
            'id' => $model->id,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($model->id),
            'status' => $model->status,
        );
        
        if (!empty($cityId)) {
            //查询在某个城市该用户是否为达人
            $ucity = UserCityMapM::model()->ucityM($model->id, $cityId);
            if (empty($ucity)) {
                $baseArr['is_vip'] = 0;
            }  else {
                $baseArr['is_vip'] = 1;
            }
        }
        
        //用户扩展信息
        $extendArr = UserInfoExtend::model()->fullProfile($model->id);
        
        if (empty($extendArr)) {
            $extendArr = array();
        }
        
        $extendArr['shared_num'] = 
                ActShareM::model()->shareActNumM($model->id)
                + 
                NewsShareM::model()->shareNewsNumM($model->id);
        $extendArr['loved_num'] = 
                ActLovUserMapM::model()->lovActNumM($model->id)
                + 
                NewsLovUserMapM::model()->lovNewsNumM($model->id);
        $extendArr['enroll_num'] = ActEnrollM::model()->enrollActNumM($model->id);
        $extendArr['checkin_num'] = ActCheckinM::model()->checkinActNumM($model->id);
        $extendArr['dynamic_num'] = UserDynamicM::model()->dynamicNumM($model->id);
        
        return array_merge($baseArr, $extendArr);
    }

    
    /**
     * 用户搜索
     * 
     * @param type $cityId 城市id
     * @param type $sex 性别
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function usersM($cityId = NULL, $sex = NULL, $keyWords = NULL, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->compare('t.is_regist', '<>' . 0);
        
        if (!empty($sex) || !empty($keyWords)) {
            $cr->with = 'fkExtend';
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
            $user = $this->profileM($v, NULL, $cityId);
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
