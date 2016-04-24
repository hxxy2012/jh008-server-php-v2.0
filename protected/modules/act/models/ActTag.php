<?php

/**
 * This is the model class for table "act_tag".
 *
 * The followings are the available columns in table 'act_tag':
 * @property string $id
 * @property string $name
 * @property integer $status
 * @property string $create_time
 */
class ActTag extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_tag';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>16),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, status, create_time', 'safe', 'on'=>'search'),
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
			'id' => '活动标签id',
			'name' => '名称',
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
		$criteria->compare('name',$this->name,true);
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
	 * @return ActTag the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    

    /**
     * 活动分类
     * 
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function tags($page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('status', ConstStatus::NORMAL);
        $count = $this->count($cr);
        
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $tags = array();
        foreach ($rst as $v) {
            $tag = $this->profile(NULL, $v);
            array_push($tags, $tag);
        }
        
        return array(
            'total_num' => $count,
            'tags' => $tags
        );
    }
    
    
    /**
     * 标签基本信息
     * 
     * @param type $id 标签id
     * @param type $model
     */
    public function profile($id, $model = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        if (empty($model)) {
            return NULL;
        }
        $tag = array();
        $tag['id'] = $model->id;
        $tag['name'] = $model->name;
        return $tag;
    }
    
}
