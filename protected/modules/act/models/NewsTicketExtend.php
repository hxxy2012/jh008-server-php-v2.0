<?php

/**
 * This is the model class for table "news_ticket_extend".
 *
 * The followings are the available columns in table 'news_ticket_extend':
 * @property string $id
 * @property string $news_id
 * @property double $price
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property NewsInfo $news
 */
class NewsTicketExtend extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'news_ticket_extend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('news_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('price', 'numerical'),
			array('news_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, news_id, price, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			'news' => array(self::BELONGS_TO, 'NewsInfo', 'news_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '资讯票务扩展id',
			'news_id' => '资讯id',
			'price' => '价格',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '更新时间',
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
		$criteria->compare('news_id',$this->news_id,true);
		$criteria->compare('price',$this->price);
		$criteria->compare('status',$this->status);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('modify_time',$this->modify_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return NewsTicketExtend the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 基本信息
     * 
     * @param type $model 票务数据
     * @param type $newsId 票务id
     */
    public function profile($model = NULL, $newsId = NULL)
    {
        if (empty($model)) {
            $model = $this->find('t.news_id=:newsId and t.status=:status', array(
                ':newsId' => $newsId, 
                ':status' => ConstStatus::NORMAL
                )
                );
        }
        if (empty($model)) {
            return NULL;
        }
        return array(
            'price' => $model->price,
        );
    }
    
}
