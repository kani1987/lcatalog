<?php

/**
 * This is the model class for table "lcatalog__Property".
 *
 * The followings are the available columns in table 'lcatalog__Property':
 * @property integer $Id
 * @property integer $categoryId
 * @property string $Name
 * @property string $Alias
 * @property string $Type
 * @property integer $IsVisible
 * @property integer $IsMultivalued
 */
class Property extends CActiveRecord
{
	/**
	 * возможные типы характеристик
	 * @var unknown_type
	 */
	private static $_types = array('list','float','bit','text');
	
	/**
	 * список возможных типов характеристик
	 */
	public function getPossibleTypes(){
		$types = array_flip(Property::$_types);
		foreach($types as $key=>$value) $types[$key] = $key;
		
		return $types;
	}
    
    /**
     * Возвращает имя таблицы, в которой хранятся значения товаров,
     * связанные с данной характеристикой
     * @return string
     */
    public function getValueTableName(){
        if($this -> Type == 'list')  $tableName = 'PrvalueList'; 
        if($this -> Type == 'float') $tableName = 'PrvalueFloat';
        if($this -> Type == 'bit')   $tableName = 'PrvalueBit';
        if($this -> Type == 'text')  $tableName = 'PrvalueText';
        
        return $tableName;
    }
	
	/**
	 * возможные значения характеристики типа list
	 * @return array of PrListValue
	 */
	public function getListValues(){
		return PrListValue::model() -> findAllByAttributes(array('propertyId' => $this -> Id));
	}
	
	/**
	 * возвращает диапазон возможных значений, принимаемых данной характеристикой
	 * в виде массива для функции CHtml::activeDropDownList
	 * @return mixed
	 */
	public function getPossibleValues($withEmpty = true){
		$result = NULL;
		
		if($this -> Type == 'list'){
			$data   = PrListValue::model() -> findAllByAttributes(array('propertyId' => $this -> Id));
			$result = CHtml::listData($data,'Id','Name');
			return $withEmpty ? array('пусто' => 'пусто') + $result : $result;
		}
		
		return NULL;		
	}
	
	/**
	 * возвращает диапазон возможных значений, принимаемых данной характеристикой
	 * в виде объекта CActiveDataProvider
	 * @return CActiveDataProvider | NULL
	 */
	public function getPossibleListValuesDataProvider(){
		if($this -> Type != 'list') throw new CException('Эта функция работает только для характеристик типа list');
		
		$criteria = new CDbCriteria();
		$criteria -> compare('propertyId',$this -> Id);
		
		return new CActiveDataProvider('PrListValue',array(
			'criteria' => $criteria
		));
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Property the static model class
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
		return 'lcatalog__Property';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryId, Name, Alias, Type', 'required'),
			array('categoryId, IsVisible, IsMultivalued, IsCustom', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>256),
			array('Alias', 'length', 'max'=>50),
			array('Type', 'length', 'max'=>5),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, categoryId, Name, Alias, Type, IsVisible, IsMultivalued', 'safe', 'on'=>'search'),
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
			'Id'            => 'ID',
			'categoryId'    => 'Категория',
			'Name'          => 'Имя',
			'Alias'         => 'Алиас',
			'Type'          => 'Тип',
			'IsVisible'     => 'Видимость',
			'IsMultivalued' => 'Мультизначность',
			'IsCustom'      => 'Настраеваемая',
			'Unit'          => 'Единицы',
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
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Alias',$this->Alias,true);
		$criteria->compare('Type',$this->Type,true);
		$criteria->compare('IsVisible',$this->IsVisible);
		$criteria->compare('IsMultivalued',$this->IsMultivalued);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Характеристику можно удалять только если нет товаров в соотв. категории
	 */
	public function delete(){
		if(0 === Good::model() -> countByAttributes(array('categoryId' => $this -> categoryId)))
			return parent::delete();
		
		return false;
	}
}