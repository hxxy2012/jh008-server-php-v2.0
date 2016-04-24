<?php

/**
 * This is the model class for table "user_pho_idfy".
 *
 * The followings are the available columns in table 'user_pho_idfy':
 * @property string $id
 * @property string $pho_num
 * @property string $salt
 * @property string $idfy_code
 * @property string $msg_content
 * @property string $create_time
 * @property string $invalid_time
 * @property string $pass_time
 * @property integer $send_status
 */
class UserPhoIdfy extends UserModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_pho_idfy';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('send_status', 'numerical', 'integerOnly'=>true),
			array('pho_num, idfy_code, msg_content', 'length', 'max'=>255),
			array('salt', 'length', 'max'=>6),
			array('create_time, invalid_time, pass_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, pho_num, salt, idfy_code, msg_content, create_time, invalid_time, pass_time, send_status', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户手机号验证id',
			'pho_num' => '手机号',
			'salt' => '哈希标识',
			'idfy_code' => '验证码',
			'msg_content' => '消息内容',
			'create_time' => '创建时间',
			'invalid_time' => '失效时间',
			'pass_time' => '验证通过时间',
			'send_status' => '发送状态',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPhoIdfy the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取最后一个验证码
     * 
     * @param type $phoneNum
     */
    public function getLast($phoneNum)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.pho_num', $phoneNum);
        $cr->compare('t.status', '>=' . ConstStatus::NORMAL);
        $cr->order = 't.id desc';
        return $this->find($cr);
    }
    
    
    /**
     * 生成手机验证码
     * 
     * @param type $phoneNum 手机号
     */
    public function makePhCode($phoneNum)
    {
        $model = $this->getLast($phoneNum);
        if (!empty($model) && strtotime($model->create_time) + 60 > time()) {
            //60s以内不得再次发送
            return Error::PHONE_CODE_SEND_FREQUENT;
        }
        $model = new UserPhoIdfy();
        $model->pho_num = $phoneNum;
        $model->salt = StrTool::getRandStr(6);
        $idfyCode = StrTool::getRandNumStr(6);
        $model->idfy_code = strtoupper(md5(strtoupper($model->salt) . strtoupper($idfyCode)));
        $model->msg_content = "尊敬的用户您好，您本次的验证码为：{$idfyCode}。";
        $nowTime = time();
        $model->create_time = date('Y-m-d H:i:s', $nowTime);
        //30分钟内有效
        $model->invalid_time = date('Y-m-d H:i:s', $nowTime + 60 * 30);
        $model->pass_time = NULL;
        $model->send_status = 0;
        if (!$model->save()) {
            return Error::OPERATION_EXCEPTION;
        }
        if (!Yii::app()->sms->send(
            array($phoneNum), 
            $model->msg_content
        )) {
            $model->send_status = 2;
            return Error::SMS_SEND_FAIL;
        }
        $model->send_status = 1;
        $model->update();
        return Error::NONE;
    }
    
    
    /**
     * 新注册发送手机验证码
     * @param type $phoneNum
     */
    public function newPhCode($phoneNum) 
    {
        if (UserInfo::model()->validUser($phoneNum)) {
            return Error::USERNAME_EXIST;
        }
        return $this->makePhCode($phoneNum);
    }
    
    
    /**
     * 已注册用户再次获取手机验证码（找回密码）
     * @param type $phoneNum
     */
    public function getPhCode($phoneNum) 
    {
        if (!UserInfo::model()->validUser($phoneNum)) {
            return Error::USERNAME_INVALID;
        }
        return $this->makePhCode($phoneNum);
    }
    
    
    /**
     * 新手机号验证Login
     * @param type $phoneNum
     * @param type $idfyCode
     */
    public function validNewPhCode($phoneNum, $idfyCode) 
    {
        $identity = new UserIdentity('phCodeNewAuthenticate', $phoneNum, $idfyCode);
        return UserInfo::model()->login($identity, 60 * 30);
    }
    
    
    /**
     * 已注册的手机号验证Login
     * @param type $phoneNum
     * @param type $idfyCode
     */
    public function validHasPhCode($phoneNum, $idfyCode) 
    {
        $identity = new UserIdentity('phCodeHasAuthenticate', $phoneNum, $idfyCode);
        return UserInfo::model()->login($identity);
    }
    
}
