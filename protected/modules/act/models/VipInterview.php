<?php

/**
 * This is the model class for table "vip_interview".
 *
 * The followings are the available columns in table 'vip_interview':
 * @property string $id
 * @property string $u_id
 * @property string $news_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 * @property NewsInfo $news
 */
class VipInterview extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vip_interview';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, news_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('u_id, news_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, news_id, status, create_time', 'safe', 'on'=>'search'),
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
			'id' => '达人专访关联id',
			'u_id' => '用户id',
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
		$criteria->compare('u_id',$this->u_id,true);
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
	 * @return VipInterview the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 达人的专访
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $currUid 当前用户id
     */
    public function news($uid, $page, $size, $currUid)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        
        $cr->with = 'fkNews';
        $cr->compare('fkNews.status', ConstActStatus::PUBLISHING);
        $count = $this->count($cr);
        
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);

        $news = array();
        foreach ($rst as $v) {
            $newsInfo = NewsInfo::model()->profile(NULL, $v->news_id, $currUid);
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
