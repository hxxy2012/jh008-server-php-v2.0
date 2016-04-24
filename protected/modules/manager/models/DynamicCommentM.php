<?php

class DynamicCommentM extends DynamicComment {
    
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
     * 获取动态的评论数
     * 
     * @param type $dynamicId 动态id
     */
    public function commentNumM($dynamicId) 
    {
        return $this->count('dynamic_id=:dynamicId', array(':dynamicId' => $dynamicId));
    }

    
    /**
     * 获取动态评论的用户数
     * 
     * @param type $dynamicId 动态id
     */
    public function commentUserNumM($dynamicId) 
    {
        $cr = new CDbCriteria();
        $cr->select = 't.author_id';
        $cr->compare('t.dynamic_id', $dynamicId);
        return $this->count($cr);
    }
    
}

?>
