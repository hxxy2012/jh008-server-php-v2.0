<?php

class SystemMsgUserTaskM extends SystemMsgUserTask {
    
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
     * 添加系统消息发送任务
     * 
     * @param type $content 内容
     * @param array $toUserIds 需要发送的用户id数组
     */
    public function addM($content, array $toUserIds)
    {
        $msgModel = new SystemMsg();
        $rst = SystemMsgM::model()->addM($msgModel, $content);
        if (!$rst) {
            return FALSE;
        }
        
        $model = new SystemMsgUserTask();
        $model->msg_id = $msgModel->id;
        $model->user_ids = json_encode($toUserIds);
        $model->last_max_user_id = NULL;
        $model->status = ConstStatus::NORMAL;
        return $model->save();
    }
    
}

?>
