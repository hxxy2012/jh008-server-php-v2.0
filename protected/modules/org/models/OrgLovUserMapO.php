<?php

class OrgLovUserMapO extends OrgLovUserMap {
    
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
     * 社团关注者
     * 
     * @param type $orgId 社团id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function lovUsersO($orgId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.org_id', $orgId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfoO::model()->profileO($v->u_id, NULL);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
}

?>
