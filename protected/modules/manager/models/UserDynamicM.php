<?php

class UserDynamicM extends UserDynamic {
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserDynamicM the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取用户发布的动态数
     * 
     * @param type $uid 用户id
     */
    public function dynamicNumM($uid) 
    {
        return $this->count('author_id=:uid', array(':uid' => $uid));
    }
   
    
    /**
     * 基本信息
     * 
     * @param type $model 动态数据
     * @param type $id 动态id
     */
    public function profile($model = NULL, $id = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $dynamic = array();
        $dynamic['id'] = $model->id;
        $dynamic['at_user_num'] = DynamicCommentM::model()->commentUserNumM($model->id);
        $dynamic['at_num'] = DynamicCommentM::model()->commentNumM($model->id);
        $dynamic['publish_time'] = $model->create_time;
        $dynamic['content'] = $model->content;
        $dynamic['status'] = $model->status;
        $rst = DynamicImgMapM::model()->imgsM($model->id, 1, 9);
        $dynamic['imgs'] = $rst['imgs'];
        return $dynamic;
    }


    /**
     * 用户动态列表
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function dynamics($uid, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('author_id', $uid);
        
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $dynamics = array();
        foreach ($rst as $v) {
            $dynamic = $this->profile($v, NULL);
            if (empty($dynamic)) {
                continue;
            }
            array_push($dynamics, $dynamic);
        }
        
        return array(
            'total_num' => $count,
            'dynamics' => $dynamics,
        );
    }
    
}

?>
