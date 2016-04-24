<?php

/**
 * This is the model class for table "act_news_map".
 *
 * The followings are the available columns in table 'act_news_map':
 * @property string $id
 * @property string $act_id
 * @property string $news_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property NewsInfo $news
 */
class ActNewsMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_news_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, news_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, news_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, news_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'act' => array(self::BELONGS_TO, 'ActInfo', 'act_id'),
			//'news' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
            
            'fkNews' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动资讯关联id',
			'act_id' => '活动id',
			'news_id' => '资讯id',
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
		$criteria->compare('act_id',$this->act_id,true);
		$criteria->compare('news_id',$this->news_id,true);
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
	 * @return ActNewsMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 活动相关资讯
     * 
     * @param type $actId 活动id
     * @param type $typeId 资讯类型id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function news($actId, $typeId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        
        $cr->with = 'fkNews';
        $cr->compare('fkNews.type_id', $typeId);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $news = array();
        foreach ($rst as $k => $v) {
            $newsInfo = NewsInfo::model()->profile(NULL, $v->news_id);
            if (empty($newsInfo)) {
                continue;
            }
            array_push($news, $newsInfo);
        }
        return array(
            'total_num' => $count,
            'news' => $news
            );
    }
    
}
