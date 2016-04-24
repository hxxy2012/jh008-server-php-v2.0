<?php

/**
 * This is the model class for table "vip_search".
 *
 * The followings are the available columns in table 'vip_search':
 * @property string $id
 * @property string $vip_id
 * @property integer $city_k
 * @property integer $tag_k
 * @property integer $sex_k
 * @property integer $user_tag_k
 * @property string $key_words_k
 * @property integer $status
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property UserInfo $vip
 */
class VipSearch extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vip_search';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('vip_id, modify_time', 'required'),
			array('city_k, tag_k, sex_k, user_tag_k, status', 'numerical', 'integerOnly'=>true),
			array('vip_id', 'length', 'max'=>10),
			array('key_words_k', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, vip_id, city_k, tag_k, sex_k, user_tag_k, key_words_k, status, modify_time', 'safe', 'on'=>'search'),
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
			//'vip' => array(self::BELONGS_TO, 'UserInfo', 'vip_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'vip搜索关联id',
			'vip_id' => 'vip用户id',
			'city_k' => '城市搜索项',
			'tag_k' => '类别标签搜索项',
			'sex_k' => '性别',
			'user_tag_k' => '达人标签搜索项',
			'key_words_k' => '关键字搜索项',
			'status' => '状态',
			'modify_time' => '修改时间',
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
		$criteria->compare('vip_id',$this->vip_id,true);
		$criteria->compare('city_k',$this->city_k);
		$criteria->compare('tag_k',$this->tag_k);
		$criteria->compare('sex_k',$this->sex_k);
		$criteria->compare('user_tag_k',$this->user_tag_k);
		$criteria->compare('key_words_k',$this->key_words_k,true);
		$criteria->compare('status',$this->status);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return VipSearch the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 达人搜索
     * 
     * @param type $cityId 城市id
     * @param type $tagId 类别标签id
     * @param type $sex 性别id
     * @param type $vipTagId 达人标签id
     * @param type $keyWords 关键字
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function vips($cityId, $tagId = NULL, $sex = NULL, $vipTagId = NULL, $keyWords = NULL, $page, $size, $currUid = NULL) 
    {
        if (!BinaryTool::validPos($cityId) || !BinaryTool::validPos($tagId) || !BinaryTool::validPos($vipTagId)) {
            return array(
                'total_num' => 0,
                'users' => array(),
            );
        }
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);
        if (!empty($cityId)) {
            $cityK = BinaryTool::setOne(0, $cityId);
            $cr->addCondition('t.city_k&' . $cityK . '<>0');
        }
        if (!empty($tagId)) {
            $tagIdK = BinaryTool::setOne(0, $tagId);
            $cr->addCondition('t.tag_k&' . $tagIdK . '<>0');
        }
        if (!empty($sex)) {
            $cr->compare('t.sex_k', $sex);
        }
        if (!empty($vipTagId)) {
            $vipTagIdK = BinaryTool::setOne(0, $vipTagId);
            $cr->addCondition('t.user_tag_k&' . $vipTagIdK . '<>0');
        }
        if (!empty($keyWords)) {
            $cr->compare('t.key_words_k', $keyWords, TRUE);
        }
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $users = array();
        foreach ($rst as $v) {
            $user = UserInfo::model()->profile(NULL, $v->vip_id, $cityId, $currUid, NULL, TRUE, FALSE);
            if (empty($user)) {
                continue;
            }
            array_push($users, $user);
        }
        return array(
            'total_num' => $count,
            'users' => $users,
        );
    }
    
}
