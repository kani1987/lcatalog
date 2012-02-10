<?php

class MediaType extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'reestrclient__mediatypes':
	 * @var integer $Id
	 * @var string $Extension
	 * @var string $Postfix
	 * @var string $Url
	 * @var string $Description
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lcatalog__mediatypes';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Extension', 'length', 'max'=>5),
			array('Postfix', 'length', 'max'=>10),
			array('Url', 'length', 'max'=>255),
			array('Description', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, Extension, Postfix, Url, Description', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'Id',
			'Extension' => 'Extension',
			'Postfix' => 'Postfix',
			'Url' => 'Url',
			'Description' => 'Description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id);

		$criteria->compare('Extension',$this->Extension,true);

		$criteria->compare('Postfix',$this->Postfix,true);

		$criteria->compare('Url',$this->Url,true);

		$criteria->compare('Description',$this->Description,true);

		return new CActiveDataProvider('MediaTypes', array(
			'criteria'=>$criteria,
		));
	}
}