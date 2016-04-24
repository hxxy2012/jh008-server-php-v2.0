<?php

class MsgRevUserMapAdmin extends MsgRevUserMap
{
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgRevUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
     /**
     * 获取某个用户的所有消息
     * @param type $uid
     */
    public function getUserMsgs($uid, $page, $size, $isDel = FALSE)
    {
        $criteria = new CDbCriteria();
        $criteria->with = 'fkMsg.fkType';
        $criteria->compare('t.u_id', $uid);
        if ($isDel) {
            $criteria->compare('t.status', ConstStatus::DELETE);
        }  else {
            $criteria->compare('t.status', '<>' . ConstStatus::DELETE);
        }
        $totalNum = $this->count($criteria);
        
        $criteria->offset = ($page - 1) * $size;
        $criteria->limit = $size;
        $rst = $this->findAll($criteria);
        
        $msgs = array();
        foreach ($rst as $k => $v) {
            $msg = array();
            $msg['id'] = $v->fkMsg->id;
            $msg['content'] = $v->fkMsg->content;
            $msg['filter'] = $v->fkMsg->filter;
            $msg['status'] = $v->status;
            $msg['create_time'] = $v->fkMsg->create_time;
            $msg['publish_time'] = $v->fkMsg->publish_time;
            if (!empty($v->fkMsg->fkType)) {
                $type = array();
                $type['id'] = $v->fkMsg->fkType->id;
                $type['name'] = $v->fkMsg->fkType->name;
                $msg['type'] = $type;
            }
            array_push($msgs, $msg);
        }
        return array(
            'total_num' => $totalNum,
            'msgs' => $msgs,
            );
    }
    
    
    /**
     * 获取消息的接收者列表
     * @param type $msgId
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function getMsgRevUsers($msgId, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.msg_id', $msgId);
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        }
        $count = $this->count($cr);
        
        $cr->with = array('fkUser.fkHeadImg.fkImg');
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = array();
            $user['id'] = $v->fkUser->id;
            $user['nick_name'] = $v->fkUser->nick_name;
            $user['sex'] = $v->fkUser->sex;
            $user['birth'] = $v->fkUser->birth;
            $user['address'] = $v->fkUser->address;
            $user['email'] = $v->fkUser->email;
            $user['real_name'] = $v->fkUser->real_name;
            $user['contact_qq'] = $v->fkUser->contact_qq;
            $user['contact_phone'] = $v->fkUser->contact_phone;
            if (!empty($v->fkUser->fkHeadImg)) {
                $user['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkUser->fkHeadImg->fkImg->img_url);
            }
            $user['status'] = $v->fkUser->status;
            array_push($users, $user);
        }
        
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
}

?>
