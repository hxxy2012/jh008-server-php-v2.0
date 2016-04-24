<?php

/**
 * This is the model class for table "user_photo_wall".
 *
 * The followings are the available columns in table 'user_photo_wall':
 * @property string $id
 * @property string $u_id
 * @property string $img_ids
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property UserInfo $u
 */
class UserPhotoWall extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user_photo_wall';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('u_id, update_time', 'required'),
			array('u_id', 'length', 'max'=>10),
			array('img_ids', 'length', 'max'=>120),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, u_id, img_ids, update_time', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '用户照片墙关联id',
			'u_id' => '用户id',
			'img_ids' => 'Img Ids',
			'update_time' => '更新时间',
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
		$criteria->compare('img_ids',$this->img_ids,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPhotoWall the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 用户照片墙
     * 
     * @param type $uid 用户id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function photos($uid, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        if (empty($model) || empty($model->img_ids)) {
            return array(
                'total_num' => 0,
                'photos' => array()
            );
        }
        
        $imgIds = json_decode($model->img_ids);
        
        $count = count($imgIds);
        $pageImgIds = ArrTool::sliceByPageAndSize($imgIds, $page, $size);
        
        $photos = array();
        foreach ($pageImgIds as $v) {
            $photo = ImgInfo::model()->profile($v);
            if (!empty($photo)) {
                array_push($photos, $photo);
            }
        }
        
        return array(
            'total_num' => $count,
            'photos' => $photos
        );
    }
    
    
    /**
     * 更新照片墙
     * 
     * @param array $uid 用户id
     * @param array $imgIds 图像id数组
     */
    public function updatePhotos($uid, array $imgIds)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $model = $this->find($cr);
        
        $idsStr = json_encode(ArrTool::toNumArr($imgIds));
        
        if (empty($model)) {
            $model = new UserPhotoWall();
            $model->u_id = $uid;
            $model->img_ids = $idsStr;
            $model->update_time = date('Y-m-d H:i:s');
            return $model->save();
        }
        
        if (!empty($model->img_ids) && $idsStr == $model->img_ids) {
            return TRUE;
        }
        
        $model->img_ids = $idsStr;
        $model->update_time = date('Y-m-d H:i:s');
        return $model->update();
    }
    
}
