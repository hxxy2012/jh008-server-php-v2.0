<?php

class UserTagM extends UserTag {
    
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
     * 基本信息
     * 
     * @param type $model 标签数据
     * @param type $tagId 标签id
     */
    public function profileM($model = NULL, $tagId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($tagId);
        }
        if (empty($model)) {
            return NULL;
        }
        $tag = array();
        $tag['id'] = $model->id;
        $tag['name'] = $model->name;
        $tag['modify_time'] = $model->create_time;
        $tag['status'] = $model->status;
        return $tag;
    }


    /**
     * 标签
     * 
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function tagsM($keyWords, $page, $size)
    {
        $cr = new CDbCriteria();
        //$cr->compare('t.status', ConstStatus::NORMAL);
        if (!empty($keyWords)) {
            $cr->compare('t.name', $keyWords, TRUE);
        }
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $tags = array();
        foreach ($rst as $v) {
            $tag = $this->profileM($v);
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
     * 添加用户标签
     * 
     * @param UserTag $model
     * @param type $name
     */
    public function addM($model = NULL, $name = null)
    {
        if (empty($model)) {
            $model = new UserTag();
        }
        if (!empty($name)) {
            $model->name = $name;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 修改用户标签
     * 
     * @param type $model 标签数据
     * @param type $tagId 标签id
     * @param type $name 名称
     */
    public function updateM($model = NULL, $tagId = NULL, $name = NULL) 
    {
        if (empty($model)) {
            $model = $this->findByPk($tagId);
        }
        if (empty($model)) {
            return FALSE;
        }
        if (!empty($name)) {
            $model->name = $name;
        }
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 删除标签
     * 
     * @param type $tagId 标签id
     */
    public function delM($tagId)
    {
        $model = $this->getM($tagId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}

?>
