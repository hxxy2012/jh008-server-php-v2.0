<?php

class NewsActMapM extends NewsActMap {
    
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
     * 相关活动
     * @param type $newsId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function actsM($newsId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.status', ConstActStatus::PUBLISHING);
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
            array_push($acts, $act);
        }
        
        return array(
            'total_num' => $count,
            'acts' => $acts,
        );
    }
    
    
    /**
     * 更新资讯活动关联
     * 
     * @param type $model 资讯活动关联数据
     * @param type $newsId 资讯id
     * @param type $actId 活动id
     */
    public function updateM($model = NULL, $newsId = NULL, $actId = NULL)
    {
        if (empty($model)) {
            $model = new NewsActMap();
        }
        if (!empty($newsId)) {
            $model->news_id = $newsId;
        }
        if (!empty($actId)) {
            $model->act_id = $actId;
        }
        
        $m = $this->getM($model->news_id, $model->act_id);
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
     * @param type $newsId 资讯id
     * @param type $actId 活动id
     */
    public function getM($newsId, $actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.act_id', $actId);
        return $this->find($cr);
    }
    
    
    /**
     * 删除资讯和活动关联
     * 
     * @param type $newsId 资讯id
     * @param type $actId 活动id
     */
    public function delM($newsId, $actId)
    {
        $model = $this->getM($newsId, $actId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
