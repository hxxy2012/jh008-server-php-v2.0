<?php

class ActModel extends CActiveRecord
{
    public static $dbT;
    
    public function getDbConnection()
	{
        if(self::$dbT === null) {
            self::$dbT=Yii::app()->getComponent('dbAct');
			if(!(self::$dbT instanceof CDbConnection))
				throw new CDbException(Yii::t('yii','Active Record requires a "dbAct" CDbConnection application component.'));
		}
        return self::$db = self::$dbT;
	}
    
    
    public function behaviors()
    {
        return array(
            'ActBehavior',
        );
    }
    
}

?>
