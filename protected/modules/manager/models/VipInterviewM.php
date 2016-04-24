<?php

class VipInterviewM extends VipInterview {
    
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
     * 达人的专访
     * 
     * @param type $uid 用户id
     */
    public function getM($uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        return $this->find($cr);
    }
    
    
    /**
     * 更新达人专访关联
     * 
     * @param type $model 达人专访关联数据
     * @param type $uid 用户id
     * @param type $newsId 资讯id
     */
    public function updateM($model = NULL, $uid = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = new VipInterview();
        }
        if (!empty($uid)) {
            $model->u_id = $uid;
        }
        if (!empty($newsId)) {
            $model->news_id = $newsId;
        }
        
        $m = $this->getM($model->u_id, $model->news_id);
        if (empty($m)) {
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        $m->status = ConstStatus::NORMAL;
        return $m->update();
    }
    
    
    /**
     * 达人的专访
     * 
     * @param type $uid 用户id
     * @param type $newsId 资讯id
     */
    public function getVipInterviewM($uid, $newsId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.news_id', $newsId);
        return $this->find($cr);
    }
    
    
    /**
     * 删除达人和专访关联
     * 
     * @param type $uid 用户id
     * @param type $newsId 资讯id
     */
    public function delM($uid, $newsId)
    {
        $model = $this->getM($uid, $newsId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
