<?php

class Prices extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'reestrclient__images':
	 * @var integer $Id
	 * @var string $Path2Image
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
		return 'lcatalog__prices';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('url', 'length', 'max'=>250),
			array('goodsId, shopId, Price, ', 'numerical', 'integerOnly'=>true),
			//array('Price', 'numerical', 'floatOnly'=>true),
			array('url, Date', 'safe'),
		);
	}
	
	public function getGoodsForId( $Id )
	{
	  return Good::model()->findByPk($Id);
	}
	
		
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'Goods' => array(self::BELONGS_TO, 'Good' ,'shopId'),
			'Shop'  => array(self::BELONGS_TO, 'Shop' ,'shopId'),
			'Site'  => array(self::BELONGS_TO, 'Site' ,'shopId'),
		);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id'       => 'Id',
			'goodsId'  => 'Товар',
			'tagId'    => 'tagId',
			'shopId'   => 'Магазин',
			'Price'    => 'Цена',
			'url'      => 'Урл',
			'DateTime' => 'Дата',
		);
	}
}