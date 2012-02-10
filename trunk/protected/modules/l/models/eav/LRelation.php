<?php

/**
 * This is the model class for table "lcatalog__Relation".
 *
 * The followings are the available columns in table 'lcatalog__Relation':
 * @property integer $Id
 * @property integer $categoryId
 * @property integer $entityCategoryId
 * @property string $Alias
 * @property string $Description
 */
class Relation extends CActiveRecord
{
	public function getName(){
		return $this -> EntityCategory -> Name;
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return LcatalogRelation the static model class
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
		return 'lcatalog__Relation';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Description','safe'),
			array('categoryId, entityCategoryId, Alias', 'required'),
			array('categoryId, entityCategoryId', 'numerical', 'integerOnly'=>true),
			array('Alias', 'length', 'max'=>50),
			array('Description', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, categoryId, entityCategoryId, Alias, Description', 'safe', 'on'=>'search'),
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
			'EntityCategory' => array(self::BELONGS_TO,'Category','entityCategoryId'),
			'Category'       => array(self::BELONGS_TO,'Category','categoryId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id'               => 'ID',
			'categoryId'       => 'Категория',
			'entityCategoryId' => 'Сущность',
			'Alias'            => 'Алиас',
			'Description'      => 'Описание',
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
		$criteria->compare('categoryId',$this->categoryId);
		$criteria->compare('entityCategoryId',$this->entityCategoryId);
		$criteria->compare('Alias',$this->Alias,true);
		$criteria->compare('Description',$this->Description,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 * возвращает диапазон возможных значений, принимаемых данной характеристикой
	 * в виде массива для функции CHtml::activeDropDownList
	 * @return mixed
	 */
	public function getPossibleValues($withEmpty = true){
		$data   = Good::model() -> findAllByAttributes(array('categoryId' => $this -> entityCategoryId));
		$result = CHtml::listData($data,'Id','Name');
		return $withEmpty ? array('пусто' => 'пусто') + $result : $result;
	}	
}