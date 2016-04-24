<?php

/**
 * This is the model class for table "business_info".
 *
 * The followings are the available columns in table 'business_info':
 * @property string $id
 * @property string $u_name
 * @property string $u_pass
 * @property string $name
 * @property integer $status
 * @property string $creat_time
 *
 * The followings are the available model relations:
 * @property BusinessOperateLog[] $businessOperateLogs
 */
class BusinessInfo extends BusinessModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'business_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_name, u_pass, creat_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_name, u_pass', 'length', 'max'=>32),
			array('name', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_name, u_pass, name, status, creat_time', 'safe', 'on'=>'search'),
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
			//'businessOperateLogs' => array(self::HAS_MANY, 'BusinessOperateLog', 'op_ter'),
            
            'fkLogoImg' => array(self::HAS_ONE, 'BusinessLogoImgMap', 'b_id', 'on' => 'fkLogoImg.status=1'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '商家id',
			'u_name' => '用户名',
			'u_pass' => '用户密码',
			'name' => '商户名称',
			'status' => '状态',
			'creat_time' => '创建时间',
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
		$criteria->compare('u_pass',$this->u_pass,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('creat_time',$this->creat_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BusinessInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    

    /**
     * 注册
     * @param type $uName
     * @param type $uPass
     */
    public function regist($model, $uName, $uPass) 
    {
        $user = $this->find('u_name=:uName', array('uName' => $uName));
        if (!empty($user)) {
            return FALSE;
        }
        $model->u_name = $uName;
        $model->salt = StrTool::getRandStr(6);
        $model->u_pass = md5($model->salt . $uPass);
        $model->status = ConstStatus::NORMAL;
        $model->creat_time = date('Y-m-d H:i:s', time());
        $model->last_login_time = date('Y-m-d H:i:s', time());
        return $model->save();
    }
    
    
    /**
     * 登录
     * @param type $uName
     * @param type $uPass
     * @param type $rememberMe
     */
    public function login($uName, $uPass, $rememberMe=FALSE) {
        $form = new BusinessLoginForm;
        $form->u_name = $uName;
        $form->u_pass = $uPass;
        $form->rememberMe = $rememberMe;
        return $form->validate() && $form->login();
    }
    
    
    /**
     * 登录后获取的基础商家信息
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
            'name' => $this->name,
            'address' => $this->address,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'contact_descri' => $this->contact_descri,
            'logo_img_url' => BusinessLogoImgMap::model()->getCurImgUrl($this->id),
        );
    }
    
    
    /**
     * 商家的资料
     * @param type $bid
     */
    public function getInfo($bid) {
        $model = $this->findByPk($bid);
        return array(
            'id' => $model->id,
            'u_name' => $model->u_name,
            'name' => $model->name,
            'address' => $model->address,
            'contact_phone' => $model->contact_phone,
            'contact_email' => $model->contact_email,
            'contact_descri' => $model->contact_descri,
            'logo_img_url' => BusinessLogoImgMap::model()->getCurImgUrl($model->id),
        );
    }
    
    
    /**
     * 删除
     * @param type $bid
     * @return boolean
     */
    public function del($bid)
    {
        $model = $this->findByPk($bid);
        if (empty($model)) {
            return FALSE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
    
    /**
     * 获取商家列表
     */
    public function getUsers($keyWords, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        $cr->with = 'fkLogoImg.fkImg';
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('t.u_name', $keyWords, TRUE);
            $crs->compare('t.name', $keyWords, TRUE);
            $cr->mergeWith($crs);
        }
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', ConstStatus::NORMAL);
        }
        $count = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $businesses = array();
        foreach ($rst as $v) {
            $business = array();
            $business['id'] = $v->id;
            $business['u_name'] = $v->u_name;
            $business['name'] = $v->name;
            $business['address'] = $v->address;
            $business['contact_phone'] = $v->contact_phone;
            $business['contact_email'] = $v->contact_email;
            $business['contact_descri'] = $v->contact_descri;
            if (!empty($v->fkLogoImg)) {
                $business['logo_img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkLogoImg->fkImg->img_url);
            }
            array_push($businesses, $business);
        }
        
        return array(
            'total_num' => $count,
            'businesses' => $businesses,
        );
    }
    
}
