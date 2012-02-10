<?php

class GoodsMedia extends CActiveRecord
{
	/**
	 * The followings are the available columns in table 'reestrclient__goods_mediafiles':
	 * @var integer $goodsId
	 * @var integer $mediafilesId
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
		return 'lcatalog__goods_mediafiles';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('goodsId, mediafilesId', 'required'),
			array('goodsId, mediafilesId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('goodsId, mediafilesId', 'safe', 'on'=>'search'),
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
			'goodsId' => 'Goods',
			'mediafilesId' => 'Mediafiles',
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

		$criteria->compare('goodsId',$this->goodsId);

		$criteria->compare('mediafilesId',$this->mediafilesId);

		return new CActiveDataProvider('GoodsMedia', array(
			'criteria'=>$criteria,
		));
	}
}