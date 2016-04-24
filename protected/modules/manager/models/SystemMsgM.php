<?php

class SystemMsgM extends SystemMsg {
    
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
     * @param type $model 系统消息数据
     * @param type $msgId 系统消息id
     */
    public function profileM($model = NULL, $msgId = NULL) 
    {
        if (empty($model)) {
            $model = $this->findByPk($msgId);
        }
        if (empty($model)) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'content' => $model->content,
            'status' => $model->status,
            'create_time' => $model->create_time,
        );
    }
    
    
    /**
     * 
     * @param type $model 系统消息数据
     * @param type $content 内容
     */
    public function addM($model = NULL, $content = NULL)
    {
        if (empty($model)) {
            $model = new SystemMsg();
        }
        if (!empty($content)) {
            $model->content = $content;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
}

?>
