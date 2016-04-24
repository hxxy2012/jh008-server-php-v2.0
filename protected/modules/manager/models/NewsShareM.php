<?php

class NewsShareM extends NewsShare {
    
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
     * 获取用户分享资讯次数
     * 
     * @param type $uid 用户id
     */
    public function shareNewsNumM($uid) 
    {
        return $this->count('u_id=:uid', array(':uid' => $uid));
    }

    
    /**
     * 获取资讯被分享次数
     * 
     * @param type $newsId 资讯id
     */
    public function shareNumM($newsId) 
    {
        return $this->count('news_id=:newsId', array(':newsId' => $newsId));
    }
    
    
    /**
     * 分享者列表
     * 
     * @param type $newsId 资讯id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function shareUsersM($newsId, $cityId = NULL, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
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
    
}

?>
