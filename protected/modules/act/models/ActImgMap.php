<?php

/**
 * This is the model class for table "act_img_map".
 *
 * The followings are the available columns in table 'act_img_map':
 * @property string $id
 * @property string $act_id
 * @property string $img_id
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property ImgInfo $img
 */
class ActImgMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_img_map';
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
            
            //'fkImg' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动图像关联id',
			'act_id' => '活动id',
			'img_id' => '图像id',
			'status' => '状态：-1删除，0正常',
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
	 * @return ActImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 获取活动的图片
     * @param type $actId
     */
    public function getImgs($actId) 
    {
        $criteria = new CDbCriteria();
        $criteria->compare('t.act_id', $actId);
        $criteria->compare('t.status', ConstStatus::NORMAL);
        //$criteria->with = 'fkImg';
        //$criteria->compare('fkImg.status', ConstStatus::NORMAL);
        
        $rst = $this->findAll($criteria);
        $imgs = array();
        foreach ($rst as $k => $v) {
            //$imgs[$k]['id'] = $v->fkImg->id;
            //$imgs[$k]['img_url'] = Yii::app()->imgUpload->getDownUrl($v->fkImg->img_url);
            
            $img = ImgInfo::model()->profile($v->img_id);
            if (!empty($img)) {
                array_push($imgs, $img);
            }
        }
        return $imgs;
    }
    
    
    /**
     * 设置活动的图片
     * @param type $imgIds
     * @param type $actId
     * @return boolean
     */
    public function setActImgs($imgIds, $actId)
    {
        $allCriteria = new CDbCriteria();
        $allCriteria->compare('act_id', $actId);
        $imgs = $this->findAll($allCriteria);
        
        $new = $imgIds;
        
        //db已有img
        $has = array();
        foreach ($imgs as $k => $v) {
            $has[$k] = intval($v->img_id);
        }
        
        //需要新插入的
        $adds = array_diff($new, $has);
        
        //已有且应添加（status改0）
        $upHas = array_intersect($new, $has);
        
        //已有不需添加（status改-1）
        $delHas = array_diff($has, $new);
        
        $transaction = Yii::app()->dbAct->beginTransaction();
        
        try{
            if (!empty($adds)) {
                foreach ($adds as $k => $v) {
                    $aim = new ActImgMap();
                    $aim->img_id = $v;
                    $aim->act_id = $actId;
                    $aim->status = ConstStatus::NORMAL;
                    $aim->save();
                }
            }
            if (!empty($upHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::NORMAL,
                ),
                'img_id in (' . implode(',', $upHas) . ') and act_id=:actId',
                array(
                    ':actId' => $actId,
                    )
                );
            }
            if (!empty($delHas)) {
                $this->updateAll(array(
                    'status' => ConstStatus::DELETE,
                ),
                'img_id in (' . implode(',', $delHas) . ') and act_id=:actId',
                array(
                    ':actId' => $actId,
                    )
                );
            }
            $transaction->commit();
            return TRUE;
        } catch(Exception $e){
            print_r($e->getMessage());
            $transaction->rollBack();
        }
        return FALSE;
    }
    
}
