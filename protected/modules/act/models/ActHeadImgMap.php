<?php

/**
 * This is the model class for table "act_head_img_map".
 *
 * The followings are the available columns in table 'act_head_img_map':
 * @property string $id
 * @property string $act_id
 * @property string $img_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property ImgInfo $img
 */
class ActHeadImgMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_head_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, img_id', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('act_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, img_id, status', 'safe', 'on'=>'search'),
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
			'id' => '活动首图关联id',
			'act_id' => '活动id',
			'img_id' => '图像id',
			'status' => '状态：-1删除，0可用，1当前使用',
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
	 * @return ActHeadImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取当前首图url
     * @param type $id
     */
    public function getCurImgUrl($actId) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('status', 1);
        $criteria->compare('act_id', $actId);
        $imgM = $this->find($criteria);
        if (empty($imgM)) {
            return NULL;
        }
        $img = ImgInfo::model()->profile($imgM->img_id);
        if (empty($img)) {
            return NULL;
        }
        return $img['img_url'];
    }
    
    
    /**
     * 设置某张图为活动首图
     * @param type $actId
     * @param type $imgId
     */
    public function setCurrImg($actId, $imgId) 
    {
        $ahi = $this->find('act_id=:actId and img_id=:imgId', array(
            ':actId' => $actId,
            ':imgId' => $imgId,
        ));
        if (empty($ahi)) {
            $modelImg = new ActHeadImgMap();
            $modelImg->act_id = $actId;
            $modelImg->img_id = $imgId;
            $modelImg->status = ConstStatus::NORMAL;
            $s = $modelImg->save();
            if (!$s) {
                return FALSE;
            }
            $ahi = $modelImg;
        }
        
        $this->updateAll(array('status' => ConstStatus::NORMAL), 'status=1 and act_id=:actId', array(':actId' => $actId));
        
        return $this->updateByPk($ahi->id, array('status' => 1));
    }
    
}
