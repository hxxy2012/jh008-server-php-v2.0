<?php

/**
 * This is the model class for table "app_info".
 *
 * The followings are the available columns in table 'app_info':
 * @property string $id
 * @property string $type
 * @property string $code
 * @property string $name
 * @property string $descri
 * @property string $up_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UpInfo $up
 */
class AppInfo extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, code, name, up_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('type, code, up_id', 'length', 'max'=>10),
			array('name', 'length', 'max'=>32),
			array('descri', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, type, code, name, descri, up_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'up' => array(self::BELONGS_TO, 'UpInfo', 'up_id'),
            
            'fkUp' => array(self::BELONGS_TO, 'UpInfo', 'up_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '版本id',
			'type' => '类型：1安卓',
			'code' => '版本号',
			'name' => '版本名称',
			'descri' => '版本描述',
			'up_id' => '安装文件id',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('descri',$this->descri,true);
		$criteria->compare('up_id',$this->up_id,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->creat_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AppInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取最后的版本信息
     * @param type $type
     */
    public function getLast($type)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('type', $type);
        $criteria->order = 'code desc, id desc';
        $model = $this->find($criteria);
        if (empty($model)) {
            return array();
        }
        $app = array();
        $app['code'] = $model->code;
        $app['name'] = $model->name;
        $app['descri'] = $model->descri;
        $file = UpInfo::model()->profile($model->up_id);
        if (!empty($file)) {
            $app['ver_url'] = $file['file_url'];
        }
        return $app;
    }
    
    
    /**
     * 获取最后的版本url
     * @param type $type
     */
    public function getLastUrl($type)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('type', $type);
        $criteria->order = 'code desc, id desc';
        $model = $this->find($criteria);
        if (empty($model)) {
            return NULL;
        }
        $file = UpInfo::model()->profile($model->up_id);
        if (empty($file)) {
            return NULL;
        }
        return $file['file_url'];
    }
    
    
    /**
     * 添加版本
     */
    public function ins()
    {
        $this->status = ConstStatus::NORMAL;
        $this->create_time = date('Y-m-d H:i:s', time());
        return $this->save();
    }
    
    
    /**
     * 修改版本
     */
    public function updateApp()
    {
        return $this->update();
    }
    
    
    /**
     * 删除版本
     */
    public function del()
    {
        $this->status = ConstStatus::DELETE;
        return $this->update();
    }
    
    
    /**
     * 搜索app版本列表
     * @param type $type
     * @param type $keyWords
     * @param type $page
     * @param type $size
     * @param type $isDel
     */
    public function searchApps($type, $keyWords, $page, $size, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        if (!empty($type)) {
            $cr->compare('t.type', $type);
        }
        if (!empty($keyWords)) {
            $crs = new CDbCriteria();
            $crs->compare('t.name', $keyWords, TRUE, 'OR');
            $crs->compare('t.descri', $keyWords, TRUE, 'OR');
            $cr->mergeWith($crs);
        }
        if ($isDel) {
            $cr->compare('t.status', ConstStatus::DELETE);
        }  else {
            $cr->compare('t.status', ConstStatus::NORMAL);
        }
        $cr->with = 'fkUp';
        $count = $this->count($cr);
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $cr->order = 't.id desc';
        $rst = $this->findAll($cr);
        
        $apps = array();
        foreach ($rst as $v) {
            $app = array();
            $app['id'] = $v->id;
            $app['type'] = $v->type;
            $app['code'] = $v->code;
            $app['name'] = $v->name;
            $app['descri'] = $v->descri;
            if (!empty($v->fkUp)) {
                $app['app_url'] = Yii::app()->fileUpload->getDownUrl($v->fkUp->url);                
            }
            $app['status'] = $v->status;
            $app['create_time'] = $v->create_time;
            array_push($apps, $app);
        }
        return array(
            'total_num' => $count,
            'apps' => $apps,
        );
    }
    
}
