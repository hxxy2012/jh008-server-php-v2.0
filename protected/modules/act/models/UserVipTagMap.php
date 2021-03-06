<?php

/**
 * This is the model class for table "user_vip_tag_map".
 *
 * The followings are the available columns in table 'user_vip_tag_map':
 * @property string $id
 * @property string $u_id
 * @property string $tag_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property VipTag $tag
 */
class UserVipTagMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_vip_tag_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, tag_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_id, tag_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, tag_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'tag' => array(self::BELONGS_TO, 'VipTag', 'tag_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户标签关联id',
			'u_id' => '用户id',
			'tag_id' => '标签id',
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
		$criteria->compare('u_id',$this->u_id,true);
		$criteria->compare('tag_id',$this->tag_id,true);
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
	 * @return UserVipTagMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户相关的类别标签id
     * 
     * @param type $uid 用户id
     */
    public function tagIds($uid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->order = 't.id asc';
        $rst = $this->findAll($cr);
        
        $tagIds = array();
        foreach ($rst as $v) {
            array_push($tagIds, $v->tag_id);
        }
        return $tagIds;
    }
    
}
