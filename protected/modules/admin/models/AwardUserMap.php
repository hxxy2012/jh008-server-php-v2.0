<?php

/**
 * This is the model class for table "award_user_map".
 *
 * The followings are the available columns in table 'award_user_map':
 * @property string $id
 * @property string $award_id
 * @property string $u_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property AwardInfo $award
 * @property UserInfo $u
 */
class AwardUserMap extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'award_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('award_id, u_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('award_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, award_id, u_id, status, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			//'award' => array(self::BELONGS_TO, 'AwardInfo', 'award_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkUser' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '抽奖奖项和中奖用户关联id',
			'award_id' => '奖项id',
			'u_id' => '中奖用户id',
			'status' => '状态',
			'create_time' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('award_id',$this->award_id,true);
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AwardUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加活动抽奖方案的奖项和中奖者关联
     */
    public function add()
    {
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s');
        return $this->save();
    }
    
    
    /**
     * 获取活动抽奖方案奖项的中奖者列表
     * @param type $awardId
     */
    public function getUsers($awardId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.award_id', $awardId);
        $cr->compare('t.status', '<>' . ConstStatus::DELETE);
        
        $cr->with = array('fkUser.fkHeadImg.fkImg');
        $cr->compare('fkUser.status', '<>' . ConstStatus::DELETE);
        
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user['id'] = $v->fkUser->id;
            $userExtend = UserInfoExtend::model()->fullProfile($v->fkUser->id);
            if (!empty($userExtend)) {
                array_merge($user, $userExtend);
            }
            //$user['nick_name'] = $v->fkUser->nick_name;
            //$user['sex'] = $v->fkUser->sex;
            //$user['birth'] = $v->fkUser->birth;
            //$user['address'] = $v->fkUser->address;
            //$user['email'] = $v->fkUser->email;
            //$user['real_name'] = $v->fkUser->real_name;
            //$user['contact_qq'] = $v->fkUser->contact_qq;
            //$user['contact_phone'] = $v->fkUser->contact_phone;
            if (!empty($v->fkUser->fkHeadImg)) {
                $user['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkUser->fkHeadImg->fkImg->img_url);
            }
            $user['status'] = $v->fkUser->status;
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users
        );
    }
    
    
    /**
     * 产生一个中奖者
     * @param type $awardId
     */
    public function makeAwardUser($awardId, $startTime, $endTime, $includeWinners, $needUserInfo)
    {
        $award = AwardInfo::model()->findByPk($awardId);
        $prize = PrizeAwardMap::model()->getPrize($award->id);
        $act = ActPrizeMap::model()->getAct($prize->id);
        $uids = ActCheckinAdmin::model()->getUserIds($act->id, $startTime, $endTime, $needUserInfo);
        
        if ($includeWinners) {
            //排除当前奖项已中奖的用户
            $userIds = $this->getUserIds($award->id);
            $uids = array_diff($uids, $userIds);
        }  else {
            //排除该方案中已中奖的用户id
            $rst = PrizeAwardMap::model()->findAll(
                    'prize_id=:prizeId and status<>-1', 
                    array(
                        ':prizeId' => $prize->id
                    ));
            $hasUids = array();
            foreach ($rst as $v) {
                $userIds = $this->getUserIds($v->award_id);
                array_merge($hasUids, $userIds);
            }
            $uids = array_diff($uids, $hasUids);
        }
        
        if (empty($uids)) {
            return NULL;
        }
        
        //随机抽取一个用户作为参考中奖用户
        $uid = $uids[rand(0, count($uids) - 1)];
        
        return UserInfoAdmin::model()->getInfo($uid);
    }
    
    
    /**
     * 获取某奖项的已中奖用户id
     * @param type $awardId
     */
    public function getUserIds($awardId)
    {
        $users = $this->findAll(
                'award_id=:awardId and status<>-1', 
                array(
                    ':awardId' => $awardId
                ));
        $uids = array();
        foreach ($users as $v) {
            array_push($uids, $v->id);
        }
        return $uids;
    }
    
}
