<?php

/**
 * This is the model class for table "jhla_act_route".
 *
 * The followings are the available columns in table 'jhla_act_route':
 * @property integer $act_route_id
 * @property string $act_route_name
 * @property string $act_route_points
 * @property string $act_route_create_time
 */
class ActRoute extends ActModel {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'act_route';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('act_route_name', 'length', 'max' => 255),
            array('act_route_create_time', 'length', 'max' => 32),
            array('act_route_points', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('act_route_id, act_id, act_route_name, act_route_points, act_route_create_time, act_route_status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'act_route_id' => 'id',
            'act_id' => '活动id',
            'act_route_name' => '线路名称',
            'act_route_points' => '线路经纬度集合',
            'act_route_create_time' => '创建时间',
            'act_route_status' => '状态-1：删除0：启用',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('act_route_id', $this->act_route_id);
        $criteria->compare('act_id', $this->act_id);
        $criteria->compare('act_route_name', $this->act_route_name, true);
        $criteria->compare('act_route_points', $this->act_route_points, true);
        $criteria->compare('act_route_create_time', $this->act_route_create_time, true);
        $criteria->compare('act_route_status', $this->act_route_status);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ActRoute the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }
    
    public function getActRoutes($act_id){
        return $this->findAll("act_id = :act_id and act_route_status = :act_route_status", 
                    array("act_id" => $act_id, 
                        'act_route_status' => 0,
                ));
    }
    
    public function getActRoutesBasic($act_id){
         return array_map(function($record) {
                    return array('act_route_id' => $record->attributes['act_route_id'],
                                    'act_route_name'=>$record->attributes['act_route_name'],
                                    'act_route_create_time'=>$record->attributes['act_route_create_time'],
                                );
                                
                }, $this->getActRoutes($act_id));
    }
    
    
    
    public function queryActRoute($act_route_id) {
        return $this->find("act_route_id = :act_route_id", array("act_route_id" => $act_route_id));
    }

    public function addActRoute($act_id, $act_route_name, $act_route_points) {
        $model = new ActRoute();
        $model->updateAll(array('act_route_status' => -1
                ), 
                'act_id = :act_id and act_route_status = :act_route_status',
                array("act_id" => $act_id, 
                        'act_route_status' => 0,
                ));
        $model->act_id = $act_id;
        $model->act_route_name = $act_route_name;
        $model->act_route_points = $act_route_points;
        $model->act_route_create_time = date('Y-m-d H:i:s', time());
        return $model->save();
    }

    public function addActRouteEx($act_id, $act_route_name, $act_route_points) {
        $route_points_info = array();
        if(!empty($act_route_points)){
		$tmp_points = explode('-', $act_route_points);
            foreach($tmp_points as $tmp_point){
                if(!empty($tmp_point)){
                    $tmp_point = explode(',', $tmp_point);
                    $route_points_info[] = array(
                        'lng' => $tmp_point[0],
                        'lat' => $tmp_point[1],
                        'desc' => $tmp_point[2],
                        'addr' => $tmp_point[3],
                    );
                }
            }
        }
        //var_dump($route_points_info);
        return $this->addActRoute($act_id, $act_route_name, serialize($route_points_info));
    }
}
