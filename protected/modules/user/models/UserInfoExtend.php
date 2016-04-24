<?php

/**
 * This is the model class for table "user_info_extend".
 *
 * The followings are the available columns in table 'user_info_extend':
 * @property string $id
 * @property string $u_id
 * @property string $nick_name
 * @property integer $sex
 * @property string $birth
 * @property string $intro
 * @property string $address
 * @property string $email
 * @property string $real_name
 * @property string $contact_qq
 * @property string $contact_phone
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 */
class UserInfoExtend extends UserModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_info_extend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id', 'required'),
			array('sex', 'numerical', 'integerOnly'=>true),
			array('u_id', 'length', 'max'=>10),
			array('nick_name, email, real_name, contact_qq, contact_phone', 'length', 'max'=>32),
			array('intro', 'length', 'max'=>240),
			array('address', 'length', 'max'=>64),
			array('birth', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, nick_name, sex, birth, intro, address, email, real_name, contact_qq, contact_phone', 'safe', 'on'=>'search'),
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
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户扩展信息关联id',
			'u_id' => '用户id',
			'nick_name' => '昵称',
			'sex' => '用户性别：1男，2女',
			'birth' => '用户生日',
			'intro' => '个人简介',
			'address' => '地址',
			'email' => '邮箱',
			'real_name' => '真实姓名',
			'contact_qq' => '联系qq',
			'contact_phone' => '联系电话',
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
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('nick_name',$this->nick_name,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('birth',$this->birth,true);
		$criteria->compare('intro',$this->intro,true);
		$criteria->compare('address',$this->address,true);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('real_name',$this->real_name,true);
		$criteria->compare('contact_qq',$this->contact_qq,true);
		$criteria->compare('contact_phone',$this->contact_phone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserInfoExtend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取用户扩展信息model
     * 
     * @param type $uid
     */
    public function get($uid)
    {
        return $this->find('t.u_id=:uid', array(':uid' => $uid));
    }


    /**
     * 基本扩展信息
     * 
     * @param type $uid 用户id
     */
    public function profile($uid)
    {
        $model = $this->find('u_id=:uid', array(':uid' => $uid));
        if (empty($model)) {
            return array();
        }
        return array(
            'nick_name' => $model->nick_name,
            'sex' => $model->sex,
            'birth' => $model->birth,
            'intro' => $model->intro,
            'hobby' => $model->hobby,
            'last_login_platform' => $model->last_login_platform,
            'baidu_user_id' => $model->baidu_user_id,
            'baidu_channel_id' => $model->baidu_channel_id,
        );
    }


    /**
     * 全部扩展信息
     * @param type $uid 用户id
     */
    public function fullProfile($uid) 
    {
        $model = $this->find('u_id=:uid', array(':uid' => $uid));
        if (empty($model)) {
            return array();
        }
        return array(
            'nick_name' => $model->nick_name,
            'sex' => $model->sex,
            'birth' => $model->birth,
            'intro' => $model->intro,
            'address' => $model->address,
            'email' => $model->email,
            'real_name' => $model->real_name,
            'contact_qq' => $model->contact_qq,
            'contact_phone' => $model->contact_phone,
            'hobby' => $model->hobby,
            'last_login_platform' => $model->last_login_platform,
            'baidu_user_id' => $model->baidu_user_id,
            'baidu_channel_id' => $model->baidu_channel_id,
        );
    }
    
    
    /**
     * 删除同一推送参数的其他用户的推送参数
     * 
     * @param type $uid
     * @param type $baidu_user_id
     * @param type $baidu_channel_id
     */
    public function delOtherPushParm($uid, $baidu_user_id, $baidu_channel_id)
    {
        return $this->updateAll(
                array(
                    'baidu_user_id' => NULL,
                    'baidu_channel_id' => NULL,
                ), 
                'baidu_user_id=:baidu_user_id and baidu_channel_id=:baidu_channel_id and u_id<>:uid', 
                array(
                    ':baidu_user_id' => $baidu_user_id,
                    ':baidu_channel_id' => $baidu_channel_id,
                    ':uid' => $uid,
                )
                );
    }
    
}
