<?php

/**
 * This is the model class for table "lcatalog__PrvalueEntity".
 *
 * The followings are the available columns in table 'lcatalog__PrvalueEntity':
 * @property integer $Id
 * @property integer $goodId
 * @property integer $relationId
 * @property integer $entityId
 */
class PrvalueEntity extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return PrvalueEntity the static model class
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
		return 'lcatalog__PrvalueEntity';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goodId, relationId, entityId', 'required'),
			array('goodId, relationId, entityId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, goodId, relationId, entityId', 'safe', 'on'=>'search'),
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
			'relationId' => 'Relation',
			'entityId' => 'Entity',
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
		$criteria->compare('relationId',$this->relationId);
		$criteria->compare('entityId',$this->entityId);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}