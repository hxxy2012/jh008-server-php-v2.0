<?php

/**
 * This is the model class for table "admin_info".
 *
 * The followings are the available columns in table 'admin_info':
 * @property string $id
 * @property string $u_name
 * @property string $salt
 * @property string $u_pass
 * @property integer $status
 * @property string $create_time
 * @property string $last_login_time
 * @property string $nick_name
 *
 * The followings are the available model relations:
 * @property AdminHeadImgMap[] $adminHeadImgMaps
 * @property AdminOperateLog[] $adminOperateLogs
 * @property ImgUpAdminMap[] $imgUpAdminMaps
 * @property UpAdminMap[] $upAdminMaps
 */
class AdminInfo extends AdminModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'admin_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_name, salt, u_pass, create_time, last_login_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_name, u_pass, nick_name', 'length', 'max'=>32),
			array('salt', 'length', 'max'=>6),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_name, salt, u_pass, status, create_time, last_login_time, nick_name', 'safe', 'on'=>'search'),
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
			//'adminHeadImgMaps' => array(self::HAS_MANY, 'AdminHeadImgMap', 'a_id'),
			//'adminOperateLogs' => array(self::HAS_MANY, 'AdminOperateLog', 'op_ter'),
			//'imgUpAdminMaps' => array(self::HAS_MANY, 'ImgUpAdminMap', 'a_id'),
			//'upAdminMaps' => array(self::HAS_MANY, 'UpAdminMap', 'a_id'),
            
			'fkHeadImg' => array(self::HAS_ONE, 'AdminHeadImgMap', 'a_id', 'on' => 'fkHeadImg.status=1'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '管理员id',
			'u_name' => '用户名',
			'salt' => '哈希标识',
			'u_pass' => '用户密码',
			'status' => '状态',
			'create_time' => '创建时间',
			'last_login_time' => '最后登录时间',
			'nick_name' => '昵称',
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
		$criteria->compare('u_name',$this->u_name,true);
		$criteria->compare('salt',$this->salt,true);
		$criteria->compare('u_pass',$this->u_pass,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->creat_time,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('nick_name',$this->nick_name,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AdminInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 注册
     * @param type $uName
     * @param type $uPass
     * @param type $nickName
     */
    public function addUser($uName, $uPass) 
    {
        $admin = $this->find('u_name=:uName', array('uName' => $uName));
        if (!empty($admin)) {
            return FALSE;
        }
        $this->u_name = $uName;
        $this->salt = StrTool::getRandStr(6);
        $this->u_pass = md5($this->salt . $uPass);
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s', time());
        $this->last_login_time = date('Y-m-d H:i:s', time());
        return $this->save();
    }
    
    
    /**
     * 登录
     * @param type $uName
     * @param type $uPass
     * @param type $rememberMe
     */
    public function login($uName, $uPass, $rememberMe=FALSE) {
        $form = new AdminLoginForm;
        $form->u_name = $uName;
        $form->u_pass = $uPass;
        $form->rememberMe = $rememberMe;
        return $form->validate() && $form->login();
    }
    
    
    /**
     * 登录后获取的基础管理员信息
     */
    public function getLogUInfo() {
        return array(
            'id' => $this->id,
        );
    }
    
    
    /**
     * 自己的资料
     */
    public function getMyInfo() {
        return array(
            'id' => $this->id,
            'u_name' => $this->u_name,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'last_login_time' => $this->last_login_time,
            'nick_name' => $this->nick_name,
            'head_img_url' => AdminHeadImgMap::model()->getCurImgUrl($this->id),
        );
    }
    
    
    /**
     * 他人的资料
     */
    public function getUserInfo($id) {
        $r = $this->findByPk($id);
        return array(
            'id' => $r->id,
            'u_name' => $r->u_name,
            'status' => $r->status,
            'create_time' => $r->create_time,
            'last_login_time' => $r->last_login_time,
            'nick_name' => $r->nick_name,
            'head_img_url' => AdminHeadImgMap::model()->getCurImgUrl($r->id),
        );
    }
    
    
    /**
     * 获取管理员列表
     */
    public function getUsers($isDel = FALSE)
    {
        $cr = new CDbCriteria();
        $cr->with = 'fkHeadImg.fkImg';
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', ConstStatus::NORMAL);
        }
        $rst = $this->findAll($cr);
        
        $admins = array();
        foreach ($rst as $v) {
            $admin = array();
            $admin['id'] = $v->id;
            $admin['u_name'] = $v->u_name;
            $admin['status'] = $v->status;
            $admin['create_time'] = $v->create_time;
            $admin['last_login_time'] = $v->last_login_time;
            $admin['nick_name'] = $v->nick_name;
            if (!empty($v->fkHeadImg) && !empty($v->fkHeadImg->fkImg)) {
                $admin['head_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkHeadImg->fkImg->img_url);
            }
            array_push($admins, $admin);
        }
        return $admins;
    }
    
    
    /**
     * 删除用户
     * @param type $id
     */
    public function delUser($id) 
    {
        $model = $this->findByPk($id);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}
