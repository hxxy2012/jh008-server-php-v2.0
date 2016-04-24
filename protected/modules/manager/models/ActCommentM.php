<?php

class ActCommentM extends ActComment {
    
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
     * 获取活动被评论次数
     * 
     * @param type $actId 活动id
     */
    public function commentNumM($actId) 
    {
        return $this->count('act_id=:actId', array(':actId' => $actId));
    }
    
    
    /**
     * 评论列表
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function commentsM($actId, $page, $size) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $comments = array();
        foreach ($rst as $v) {
            $comment = array();
            $comment['id'] = $v->id;
            $comment['author_id'] = $v->author_id;
            $comment['content'] = $v->content;
            $comment['status'] = $v->status;
            $comment['create_time'] = $v->create_time;
            array_push($comments, $comment);
        }
        
        return array(
            'total_num' => $count,
            'comments' => $comments,
        );
    }
    
}

?>
