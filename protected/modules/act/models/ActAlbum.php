<?php

/**
 * This is the model class for table "act_album".
 *
 * The followings are the available columns in table 'act_album':
 * @property string $id
 * @property string $act_id
 * @property integer $type
 * @property integer $owner_type
 * @property string $cover_id
 * @property string $subject
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActInfo $act
 * @property ActAlbumImgMap[] $actAlbumImgMaps
 * @property ActAlbumUserMap[] $actAlbumUserMaps
 * @property ActAlbumVideoMap[] $actAlbumVideoMaps
 */
class ActAlbum extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_album';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('act_id, create_time, modify_time', 'required'),
			array('type, owner_type, status', 'numerical', 'integerOnly'=>true),
			array('act_id, cover_id', 'length', 'max'=>10),
			array('subject', 'length', 'max'=>64),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, act_id, type, owner_type, cover_id, subject, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'actAlbumImgMaps' => array(self::HAS_MANY, 'ActAlbumImgMap', 'album_id'),
			//'actAlbumUserMaps' => array(self::HAS_MANY, 'ActAlbumUserMap', 'album_id'),
			//'actAlbumVideoMaps' => array(self::HAS_MANY, 'ActAlbumVideoMap', 'album_id'),
            
            'fkAct' => array(self::BELONGS_TO, 'ActInfo', 'act_id', 'on' => 'fkAct.status=' . ConstActStatus::PUBLISHING),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动相册id',
			'act_id' => '活动id',
			'type' => '类型：1图片，2视频',
			'owner_type' => '所有者：1主办方，2用户',
			'cover_id' => '封面图片id',
			'subject' => '相册标题',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActAlbum the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 活动的主办方图片相册
     * 
     * @param type $actId 活动id
     */
    public function busiImgAlbum($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.type', 1);
        $cr->compare('t.owner_type', 1);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }  else {
            return $this->profile(NULL, $model);
        }
    }
    
    
    /**
     * 活动的主办方视频相册
     * 
     * @param type $actId 活动id
     */
    public function busiVideoAlbum($actId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.type', 2);
        $cr->compare('t.owner_type', 1);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $model = $this->find($cr);
        
        if (empty($model)) {
            return NULL;
        }  else {
            return $this->profile(NULL, $model);
        }
    }
    
    
    /**
     * 用户相册
     * 
     * @param type $actId 活动id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $cityId 城市id
     * @param type $uid 当前用户id
     */
    public function userImgAlbums($actId, $page, $size, $cityId = NULL, $uid = NULL)
    {
        $cr = new CDbCriteria();
        $cr->select = 't.*,COUNT(i.id) AS img_num';
        $cr->join = 'LEFT JOIN act_album_img_map AS i on i.album_id=t.id AND i.status=3';
        $cr->compare('t.act_id', $actId);
        $cr->compare('t.type', 1);
        $cr->compare('t.owner_type', 2);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $cr->group = 't.id';
        
        //SELECT COUNT(i.`id`) AS img_num, a.* FROM act_album a LEFT JOIN act_album_img_map i ON a.id = i.`album_id` AND i.`status`=3 WHERE a.`act_id`=99 AND a.`type`=1 AND a.`owner_type`=2 GROUP BY a.`id`;
        
        $count = $this->count($cr);
        $cr->order = 'img_num desc, t.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $albums = array();
        foreach ($rst as $v) {
            $model = $this->profile(NULL, $v, $cityId, $uid);
            array_push($albums, $model);
        }
        return array(
            'total_num' => $count,
            'albums' => $albums,
        );
    }


    /**
     * 相册基本资料
     * 
     * @param type $id 相册id
     * @param type $model 相册model
     * @param type $uid 当前用户id
     * @param type $cityId 城市id
     */
    public function profile($id, $model = NULL, $uid = NULL, $cityId = NULL)
    {
        if (empty($model)) {
            $model = $this->findByPk($id);
        }
        
        $album = array(
            'id' => $model->id,
            'subject' => $model->subject,
            'create_time' => $model->create_time,
        );
        if (empty($model->cover_id)) {
            if (1 == $model->type) {
                $coverId = ActAlbumImgMap::model()->getLastImgId($model->id);
                $img = ImgInfo::model()->profile($coverId);
                if (!empty($img)) {
                    $album['cover_url'] = $img['img_url'];
                }
            }
        }  else {
            $img = ImgInfo::model()->profile($model->cover_id);
            if (!empty($img)) {
                $album['cover_url'] = $img['img_url'];
            }
        }
        if (1 == $model->type) {
            $album['sum'] = ActAlbumImgMap::model()->countImgs($model->id);
        }  else {
            $album['sum'] = ActAlbumVideoMap::model()->countVideos($model->id);
        }
        if (2 == $model->owner_type) {
            $album['user'] = ActAlbumUserMap::model()->owner($model->id, $uid, $cityId);
        }
        return $album;
    }
    
    
    /**
     * 添加活动相册
     * 
     * @param type $actId 活动id
     * @param type $subject 标题
     * @param type $model 相册model
     */
    public function add($actId, $subject, $model = NULL) 
    {
        if (empty($model)) {
            $model = new ActAlbum();
        }
        $model->act_id = $actId;
        $model->type = 1;
        $model->owner_type = 2;
        $model->cover_id = NULL;
        $model->subject = $subject;
        $model->status = ConstCheckStatus::PASS;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 查看活动id
     * 
     * @param type $albumId 相册id
     */
    public function actId($albumId)
    {
        $model = $this->findByPk($albumId);
        if (empty($model) || ConstStatus::DELETE == $model->status) {
            return NULL;
        }
        return $model->act_id;
    }
    
}
