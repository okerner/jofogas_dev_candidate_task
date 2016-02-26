<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class Tbl_categories extends CActiveRecord
{
	public $parent_id;
	public $cat_name;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}	
	
	public function tableName()
    {
        return 'tbl_categories';
    }
	
	public function rules()
	{
		return array(
			// cat_name is required
			array('cat_name', 'required'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'cat_name'=>'Name of category',
		);
	}

}
