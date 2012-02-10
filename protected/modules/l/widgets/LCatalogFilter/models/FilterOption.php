<?php

/**
 * This is the model class for table "lcatalog__FilterOption".
 *
 * The followings are the available columns in table 'lcatalog__FilterOption':
 * @property integer $categoryId
 * @property string $Template
 */
class FilterOption extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return FilterOption the static model class
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
		return 'lcatalog__FilterOption';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryId, Name, Template', 'required'),
			array('categoryId', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('categoryId, Name, Template', 'safe', 'on'=>'search'),
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
			'Category' => array(self::BELONGS_TO,'Category','categoryId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Name' => 'Фильтр',
			'categoryId' => 'Category',
			'Template' => 'Template',
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

		$criteria->compare('categoryId',Yii::app() -> controller -> getCategory() -> Id);
		$criteria->compare('Template',$this->Template,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	/**
	 * Создание шаблона фильтра по-умолчанию
	 * @param Category $category
	 * @param string $filterName
	 */
	private static function createFilterOptionByCategory(Category $category,$filterName){
		$filterOption = new FilterOption;
		$filterOption -> categoryId = $category -> Id;
		$filterOption -> Name = $filterName;
		
		$filterOption -> Template = 
			"<div class='filter'>". CHtml::beginForm('','get') . "\n";
		
		$properties = $category->getProperties(); 
		if(is_array($properties)) foreach($properties as $property){
			if($property -> Type == 'bit' || $property -> Type == 'text') continue;
			if($property -> Type == 'list') $type = 'checkbox';
			else $type = 'range';
			
			$alias = $property -> Alias;
			
			$filterOption -> Template .= "	<div class='element property {$type}' id='pr_{$type}_{$property->Id}'>{property|{$alias},{$type}}</div>\n";
		}
		
		$relations = $category->getRelations();
		if(is_array($relations)) foreach($relations as $relation){
			$alias = $relation -> Alias;
			$filterOption -> Template .= "	<div class='element relation' id='rel_{$alias}'>{property|{$alias},relation}</div>\n";
		}
				
		$filterOption -> Template .= "	<div class='gpp'>На странице: {gpp1,20,50,100}</div>\n"
				. "	" .CHtml::submitButton('Поиск') . "\n"
				. CHtml::endForm()
				. "</div>";

		if(!$filterOption->save())
			throw new CException('Не удалось сохранить настройки фильтра для данной страницы');
		
		
		
		return $filterOption;
	}
	
	/**
	 * Возвращает шаблон виджета фильтра по текущей категории
	 * @param Category $category
	 * @return string
	 */
	public static function getTemplate(Category $category, $filterName){
		if(empty($category))
			throw new CException('Некорректная работа с фильтром: не указана категория');
		
		$filterOption = FilterOption::model() -> findByAttributes(array(
			'categoryId' => $category -> Id,
			'Name'       => $filterName,
		));
		
		if(empty($filterOption))
			$filterOption = self::createFilterOptionByCategory($category,$filterName);
				
		return $filterOption -> Template;
	}
}