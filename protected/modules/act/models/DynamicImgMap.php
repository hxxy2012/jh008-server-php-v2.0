<?php

/**
 * This is the model class for table "dynamic_img_map".
 *
 * The followings are the available columns in table 'dynamic_img_map':
 * @property string $id
 * @property string $dynamic_id
 * @property string $img_id
 * @property integer $status
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property UserDynamic $dynamic
 * @property ImgInfo $img
 */
class DynamicImgMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'dynamic_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('dynamic_id, img_id, create_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('dynamic_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, dynamic_id, img_id, status, create_time', 'safe', 'on'=>'search'),
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
			//'dynamic' => array(self::BELONGS_TO, 'UserDynamic', 'dynamic_id'),
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
            
            'fkDynamic' => array(self::BELONGS_TO, 'UserDynamic', 'dynamic_id'),
            'fkImg' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '动态图片关联id',
			'dynamic_id' => '动态id',
			'img_id' => '图片id',
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
		$criteria->compare('dynamic_id',$this->dynamic_id,true);
		$criteria->compare('img_id',$this->img_id,true);
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
	 * @return DynamicImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 添加动态和图片的关联
     * 
     * @param type $dynamic_id 动态id
     * @param type $img_id 图片的id
     */
    public function add($dynamic_id, $img_id)
    {
        $cr = new CDbCriteria();
        $cr->compare('dynamic_id', $dynamic_id);
        $cr->compare('img_id', $img_id);
        $model = $this->find($cr);
        
        if (empty($model)) {
            $model = new DynamicImgMap();
            $model->dynamic_id = $dynamic_id;
            $model->img_id = $img_id;
            $model->status = ConstStatus::NORMAL;
            $model->create_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        if (ConstStatus::NORMAL == $model->status) {
            return FALSE;
        }
        
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
    
    /**
     * 动态相关的图片
     * 
     * @param type $dynamicId
     * @param type $page
     * @param type $size
     */
    public function imgs($dynamicId, $page, $size) 
    {
       $cr = new CDbCriteria();
       $cr->compare('t.dynamic_id', $dynamicId);
       $cr->compare('t.status', ConstStatus::NORMAL);
       
       $cr->with = 'fkImg';
       $cr->compare('fkImg.status', ConstStatus::NORMAL);
       
       $count = $this->count($cr);
       $cr->order = 't.id desc';
       $cr->offset = ($page - 1) * $size;
       $cr->limit = $size;
       $rst = $this->findAll($cr);
       
       $imgs = array();
       foreach ($rst as $v) {
           $img = ImgInfo::model()->profile($v->img_id);
           if (empty($img)) {
                continue;
            }
            array_push($imgs, $img);
       }
       
       return array(
           'total_num' => $count,
           'imgs' => $imgs
       );
    }
    
    
    /**
     * 用户动态图片
     * 
     * @param type $author_id
     * @param type $page
     * @param type $size
     */
    public function userImgs($author_id, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.status', ConstStatus::NORMAL);

        $cr->with = array('fkDynamic', 'fkImg');
        $cr->compare('fkDynamic.author_id', $author_id);
        $cr->compare('fkDynamic.status', ConstStatus::NORMAL);
        $cr->compare('fkImg.status', ConstStatus::NORMAL);
        
        $count = $this->count($cr);
        $cr->order = 't.id desc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);

        $imgs = array();
        foreach ($rst as $v) {
            $img = ImgInfo::model()->profile($v->img_id);
            if (empty($img)) {
                continue;
            }
            array_push($imgs, $img);
        }

        return array(
            'total_num' => $count,
            'photos' => $imgs
        );
    }
    
}
