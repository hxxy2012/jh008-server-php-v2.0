<?php

class CityActTagMapM extends CityActTagMap {
    
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
     * 城市活动标签分类列表
     * 
     * @param type $cityId 城市id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function tagsM($cityId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        $cr->order = 't.modify_time asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $tags = array();
        foreach ($rst as $v) {
            $tag = ActTagM::model()->profileM(NULL, $v->tag_id);
            if (empty($tag)) {
                continue;
            }
            array_push($tags, $tag);
        }
        
        return array(
            'total_num' => $count,
            'tags' => $tags,
        );
    }
    
    
    /**
     * 添加城市活动标签关联
     * 
     * @param type $cityId 城市id
     * @param type $name 标签名称
     */
    public function addM($cityId, $name)
    {
        $tag = ActTagM::model()->getM($name);
        if (empty($tag)) {
            $model = new ActTag();
            ActTagM::model()->addM($model, $name);
        }
        $isNew = FALSE;
        if (empty($model)) {
            $model = new CityActTagMap();
            $model->create_time = date('Y-m-d H:i:s');
            $isNew = TRUE;
        }
        $model->city_id = $cityId;
        $model->tag_id = $tag->id;
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNew) {
            return $model->save();
        }  else {
            return $model->update();
        }
    }
    
    
    /**
     * 删除标签
     * 
     * @param type $cityId 城市id
     * @param type $tagId 标签id
     */
    public function delM($cityId, $tagId)
    {
        $model = $this->getM($cityId, $tagId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 查看城市标签关联
     * 
     * @param type $cityId 城市id
     * @param type $tagId 标签id
     */
    public function getM($cityId, $tagId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        $cr->compare('t.tag_id', $tagId);
        return $this->find($cr);
    }
    
}

?>
