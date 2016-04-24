<?php

class SystemMsgUserMapM extends SystemMsgUserMap {
    
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
     * 用户的系统消息
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function userSystemMsgsM($uid, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        
        $cr->with = 'fkMsg';
        $cr->compare('fkMsg.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $systemMsgs = array();
        foreach ($rst as $v) {
            $systemMsg = SystemMsgM::model()->profileM(NULL, $v->msg_id);
            if (empty($systemMsg)) {
                continue;
            }
            array_push($systemMsgs, $systemMsg);
        }
        return array(
            'total_num' => $count,
            'system_msgs' => $systemMsgs,
        );
    }
    
}

?>
