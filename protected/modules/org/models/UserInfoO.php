<?php

class UserInfoO extends UserInfo {
    
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
     * 用户搜索
     * 
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function usersO($keyWords, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->compare('t.is_regist', 1);
        
        if (!empty($keyWords)) {
            $cr->with = 'fkExtend';
            $cr->compare('fkExtend.nick_name', trim($keyWords), TRUE);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = $this->profileO(NULL, $v);
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
    
    
    /**
     * 基本信息
     * 
     * @param type $uid 用户id
     * @param type $model 用户数据
     */
    public function profileO($uid, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($uid);
        }
        
        if (empty($model)) {
            return NULL;
        }
        
        $img = UserHeadImgMap::model()->getCurrImg($model->id);
        $baseArr = array(
            'id' => $model->id,
            'pho_num' => $model->pho_num,
            'head_img_id' => empty($img) ? NULL : $img['id'],
            'head_img_url' => empty($img) ? NULL : $img['img_url'],
            'status' => $model->status,
        );
        
        //用户扩展信息
        $extendArr = UserInfoExtendO::model()->profileO($model->id, empty($model->fkExtend) ? NULL : $model->fkExtend);
        
        if (empty($extendArr)) {
            $extendArr = array();
        }
        
        return array_merge($baseArr, $extendArr);
    }
    
    
    /*
     * 查询用户手机号码
     * @param type $uid 用户uid
     * @return null
     */
    public function getPhoneNum($uid)
    {
        $model = $this->findByPk($uid);
        if($model){
            return $model->pho_num;
        }
        return NULL;
    }
}

?>
