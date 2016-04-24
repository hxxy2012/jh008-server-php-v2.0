<?php

/**
 * This is the model class for table "act_album_img_map".
 *
 * The followings are the available columns in table 'act_album_img_map':
 * @property string $id
 * @property string $album_id
 * @property string $img_id
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActAlbum $album
 * @property ImgInfo $img
 */
class ActAlbumImgMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_album_img_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('album_id, img_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('album_id, img_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, album_id, img_id, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'img' => array(self::BELONGS_TO, 'ImgInfo', 'img_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动相册图片关联id',
			'album_id' => '活动相册id',
			'img_id' => '图片id',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
		);
	}


	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActAlbumImgMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 统计相册图片数
     * 
     * @param type $albumId 相册id
     */
    public function countImgs($albumId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        return $this->count($cr);
    }
    
    
    /**
     * 相册的图片
     * 
     * @param type $albumId 相册的id
     * @param type $page 页数
     * @param type $size 每页记录数
     * @param type $isSelf 是否本人相册
     */
    public function photos($albumId, $page, $size, $isSelf = FALSE)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        if ($isSelf) {
            $cr->compare('t.status', '<>' . ConstCheckStatus::DELETE);
        }  else {
            //非本人只拉取已通过的
            $cr->compare('t.status', ConstCheckStatus::PASS);
        }
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $photoes = array();
        foreach ($rst as $v) {
            $photo = array();
            $photo['id'] = $v->id;
            if (!empty($v->img_id)) {
                $img = ImgInfo::model()->profile($v->img_id);
                if (!empty($img)) {
                    $photo['img_url'] = $img['img_url'];
                }
            }
            $photo['create_time'] = $v->create_time;
            if ($isSelf) {
                $photo['status'] = $v->status;
            }
            array_push($photoes, $photo);
        }
        
        return array(
            'total_num' => $count,
            'photoes' => $photoes,
        );
    }
    
    
    /**
     * 添加相册与图片关联
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     * @param type $model 关联model
     */
    public function add($albumId, $imgId, $model = NULL) 
    {
        if (empty($model)) {
            $model = new ActAlbumImgMap();
        }
        if (!empty($albumId)) {
            $model->album_id = $albumId;
        }
        if (!empty($imgId)) {
            $model->img_id = $imgId;
        }
        
        $isNew = TRUE;
        $m = $this->get($model->album_id, $model->img_id);
        if (!empty($m)) {
            $model = $m;
            $isNew = FALSE;
        }
        $model->status = ConstCheckStatus::PASS;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        if ($isNew) {
            return $model->save();
        }
        return $model->update();
    }
    
    
    /**
     * 获取相册图片关联
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     */
    public function get($albumId, $imgId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.img_id', $imgId);
        return $this->find($cr);
    }
    
    
    /**
     * 获取相册最后一张图片id
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     */
    public function getLastImgId($albumId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $model = $this->find($cr);
        if (empty($model)) {
            return NULL;
        }
        return $model->img_id;
    }
    
    
    /**
     * 删除相册图片
     * 
     * @param type $albumId 相册id
     * @param type $imgId 图片id
     */
    public function del($albumId, $imgId)
    {
        $model = $this->get($albumId, $imgId);
        if (empty($model)) {
            return TRUE;
        }
        $model->status = ConstStatus::DELETE;
        return $model->update();
    }
    
}
