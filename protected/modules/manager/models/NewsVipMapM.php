<?php

class NewsVipMapM extends NewsVipMap {
    
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
     * 相关达人
     * 
     * @param type $newsId 资讯id
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function vipsM($newsId, $cityId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        $vips = array();
        foreach ($rst as $v) {
            $vip = UserInfoM::model()->suserM(NULL, $v->u_id, $cityId);
            if (empty($vip)) {
                continue;
            }
            array_push($vips, $vip);
        }
        
        return array(
            'total_num' => $count,
            'vips' => $vips,
        );
    }
    
    
    /**
     * 更新资讯达人关联
     * 
     * @param type $model 资讯达人关联数据
     * @param type $newsId 资讯id
     * @param type $vipId 达人id
     */
    public function updateM($model = NULL, $newsId = NULL, $vipId = NULL)
    {
        if (empty($model)) {
            $model = new NewsVipMap();
        }
        if (!empty($newsId)) {
            $model->news_id = $newsId;
        }
        if (!empty($vipId)) {
            $model->u_id = $vipId;
        }
        
        $m = $this->getM($model->news_id, $model->u_id);
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
     * @param type $vipId 达人id
     */
    public function getM($newsId, $vipId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.news_id', $newsId);
        $cr->compare('t.u_id', $vipId);
        return $this->find($cr);
    }
    
    
    /**
     * 删除资讯和达人关联
     * 
     * @param type $newsId 资讯id
     * @param type $vipId 达人id
     */
    public function delM($newsId, $vipId)
    {
        $model = $this->getM($newsId, $vipId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
