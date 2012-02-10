<?php

/**
 * This is the model class for table "lcatalog__PrListValue".
 *
 * The followings are the available columns in table 'lcatalog__PrListValue':
 * @property integer $Id
 * @property integer $propertyId
 * @property string $Name
 */
class PrListValue extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PrListValue the static model class
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
		return 'lcatalog__PrListValue';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('propertyId', 'required'),
			array('propertyId', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, propertyId, Name', 'safe', 'on'=>'search'),
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
			'Property' => array(self::BELONGS_TO, 'Property', 'propertyId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'propertyId' => 'Характеристика',
			'Name' => 'Имя',
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
		$criteria->compare('propertyId',$this->propertyId);
		$criteria->compare('Name',$this->Name,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}