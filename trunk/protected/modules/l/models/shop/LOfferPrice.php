<?php

/**
 * Модель связки спецпредложения и товара
 * This is the model class for table "{{offer_price}}".
 *
 * The followings are the available columns in table '{{offer_price}}':
 * @property integer $priceId
 * @property integer $offerId
 * @property double $Price
 */
class OfferPrice extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return OfferPrice the static model class
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
		return 'lcatalog__offer_price';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
		array('priceId, offerId', 'required'),
		array('priceId, offerId', 'numerical', 'integerOnly'=>true),
		array('Price', 'numerical'),
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
			'priceId' => 'Price',
			'offerId' => 'Offer',
			'Price' => 'Price',
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

		$criteria->compare('priceId',$this->priceId);
		$criteria->compare('offerId',$this->offerId);
		$criteria->compare('Price',$this->Price);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}

	/* 
	 * this function was used to port this model to yii with small version
	 * 
	public function countByAttributes($attributes,$condition='',$params=array())
	{
		Yii::trace(get_class($this).'.countByAttributes()','system.db.ar.CActiveRecord');
		$prefix=$this->getTableAlias(true).'.';
		$builder=$this->getCommandBuilder();
		$criteria=$builder->createColumnCriteria($this->getTableSchema(),$attributes,$condition,$params,$prefix);
		$this->applyScopes($criteria);

		if(empty($criteria->with))
		return $builder->createCountCommand($this->getTableSchema(),$criteria)->queryScalar();
		else
		{
			$finder=new CActiveFinder($this,$criteria->with);
			return $finder->count($criteria);
		}
	}*/
}