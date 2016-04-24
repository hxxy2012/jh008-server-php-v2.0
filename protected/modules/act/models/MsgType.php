<?php

/**
 * This is the model class for table "msg_type".
 *
 * The followings are the available columns in table 'msg_type':
 * @property string $id
 * @property string $name
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property MsgInfo[] $msgInfos
 */
class MsgType extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msg_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, status', 'safe', 'on'=>'search'),
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
			//'msgInfos' => array(self::HAS_MANY, 'MsgInfo', 'type_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '消息类型id',
			'name' => '名称',
			'status' => '状态:-1删除，0正常',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加消息类型
     */
    public function add()
    {
        $this->status = ConstStatus::NORMAL;
        return $this->save();
    }
    
    
    /**
     * 修改消息类型
     */
    public function updateType() 
    {
        return $this->update();
    }
    
    
    /**
     * 删除消息类型
     */
    public function del()
    {
        $this->status = ConstStatus::DELETE;
        return $this->update();
    }
    
    
    /**
     * 获取消息类型
     * @param type $id
     */
    public function getType($id) 
    {
        $model = $this->findByPk($id);
        return array(
            'id' => $model->id,
            'name' => $model->name,
            'status' => $model->status,
        );
    }
    
    
    /**
     * 搜索消息类型
     * @param type $keyWords
     * @param type $isDel
     */
    public function searchTypes($keyWords, $isDel = FALSE)
    {
        $cr = new CDbCriteria();
        if (!empty($keyWords)) {
            $cr->compare('name', $keyWords, TRUE);
        }
        if ($isDel) {
            $cr->compare('status', ConstStatus::DELETE);
        }  else {
            $cr->compare('status', '<>' . ConstStatus::DELETE);
        }
        $count = $this->count($cr);
        $rst = $this->findAll($cr);
        
        $types = array();
        foreach ($rst as $v) {
            $type = array();
            $type['id'] = $v->id;
            $type['name'] = $v->name;
            $type['status'] = $v->status;
            array_push($types, $type);
        }
        
        return array(
            'total_num' => $count,
            'types' => $types,
        );
    }
    
}
