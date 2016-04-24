<?php

/**
 * This is the model class for table "act_album_user_map".
 *
 * The followings are the available columns in table 'act_album_user_map':
 * @property string $id
 * @property string $album_id
 * @property string $u_id
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActAlbum $album
 * @property UserInfo $u
 */
class ActAlbumUserMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_album_user_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('album_id, u_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('album_id, u_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, album_id, u_id, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'album' => array(self::BELONGS_TO, 'ActAlbum', 'album_id'),
			//'u' => array(self::BELONGS_TO, 'UserInfo', 'u_id'),
            
            'fkAlbum' => array(self::BELONGS_TO, 'ActAlbum', 'album_id', 'on' => 'fkAlbum.status=' . ConstCheckStatus::PASS),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动相册与用户关联id',
			'album_id' => '相册id',
			'u_id' => '用户id',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActAlbumUserMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 相册所有者
     * 
     * @param type $albumId 相册id
     * @param type $uid 当前用户id
     * @param type $cityId 城市id
     */
    public function owner($albumId, $uid, $cityId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }
        return UserInfo::model()->profile(NULL, $model->u_id, $cityId, $uid, NULL, FALSE);
    }
    
    
    /**
     * 添加相册与用户关联
     * 
     * @param type $albumId 相册id
     * @param type $uid 用户id
     * @param type $model 相册与用户关联model
     */
    public function add($albumId, $uid, $model = NULL)
    {
        if (empty($model)) {
            $model = new ActAlbumUserMap();
            $model->album_id = $albumId;
            $model->u_id = $uid;
        }
        if (!empty($albumId)) {
            $model->album_id = $albumId;
        }
        if (!empty($uid)) {
            $model->u_id = $uid;
        }
        
        $isNew = TRUE;
        $m = $this->get($model->album_id, $model->u_id);
        if (!empty($m)) {
            $model = $m;
            $isNew = FALSE;
        }
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNew) {
            return $model->save();
        }
        return $model->update();
    }
    
    
    /**
     * get Model
     * 
     * @param type $albumId 相册id
     * @param type $uid 用户id
     */
    public function get($albumId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.u_id', $uid);
        return $this->find($cr);
    }
    
    
    /**
     * 创建用户相册
     * 
     * @param type $actId 活动id
     * @param type $uid 用户id
     * @param type $subject 相册名称
     */
    public function createUserAlbum($actId, $uid, $subject, ActAlbumUserMap $model = NULL)
    {
        $transaction = $this->getDbConnection()->beginTransaction();
        try {
            //创建相册
            $album = new ActAlbum();
            $r = ActAlbum::model()->add($actId, $subject, $album);
            if (!$r) {
                $transaction->rollBack();
                //print_r($album->getErrors());
                return FALSE;
            }
            
            //创建相册与用户关联
            if (empty($model)) {
                $model = new ActAlbumUserMap();
            }
            $r = ActAlbumUserMap::model()->add($album->id, $uid, $model);
            if (!$r) {
                //print_r($model->getErrors());
                $transaction->rollBack();
                return FALSE;
            }
            $transaction->commit();
            return TRUE;
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            $transaction->rollBack();
            return FALSE;
        }
    }
    
    
    /**
     * 相册的所有者用户id
     * 
     * @param type $albumId 相册id
     */
    public function ownerId($albumId) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }
        return $model->u_id;
    }
    
    
    /**
     * 某活动用户的相册id
     * 
     * @param type $actId 活动id
     * @param type $uid 用户id
     */
    public function albumId($actId, $uid) 
    {
        $cr = new CDbCriteria();
        $cr->compare('t.u_id', $uid);
        $cr->compare('t.status', ConstStatus::NORMAL);
        $cr->with = 'fkAlbum';
        $cr->compare('fkAlbum.act_id', $actId);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }
        return $model->album_id;
    }
    
}
