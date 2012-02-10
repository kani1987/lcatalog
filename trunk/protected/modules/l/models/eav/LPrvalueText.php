<?php

/**
 * This is the model class for table "lcatalog__PrvalueText".
 *
 * The followings are the available columns in table 'lcatalog__PrvalueText':
 * @property integer $Id
 * @property integer $goodId
 * @property integer $propertyId
 * @property string $Value
 */
class PrvalueText extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PrvalueText the static model class
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
		return 'lcatalog__PrvalueText';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goodId, propertyId, Value', 'required'),
			array('goodId, propertyId', 'numerical', 'integerOnly'=>true),
			array('Value', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, goodId, propertyId, Value', 'safe', 'on'=>'search'),
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
			'Id' => 'ID',
			'goodId' => 'Good',
			'propertyId' => 'Property',
			'Value' => 'Value',
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
		$criteria->compare('goodId',$this->goodId);
		$criteria->compare('propertyId',$this->propertyId);
		$criteria->compare('Value',$this->Value,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}