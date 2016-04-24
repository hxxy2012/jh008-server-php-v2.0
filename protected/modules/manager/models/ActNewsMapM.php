<?php

class ActNewsMapM extends ActNewsMap {
    
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
     * 相关资讯
     * 
     * @param type $actId 活动id
     * @param type $typeId 类型id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function newsM($actId, $typeId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        
        $cr->with = 'fkNews';
        $cr->compare('fkNews.status', ConstActStatus::PUBLISHING);
        if (!empty($typeId)) {
            $cr->compare('fkNews.type_id', $typeId);
        }
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $news = array();
        foreach ($rst as $v) {
            $newsInfo = NewsInfoM::model()->profile(NULL, $v->news_id);
            if (empty($newsInfo)) {
                continue;
            }
            array_push($news, $newsInfo);
        }
        
        return array(
            'total_num' => $count,
            'news' => $news,
        );
    }
    
    
    /**
     * 更新活动资讯关联
     * 
     * @param type $model 活动资讯关联数据
     * @param type $actId 活动id
     * @param type $newsId 资讯id
     */
    public function updateM($model = NULL, $actId = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = new ActNewsMap();
        }
        if (!empty($actId)) {
            $model->act_id = $actId;
        }
        if (!empty($newsId)) {
            $model->news_id = $newsId;
        }
        
        $m = $this->getM($model->act_id, $model->news_id);
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
     * @param type $actId 活动id
     * @param type $newsId 资讯id
     */
    public function getM($actId, $newsId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.news_id', $newsId);
        return $this->find($cr);
    }
    
    
    /**
     * 删除活动和资讯关联
     * 
     * @param type $actId 活动id
     * @param type $newsId 资讯id
     */
    public function delM($actId, $newsId)
    {
        $model = $this->getM($actId, $newsId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
