<?php

/**
 * This is the model class for table "city_manager".
 *
 * The followings are the available columns in table 'city_manager':
 * @property string $id
 * @property string $u_name
 * @property string $salt
 * @property string $u_pass
 * @property integer $status
 * @property string $create_time
 * @property string $last_login_time
 * @property string $name
 */
class CityManager extends ManagerModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'city_manager';
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
			array('u_name, name', 'length', 'max'=>16),
			array('salt', 'length', 'max'=>6),
			array('u_pass', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_name, salt, u_pass, status, create_time, last_login_time, name', 'safe', 'on'=>'search'),
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
			'id' => '管理员id',
			'u_name' => '用户名',
			'salt' => '哈希标识',
			'u_pass' => '密码',
			'status' => '状态',
			'create_time' => '创建时间',
			'last_login_time' => '最后一次登录时间',
			'name' => '名称',
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

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CityManager the static model class
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
        $form = new CityManagerLoginForm();
        $form->u_name = $uName;
        $form->u_pass = $uPass;
        $form->rememberMe = $rememberMe;
        return $form->validate() && $form->login();
    }
    
    
    /**
     * 添加城市管理员
     * 
     * @param type $uName 用户登录名
     * @param type $uPass 用户密码
     * @param type $name 名称
     * @param type $cityId 城市id
     * @param type $type 类型
     */
    public function add($model = NULL, $uName = NULL, $uPass = NULL, $name = NULL, $cityId, $type)
    {
        if (!ManagerCityMap::model()->cityCanAdd($cityId, $type)) {
            return FALSE;
        }
        
        if (empty($model)) {
            $model = new CityManager();
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
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->last_login_time = date('Y-m-d H:i:s');
        $r = $model->save();
        if ($r) {
            ManagerCityMap::model()->add($model->id, $cityId, $type);
        }
        return $r;
    }
    

    /**
     * 修改指定城市管理员的信息
     * 
     * @param type $model 数据模型
     * @param type $mid 管理员id
     * @param type $uPass 用户密码
     * @param type $name 名称
     */
    public function up($model = NULL, $mid = NULL, $uPass = NULL, $name = NULL)
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
            'status' => $model->status,
            'create_time' => $model->create_time,
            'last_login_time' => $model->last_login_time,
        );
    }
    
}
