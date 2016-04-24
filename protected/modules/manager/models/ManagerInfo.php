<?php

/**
 * This is the model class for table "manager_info".
 *
 * The followings are the available columns in table 'manager_info':
 * @property string $id
 * @property string $u_name
 * @property string $salt
 * @property string $u_pass
 * @property integer $status
 * @property string $create_time
 * @property string $last_login_time
 * @property string $name
 * @property string $type
 *
 * The followings are the available model relations:
 * @property ManagerCityMap[] $managerCityMaps
 */
class ManagerInfo extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'manager_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_name, salt, u_pass, create_time, last_login_time, type', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_name, name', 'length', 'max'=>16),
			array('salt', 'length', 'max'=>6),
			array('u_pass', 'length', 'max'=>32),
			array('type', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_name, salt, u_pass, status, create_time, last_login_time, name, type', 'safe', 'on'=>'search'),
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
			'managerCityMaps' => array(self::HAS_MANY, 'ManagerCityMap', 'm_id'),
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
			'u_pass' => '密码',
			'status' => '状态',
			'create_time' => '创建时间',
			'last_login_time' => '最后一次登录时间',
			'name' => '名称',
			'type' => '类型：1超级管理员，2,3,',
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
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('last_login_time',$this->last_login_time,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('type',$this->type,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ManagerInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 登录
     * @param type $uName
     * @param type $uPass
     * @param type $rememberMe
     */
    public function login($uName, $uPass, $rememberMe=FALSE) {
        $form = new ManagerLoginForm();
        $form->u_name = $uName;
        $form->u_pass = $uPass;
        $form->rememberMe = $rememberMe;
        return $form->validate() && $form->login();
    }
    
    
    /**
     * 添加管理员
     * 
     * @param type $uName 用户登录名
     * @param type $uPass 用户密码
     * @param type $name 名称
     */
    public function add($model = NULL, $uName = NULL, $uPass = NULL, $name = NULL, $type = NULL)
    {
        if (empty($model)) {
            $model = new ManagerInfo();
        }
        if (!empty($uName)) {
            $model->u_name = $uName;
        }
        $model->salt = StrTool::getRandStr(6);
        if (!empty($uPass)) {
            $model->u_pass = md5($model->salt . $uPass);
        }  else {
            $model->u_pass = md5($model->salt . $model->u_pass);
        }
        
        $manager = $this->find('u_name=:uName', array('uName' => $model->u_name));
        if (!empty($manager)) {
            return FALSE;
        }
        
        if (!empty($name)) {
            $model->name = $name;
        }
        if (!empty($type)) {
            $model->type = $type;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->last_login_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    

    /**
     * 修改指定管理员的信息
     * 
     * @param type $model 数据模型
     * @param type $mid 管理员id
     * @param type $uPass 用户密码
     * @param type $name 名称
     * @param type $type 类型
     */
    public function up($model = NULL, $mid = NULL, $uPass = NULL, $name = NULL, $type = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($mid);
        }
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return FALSE;
        }
        if (!empty($uPass)) {
            $model->u_pass = md5($model->salt . $uPass);
        }
        if (!empty($name)) {
            $model->name = $name;
        }
        if (!empty($type)) {
            $model->type = $type;
        }
        return $model->update();
    }

    
    /**
     * 基本信息
     * 
     * @param type $mid 管理员id
     */
    public function profile($mid)
    {
        $model = $this->findByPk($mid);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return array(
            'id' => $model->id,
            'u_name' => $model->u_name,
            'name' => $model->name,
            'type' => $model->type,
            'status' => $model->status,
            'create_time' => $model->create_time,
            'last_login_time' => $model->last_login_time,
        );
    }



    /**
     * 管理员列表
     * 
     * @param type $type 指定类型
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function managers($type = NULL, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        if (empty($type) || $type == ConstManagerStatus::SUPER_MANAGER) {
            $cr->compare('t.type', '<>' . ConstManagerStatus::SUPER_MANAGER);
        }  else {
            $cr->compare('t.type', $type);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $managers = array();
        foreach ($rst as $v) {
            $manager = array();
            $manager['id'] = $v->id;
            $manager['u_name'] = $v->u_name;
            $manager['name'] = $v->name;
            $manager['type'] = $v->type;
            $manager['status'] = $v->status;
            $manager['create_time'] = $v->create_time;
            $manager['last_login_time'] = $v->last_login_time;
            array_push($managers, $manager);
        }
        
        return array(
            'total_num' => $count,
            'managers' => $managers
        );
    }
    
}
