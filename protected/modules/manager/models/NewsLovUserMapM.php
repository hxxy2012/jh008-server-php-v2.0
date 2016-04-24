<?php

class NewsLovUserMapM extends NewsLovUserMap {
    
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
     * 获取用户收藏的资讯数
     * 
     * @param type $uid 用户id
     */
    public function lovNewsNumM($uid) 
    {
        return $this->count('u_id=:uid', array(':uid' => $uid));
    }
    
    
    /**
     * 获取资讯的收藏者数
     * 
     * @param type $newsId 资讯id
     */
    public function lovNumM($newsId) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('news_id', $newsId);
        return $this->count($criteria);
    }
 
    
    /**
     * 收藏者列表
     * 
     * @param type $newsId 资讯id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function lovUsersM($newsId, $cityId = NULL, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
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
            $suser['lov_time'] = $v->lov_time;
            array_push($susers, $suser);
        }
        
        return array(
            'total_num' => $count,
            's_users' => $susers,
        );
    }
    
}

?>
