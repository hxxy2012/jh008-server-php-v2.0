<?php

class ActShareM extends ActShare {
    
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
     * 获取用户分享活动次数
     * 
     * @param type $uid 用户id
     */
    public function shareActNumM($uid) 
    {
        return $this->count('u_id=:uid', array(':uid' => $uid));
    }
    
    
    /**
     * 获取活动被分享次数
     * 
     * @param type $actId 活动id
     */
    public function shareNumM($actId) 
    {
        return $this->count('act_id=:actId', array(':actId' => $actId));
    }
    
    
    /**
     * 分享者列表
     * 
     * @param type $actId 活动id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function shareUsersM($actId, $cityId = NULL, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $susers = array();
        foreach ($rst as $v) {
            $suser = UserInfoM::model()->suserM(NULL, $v->u_id, $cityId);
            if (empty($suser)) {
                continue;
            }
            $suser['share_time'] = $v->create_time;
            array_push($susers, $suser);
        }
        
        return array(
            'total_num' => $count,
            's_users' => $susers,
        );
    }
    
    
    /**
     * 分享的活动列表
     * 
     * @param type $uid 用户id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function shareActsM($uid, $cityId = NULL, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        
        if (!empty($cityId)) {
            $cr->with = 'fkAct';
            $cr->compare('fkAct.city_id', $cityId);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $acts = array();
        foreach ($rst as $v) {
            $act = ActInfoM::model()->profileM(NULL, $v->act_id);
            if (empty($act)) {
                continue;
            }
            $act['share_time'] = $v->create_time;
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
}

?>
