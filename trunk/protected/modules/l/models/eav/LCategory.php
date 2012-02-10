<?php

/**
 * This is the model class for table "lcatalog__Category".
 *
 * The followings are the available columns in table 'lcatalog__Category':
 * @property integer $Id
 * @property string $Name
 * @property string $Alias
 * @property string $Description
 * @property integer $LLeaf
 * @property integer $RLeaf
 * @property integer $Level
 * @property integer $IsVisible
 */
class Category extends CActiveRecord
{
	/**
	 * Получить массив дочерних категорий, в которых есть цены у данного магазина
	 * @param Shop $shop
	 */
	public function getChildCategoriesByShop(Shop $shop){
		$criteria = new CDbCriteria();
		
		$criteria -> distinct = true;
		$criteria -> join = " INNER JOIN lcatalog__Good AS good ON good.categoryId=t.Id"
		                  . " INNER JOIN lcatalog__prices AS pr ON pr.goodsId=good.Id AND pr.shopId=:shopid"		                  
		                  ;
		$criteria -> addCondition("t.LLeaf > :left AND t.RLeaf < :right AND pr.Price >= 0 AND pr.prlistsId=0");
		$criteria -> params = array(
			':left'  => $this -> LLeaf,
			':right' => $this -> RLeaf,
			':shopid' => $shop -> Id,
		);
		
		return Category::model() -> findAll($criteria);
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Category the static model class
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
		return 'lcatalog__Category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Alias, IsVisible', 'required'),
			array('LLeaf, RLeaf, Level, IsVisible', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>256),
			array('Alias', 'length', 'max'=>50),
			array('Description', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, Name, Alias, Description, LLeaf, RLeaf, Level, IsVisible', 'safe', 'on'=>'search'),
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
			'Id'          => 'ID',
			'Name'        => 'Имя',
			'Alias'       => 'Алиас',
			'Description' => 'Описание',
			'LLeaf'       => 'Левый лист',
			'RLeaf'       => 'Правый лист',
			'Level'       => 'Уровень',
			'IsVisible'   => 'Видимая',
		);
	}
	
	public function behaviors(){
	    Yii::import('lcatalog.extensions.nestedset.*');
		
		return array(
	        'TreeBehavior' => array(
	            'class'     => 'lcatalog.extensions.nestedset.TreeBehavior',
	            '_idCol'    => 'Id',
	            '_lftCol'   => 'LLeaf',
	            '_rgtCol'   => 'RLeaf',
	            '_lvlCol'   => 'Level',
	            '_rgtValue' => 'RLeaf',
	            '_lftValue' => 'LLeaf',
	        )
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
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Alias',$this->Alias,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('LLeaf',$this->LLeaf);
		$criteria->compare('RLeaf',$this->RLeaf);
		$criteria->compare('Level',$this->Level);
		$criteria->compare('IsVisible',$this->IsVisible);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 * Для CHtml::activeDropDownList список возможных категорий
	 * @return array('value' => 'value text')
	 */
	public static function getOrderedList(){
		$criteria = new CDbCriteria;
		$criteria -> order = 'LLeaf';
		$criteria -> select = 't.*';
		$categories = Category::model()->findAll($criteria);
		
		foreach($categories as $cat){
			$prefix = '';
			for($i=$cat->Level;$i>0;$i--) $prefix.= '-';
			$cat -> Name = $prefix .' '. $cat -> Name;
		}
		
		return CHtml::listData($categories, 'Id', 'Name');		
	}
	
	/**
	 * Для CHtml::activeDropDownList список возможных категорий сущностей
	 * @return array('value' => 'value text')
	 */
	public static function getOrderedEntities(){
		$coreEntity = Category::model() -> findByAttributes(array('Alias' => 'Entities'));		
		if(empty($coreEntity)) return array();

		$criteria = new CDbCriteria;
		$criteria -> order = 'LLeaf';
		$criteria -> select = 't.*';
		$criteria -> addCondition("t.LLeaf>:left AND t.RLeaf<:right");
		$criteria -> params = array(
			':left'  => $coreEntity->LLeaf,
			':right' => $coreEntity->RLeaf,
		);
		
		$categories = Category::model() -> findAll($criteria);

		/*foreach($categories as $cat){
			$prefix = '';
			for($i=$cat->Level;$i>0;$i--) $prefix.= '-';
			$cat -> Name = $prefix .' '. $cat -> Name;
		}*/
		
		return CHtml::listData($categories, 'Id', 'Name');		
	}
	
	
	/**
	 * @return array_of_Property
	 */
	public function getProperties(){
		return Property::model() -> findAllByAttributes(array('categoryId' => intval($this -> Id)));
		/*return empty($property) ? false : $property;*/
	}
	
	/**
	 * получить настраеваемые характеристики категории
	 * @return array_of_property
	 */
	public function getCustomProperties(){
		return Property::model() -> findAllByAttributes(array(
			'categoryId' => intval($this -> Id),
			'IsCustom'   => 1,
		));
	}

	/**
	 * @return array_of_Relations
	 */
	public function getRelations(){
		return Relation::model() -> findAllByAttributes(array('categoryId' => intval($this -> Id)));
		/*return empty($relations) ? false : $relations;*/
	}
	
	
	/**
	 * Получить модель характеристики по алиасу
	 * TODO: хранить в памяти объекта Категории все характеристики, имеющие к ней отношение
	 * @return Property | false 
	 */
	public function getPropertyByAlias($alias){
		$property = Property::model() -> findByAttributes(array(
			'Alias'      => $alias,
			'categoryId' => $this -> Id,
		));
		
		return empty($property) ? false : $property;
	}
	
	/**
	 * @return int | false 
	 */
	public function getRelationByAlias($alias){
		$relation = Relation::model() -> findByAttributes(array(
			'Alias'      => $alias,
			'categoryId' => $this -> Id,
		));
		
		return empty($relation) ? false : $relation;
	}	
	
	public function getGoodList(){
		$goods = Good::model() -> findAllByAttributes(array(
			'categoryId' => $this -> Id,
		));
		return array('пусто' => 'пусто') + CHtml::listData($goods, 'Id', 'Name');
	}
	
	
	/**
	 * Категорию можно удалять только если нет дочерних категорий и товаров
	 */
	public function delete(){
		if(!$this->hasChildNodes() && 0 === Good::model() -> countByAttributes(array('categoryId' => $this->Id))){
			if(parent::delete()){
				Property::model() -> deleteAllByAttributes(array('categoryId' => $this->Id));
				return true;
			}
		}

		return false;
	}
	
}