<?php

/**
 * This is the model class for table "business_logo_img_map".
 *
 * The followings are the available columns in table 'business_logo_img_map':
 * @property string $id
 * @property string $b_id
 * @property string $img_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property BusinessInfo $b
 * @property ImgInfo $img
 */
class BusinessLogoImgMap extends BusinessModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'business_logo_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('b_id, img_id, status', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('b_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, b_id, img_id, status', 'safe', 'on'=>'search'),
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
			//'b' => array(self::BELONGS_TO, 'BusinessInfo', 'b_id'),
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
            
            'fkImg' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '商家logo关联id',
			'b_id' => '商家id',
			'img_id' => '图像id',
			'status' => '状态',
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
		$criteria->compare('b_id',$this->b_id,true);
		$criteria->compare('img_id',$this->img_id,true);
		$criteria->compare('status',$this->status);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BusinessLogoImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 设置某张图为logo
     * @param type $id
     * @return boolean
     */
    public function setCurrImg($bid, $imgId) 
    {
        $bloi = $this->find('b_id=:bid and img_id=:imgId', array(
            ':bid' => $bid,
            ':imgId' => $imgId,
        ));
        if (empty($bloi)) {
            $modelImg = new BusinessLogoImgMap();
            $modelImg->b_id = $bid;
            $modelImg->img_id = $imgId;
            $modelImg->status = ConstStatus::NORMAL;
            $s = $modelImg->save();
            if (!$s) {
                return FALSE;
            }
            $bloi = $modelImg;
        }
        
        $this->updateAll(array('status' => ConstStatus::NORMAL), 'status=1');
        
        return $this->updateByPk($bloi->id, array('status' => 1));
    }
    
    
    /**
     * 获取当前logo图像路径
     * @param type $bid
     */
    public function getCurImgUrl($bid) 
    {
        $imgM = $this->find('b_id=:bid and status=1', array(':bid' => $bid));
        if (empty($imgM)) {
            return NULL;
        }
        $img = ImgInfo::model()->profile($imgM->img_id);
        if (empty($img)) {
            return NULL;
        }
        return $img['img_url'];
    }
    
}
