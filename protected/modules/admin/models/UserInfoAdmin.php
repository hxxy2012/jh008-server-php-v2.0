<?php

class UserInfoAdmin extends UserInfo
{
    
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
     * 获取某个用户的信息
     * @param type $uid
     */
    public function getInfo($uid)
    {
        $model = $this->findByPk($uid);
        $baseArr = array(
            'id' => $model->id,
            'head_img_url' => UserHeadImgMap::model()->getCurImgUrl($model->id),
            'status' => $model->status,
        );
        $extendArr = UserInfoExtend::model()->fullProfile($model->id);
        return array_merge($baseArr, $extendArr);
    }
    
    
    /**
     * 搜索用户列表
     * @param type $keyWords
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function searchUsers($keyWords, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('t.user_name', $keyWords, TRUE, 'OR');
            $crs->compare('t.pho_num', $keyWords, TRUE, 'OR');
            //$crs->compare('t.nick_name', $keyWords, TRUE, 'OR');
            //$crs->compare('t.real_name', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', ConstStatus::NORMAL);
        }
        $cr->compare('t.is_regist', '<>0');
        $cr->with = 'fkHeadImg.fkImg';
        $count = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = array();
            $user['id'] = $v->id;
            //=====================
            //$user['nick_name'] = $v->nick_name;
            //$user['sex'] = $v->sex;
            //$user['birth'] = $v->birth;
            //$user['address'] = $v->address;
            //$user['email'] = $v->email;
            //$user['real_name'] = $v->real_name;
            //$user['contact_qq'] = $v->contact_qq;
            //$user['contact_phone'] = $v->contact_phone;
            //======================
            if (!empty($v->fkHeadImg)) {
                $user['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkHeadImg->fkImg->img_url);
            }
            $user['status'] = $v->status;
            $userExtend = UserInfoExtend::model()->fullProfile($v->id);
            array_push($users, array_merge($user, $userExtend));
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
    
    /**
     * 获取时间段内的已注册用户数
     * @param type $startTime
     * @param type $endTime
     */
    public function countUsers($startTime, $endTime)
    {
        if ($endTime <= $startTime) {
            return 0;
        }
        $cr = new CDbCriteria();
        $cr->compare('is_regist', '<>0');
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        $cr->addBetweenCondition('create_time', $startTime, $endTime);
        //$cr->compare('create_time', '>=' . $startTime);
        //$cr->compare('create_time', '<=' . $endTime);
        return $this->count($cr);
    }
    
    
    /**
     * 获取所有的已注册的用户数
     */
    public function countAllUsers()
    {
        $cr = new CDbCriteria();
        $cr->compare('is_regist', '<>0');
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        return $this->count($cr);
    }
 
    
    /**
     * 获取所有用户的id
     */
    public function getAllIds()
    {
        $cr = new CDbCriteria();
        $cr->select = 'id';
        $cr->compare('is_regist', '<>0');
        $cr->compare('status', '<>' . ConstStatus::DELETE);
        $rst = $this->findAll($cr);
        
        $ids = array();
        foreach ($rst as $v) {
            array_push($ids, $v->id);
        }
        return $ids;
    }
    
}

?>
