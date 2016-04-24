<?php

/**
 * This is the model class for table "act_album_video_map".
 *
 * The followings are the available columns in table 'act_album_video_map':
 * @property string $id
 * @property string $album_id
 * @property string $video_id
 * @property integer $status
 * @property string $create_time
 * @property string $modify_time
 *
 * The followings are the available model relations:
 * @property ActAlbum $album
 * @property VideoInfo $video
 */
class ActAlbumVideoMap extends ActModel
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'act_album_video_map';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('album_id, video_id, create_time, modify_time', 'required'),
			array('status', 'numerical', 'integerOnly'=>true),
			array('album_id, video_id', 'length', 'max'=>10),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, album_id, video_id, status, create_time, modify_time', 'safe', 'on'=>'search'),
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
			//'video' => array(self::BELONGS_TO, 'VideoInfo', 'video_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => '活动相册视频关联id',
			'album_id' => '活动相册id',
			'video_id' => '视频id',
			'status' => '状态',
			'create_time' => '创建时间',
			'modify_time' => '修改时间',
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
		$criteria->compare('album_id',$this->album_id,true);
		$criteria->compare('video_id',$this->video_id,true);
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
	 * @return ActAlbumVideoMap the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    
    /**
     * 统计相册视频数
     * 
     * @param type $albumId 相册id
     */
    public function countVideos($albumId)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        $cr->compare('t.status', ConstStatus::NORMAL);
        return $this->count($cr);
    }
    
    
    /**
     * 相册的视频
     * 
     * @param type $albumId 相册的id
     * @param type $page 页数
     * @param type $size 每页记录数
     */
    public function videos($albumId, $page, $size)
    {
        $cr = new CDbCriteria();
        $cr->compare('t.album_id', $albumId);
        //只拉取已通过的
        $cr->compare('t.status', ConstCheckStatus::PASS);
        $count = $this->count($cr);
        $cr->order = 't.id asc';
        $cr->offset = ($page - 1) * $size;
        $cr->limit = $size;
        $rst = $this->findAll($cr);
        
        $videos = array();
        foreach ($rst as $v) {
            $video = array();
            $video['id'] = $v->id;
            if (!empty($v->img_id)) {
                $vi = VideoInfo::model()->profile($v->video_url);
                if (!empty($vi)) {
                    $video['video_url'] = $vi['video_url'];
                }
            }
            $video['create_time'] = $v->create_time;
            array_push($videos, $video);
        }
        
        return array(
            'total_num' => $count,
            'videos' => $videos,
        );
    }
    
}
