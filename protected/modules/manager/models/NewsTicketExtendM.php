<?php

class NewsTicketExtendM extends NewsTicketExtend {
    
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
     * @param type $model 票务数据
     * @param type $newsId 票务id
     */
    public function profileM($model = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = $this->find('t.news_id=:newsId and t.status=:status', array(
                ':newsId' => $newsId, 
                ':status' => ConstStatus::NORMAL
                )
                );
        }
        if (empty($model)) {
            return NULL;
        }
        return array(
            'price' => $model->price,
        );
    }
    
    
    /**
     * 更新票务信息
     * 
     * @param type $newsId 资讯id
     */
    public function upTicketM($newsId = NULL, $price = NULL)
    {
        $isNewIns = FALSE;
        $model = $this->find('t.news_id=:newsId', array(':newsId' => $newsId));
        if (empty($model)) {
            $model = new NewsTicketExtend();
            $model->news_id = $newsId;
            $model->create_time = date('Y-m-d H:i:s');
            $isNewIns = TRUE;
        }  else {
            $isNewIns = FALSE;
        }
        
        if (empty($model)) {
            return FALSE;
        }
        if (!empty($price)) {
            $model->price = $price;
        }
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNewIns) {
            return $model->save();
        }  else {
            return $model->update();
        }
    }
    
}

?>
