<?php

class NewsInfoM extends NewsInfo {
    
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
     * 基本资料
     * 
     * @param type $newsId
     */
    public function profileM($model = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($newsId);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        $news = array();
        $news['id'] = $model->id;
        $news['title'] = $model->title;
        $news['status'] = $model->status;
        $news['publish_time'] = $model->publish_time;
        
        $news['shared_num'] = NewsShareM::model()->shareNumM($model->id);
        $news['loved_num'] = NewsLovUserMapM::model()->lovNumM($model->id);
        $news['comment_num'] = NewsCommentM::model()->commentNumM($model->id);
        return $news;
    }


    /**
     * 完整资料
     * 
     * @param type $model 活动数据
     * @param type $newsId 活动id
     */
    public function fullProfileM($model = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($newsId);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        
        $news = array();
        $news['id'] = $model->id;
        $news['title'] = $model->title;
        $news['intro'] = $model->intro;
        $news['detail'] = $model->detail;
        $news['detail_url'] = Yii::app()->webPage->getViewUrl('act/news/detail', array('newsId' => $model->id));
        $img = ImgInfo::model()->profile($model->img_id);
        $news['h_img_id'] = empty($img) ? NULL : $img['img_url'];
        $news['status'] = $model->status;
        
        $tag = ActTagM::model()->profileM(NULL, $model->tag_id);
        if (!empty($tag)) {
            $news['tag_name'] = $tag['name'];
        }
        $news['shared_num'] = NewsShareM::model()->shareNumM($model->id);
        $news['loved_num'] = NewsLovUserMapM::model()->lovNumM($model->id);
        $news['comment_num'] = NewsCommentM::model()->commentNumM($model->id);
        return $news;
    }


    /**
     * 资讯列表
     * 
     * @param type $cityId 城市id
     * @param type $typeId 类别id
     * @param type $tagId 分类id
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function newsM($cityId, $typeId, $tagId, $keyWords = NULL, $page, $size, array $ids = NULL) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.city_id', $cityId);
        if (!empty($typeId)) {
            $cr->compare('t.type_id', $typeId);
        }
        if (!empty($tagId)) {
            $cr->compare('t.tag_id', $tagId);
        }
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        if (!empty($keyWords)) {
            $cr->compare('t.title', $keyWords, TRUE);
        }
        if (!empty($ids)) {
            $cr->compare('t.id', $ids);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $news = array();
        foreach ($rst as $v) {
            $newsInfo = $this->profileM($v);
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
     * 添加资讯
     * 
     * @param type $model 资讯数据
     * @param type $price 票务价格
     */
    public function addM($model = NULL, $price)
    {
        if (empty($model)) {
            return FALSE;
        }
        $model->create_time = date('Y-m-d H:i:s', time());
        $model->update_time = date('Y-m-d H:i:s', time());
        $model->status = ConstActStatus::NOT_COMMIT;
        $rst = $model->save(); 
        if ($rst && ConstNewsType::TICKET == $model->type_id) {
            NewsTicketExtendM::model()->upTicketM($model->id, $price);
        }
        return $rst;
    }
    
    
    /**
     * 修改资讯
     * 
     * @param type $model 资讯数据
     * @param type $price 票务价格
     */
    public function updateM($model = NULL, $price = NULL)
    {
        if (empty($model)) {
            return FALSE;
        }
        $model->update_time = date('Y-m-d H:i:s', time());
        $rst = $model->update(); 
        if ($rst && ConstNewsType::TICKET == $model->type_id) {
            NewsTicketExtendM::model()->upTicketM($model->id, $price);
        }
        return $rst;
    }
    
    
    /**
     * 修改资讯状态
     * 
     * @param type $model 资讯数据
     * @param type $newsId 资讯id
     * @param type $status 资讯状态
     */
    public function upNewsStatusM($model = NULL, $newsId = NULL, $status = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($newsId);
        }
        if (empty($model)) {
            return FALSE;
        }
        if (!empty($status)) {
            $model->status = $status;
        }
        if (ConstActStatus::PUBLISHING == $model->status) {
            $model->publish_time = date('Y-m-d H:i:s');
        }
        return $model->update();
    }
    
}

?>
