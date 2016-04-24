<?php

class ProductO extends Product {
    
    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    
    /**
     * 创建商品
     * 
     * @param type $subject
     * @param type $body
     * @param type $unit_price
     */
    public function createO($subject, $body, $unit_price, $model = NULL) 
    {
        if (empty($model)) {
            $model = new Product();
        }
        $model->subject = $subject;
        $model->body = $body;
        $model->unit_price = isset($unit_price) ? $unit_price : 0;
        $model->status = ConstStatus::NORMAL;
        $model->create_time = date('Y-m-d H:i:s');
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->save();
    }
    
    
    /**
     * 修改商品
     * 
     * @param type $productId
     * @param type $subject
     * @param type $body
     * @param type $unit_price
     */
    public function modifyO($productId, $subject, $body, $unit_price)
    {
        $model = $this->findByPk($productId);
        if (empty($model)) {
            return FALSE;
        }
        $model->subject = $subject;
        $model->body = $body;
        if (isset($unit_price)) {
            $model->unit_price = $unit_price;
        }
        $model->status = ConstStatus::NORMAL;
        $model->modify_time = date('Y-m-d H:i:s');
        return $model->update();
    }

}

?>
