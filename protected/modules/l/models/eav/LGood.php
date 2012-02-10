<?php

/**
 * This is the model class for table "lcatalog__Good".
 *
 * The followings are the available columns in table 'lcatalog__Good':
 * @property integer $Id
 * @property integer $categoryId
 * @property string $Name
 * @property string $Description
 * @property integer $IsVisible
 */
class Good extends CActiveRecord
{
	/**
	 * FK для цены товара
	 * @var NULL | int
	 */
	public $priceId;
	
	/**
	 * FK для магазина товара
	 * @var NULL | int
	 */
	public $shopId;
	
	private $_propertyValues = array();
	private $_relationValues = array();
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__get()
	 */
	public function __get($name){
		if(isset($this -> _propertyValues[$name])) return $this -> _propertyValues[$name];
		if(isset($this -> _relationValues[$name])) return $this -> _relationValues[$name];
		
		if(false !== $property = $this -> getPropertyByKey($name)) return $this -> getPropertyValue($property,false);
		if(false !== $relation = $this -> getRelationByKey($name)) return $this -> getRelationValue($relation,false);
				
		return parent::__get($name);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CActiveRecord::__set()
	 */
	public function __set($name,$value){
		if(false !== $property = $this -> getPropertyByKey($name))
			return $this -> _propertyValues[$name] = $value;
			//return $this -> setPropertyValue($property,$value);
		
		if(false !== $relation = $this -> getRelationByKey($name))
			return $this -> _relationValues[$name] = $value;
			//return $this -> setRelationValue($relation,$value);
		
		return parent::__set($name,$value);		
	}
	
	/**
	 * получить "полное" имя товара (с названием производителя)
	 * @return string
	 */
	public function getFullName(){
		$producer = $this -> Producer;
		return empty($producer) ? $this -> Name : $producer -> Name . ' ' . $this -> Name;
	}
    
    /**
     * @var false | string
     */
    public $ImageUrl = false;
    
    /**
     * get <img src=...> with first good image
     * @return html
     */
    public function getImage(){
        $url = $this -> getMediaPreview();
        return CHtml::image($url);
    }
	
	/**
	 * (non-PHPdoc)
	 * @see 1.1.6/framework/db/ar/CActiveRecord::save()
	 */
	public function save(){
		if(!parent::save()) return false;
		
		foreach($this -> _propertyValues as $name => $value){
			$property = $this -> getPropertyByKey($name);
			$this -> setPropertyValue($property,$value);
		}
		
		foreach($this -> _relationValues as $name => $value){
			$relation = $this -> getRelationByKey($name);
			$this -> setRelationValue($relation,$value);
		}
		
		return true;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CModel::getSafeAttributeNames()
	 */
	public function getSafeAttributeNames(){
		$propertyKeys = array();
		$properties = $this -> Category -> getProperties();
		if(is_array($properties)) foreach($properties as $property) $propertyKeys[] = $this -> getPropertyKey($property);
		
		$relationKeys = array();
		$relations = $this -> Category -> getRelations();
		if(is_array($relations)) foreach($relations as $relation) $relationKeys[] = $this -> getRelationKey($relation);
		
		
		return array_merge(parent::getSafeAttributeNames(),$propertyKeys,$relationKeys);
	}
	
	/**
	 * есть ли данный товар в заданном магазине
	 * @param int $shopId
	 * @return bool
	 */
	public function existsInShop($shopId){
		return Prices::model() -> exists('goodsId=:goodid AND shopId=:shopid AND prlistsId=0',array(
			':goodid' => $this -> Id,
			':shopid' => $shopId,
		));
	}
	
	/**
	 * получить все цены на этот товар всех магазинов
	 * @return CActiveDataProvider
	 */
	public function getShopsPrices(){
		$criteria = new CDbCriteria;
		
		$criteria -> compare('goodsId',$this -> Id);
		$criteria -> compare('prlistsId',0); 
		$criteria -> order = 'shopId';
		
		return new CActiveDataProvider('Prices',array(
			'criteria'   => $criteria,
			'pagination' => false,
		));
	}
	
	/**
	 * ссылка на карточку товара
	 */
	public function getPageUrl(){
		return Yii::app() -> createUrl('/lcatalog/good',array('id' => $this -> Id));
	}
	
	/**
	 * Получить значение характеристики
	 * @param string $alias
	 * return value | false
	 */
	public function getPropertyValueByAlias($alias,$isDisplayed = false){
		if(false !== $property = $this -> getGoodPropertyByAlias($alias)){
			return $this -> getPropertyValue($property,$isDisplayed);
		}
		
		return false;
	}
	
	/**
	 * Получить значение связанной характеристики
	 * @param string $alias
	 * return value | false
	 */
	public function getRelationValueByAlias($alias,$isDisplayed = false){
		if(false !== $relation = $this -> getGoodRelationByAlias($alias)){
			return $this -> getRelationValue($relation,$isDisplayed);
		}
		
		return false;		
	}
	
	/**
	 * Получить значение связи с сущностью товара
	 * @param Relation $relation
	 * @return false | mixed
	 */
	public function getRelationValue(Relation $relation,$isDisplayed = false){
		$params = array(
			':goodid' => $this -> Id,
			':relationid' => $relation -> Id,
		);
		
		if(!$isDisplayed){
			$sql = "SELECT t.entityId FROM lcatalog__PrvalueEntity AS t"
			      ." WHERE t.goodId=:goodid AND t.relationId=:relationid";
			;
			$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
			return trim($value) == NULL ? 'пусто' : $value;		
		}
		
		$sql = "SELECT t.Name FROM lcatalog__Good AS t"
		      . " INNER JOIN lcatalog__PrvalueEntity AS prvalue ON prvalue.entityId=t.Id"
		      . " WHERE prvalue.goodId=:goodid AND prvalue.relationId=:relationid";
		;
		$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
		return trim($value) == NULL ? 'пусто' : $value;
	}
	
	/**
	 * Получить значение характеристики товара
	 * @param Property $property
	 * @return false | mixed
	 */
	public function getPropertyValue(Property $property,$isDisplayed = false){
		$params = array(
			':goodid' => $this -> Id,
			':propertyid' => $property -> Id,
		);
		
		switch($property -> Type){
			case 'float':
				$sql   = "SELECT Value FROM lcatalog__PrvalueFloat"
				       . " WHERE goodId=:goodid AND propertyId=:propertyid";
				$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
				if(trim($value) == NULL) return 'пусто';
				return $value;
			case 'text':
				$sql   = "SELECT Value FROM lcatalog__PrvalueText"
				       . " WHERE goodId=:goodid AND propertyId=:propertyid";
				$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
				if(trim($value) == NULL) return 'пусто';
				return $value;
			case 'bit':
				$sql   = "SELECT Value FROM lcatalog__PrvalueBit"
				       . " WHERE goodId=:goodid AND propertyId=:propertyid";
				$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
				if(trim($value) == NULL) return 'пусто';
				return $value;
			case 'list':
				if($isDisplayed){
					$sql   = "SELECT listvalue.Name FROM lcatalog__PrvalueList AS value"
					       . " INNER JOIN lcatalog__PrListValue AS listvalue ON listvalue.Id=value.valueId"
					       . " WHERE value.goodId=:goodid AND value.propertyId=:propertyid";
				}else{
					$sql   = "SELECT valueId FROM lcatalog__PrvalueList"
				    	   . " WHERE goodId=:goodid AND propertyId=:propertyid";
				}
				$value = $this -> dbConnection -> createCommand($sql) -> queryScalar($params);
				if(trim($value) == NULL) return 'пусто';				
				return $value;				
			default:
				return "поведение для типа характеристик '$property->Type' не определено";
		}
	}
	
	/**
	 * Задать значение характеристики товара
	 * @param Property $property
	 * @param mixed $value
	 * @return bool
	 */
	public function setPropertyValue(Property $property, $value){
		$params = array(
			'propertyId' => $property->Id,
			'goodId' => $this -> Id,
		);
		
		if($property -> Type == 'list')  PrvalueList::model()  -> deleteAllByAttributes($params);
		if($property -> Type == 'float') PrvalueFloat::model() -> deleteAllByAttributes($params);
		if($property -> Type == 'text')  PrvalueText::model()  -> deleteAllByAttributes($params);
		if($property -> Type == 'bit')   PrvalueBit::model()   -> deleteAllByAttributes($params);
		
		if($value === 'пусто') return true;
		
		$className = 'Prvalue' . ucfirst($property -> Type);		
		$v = new $className;
		$v -> goodId = $this -> Id;
		$v -> propertyId = $property -> Id;
		
		if($property -> Type == 'list')
			$v -> valueId = $value;
		else			
			$v -> Value = $value;
		
		return $v -> save();
	}
	
	
	/**
	 * Задать значение связи товара и сущности
	 * @param Relation $relation
	 * @param unknown_type $value
	 */
	public function setRelationValue(Relation $relation, $value){
		$params = array(
			'relationId' => $relation->Id,
			'goodId'     => $this -> Id,
		);
		
		PrvalueEntity::model()  -> deleteAllByAttributes($params);		
		if($value === 'пусто') return true;
		
		$v = new PrvalueEntity();
		$v -> goodId     = $this -> Id;
		$v -> relationId = $relation -> Id;
		$v -> entityId   = $value;
		
		return $v -> save();
	}
	
	/**
	 * Получить объект характеристики товара
	 * @return false | Property
	 * @param string $alias
	 */
	private function getGoodPropertyByAlias($alias){
		$cat = $this -> getCategory();
		if(empty($cat)) return false;
		
		return $cat -> getPropertyByAlias($alias);
	}
	
	/**
	 * Получить объект связи с сущностью товара
	 * @return false | Relation
	 * @param string $alias
	 */
	private function getGoodRelationByAlias($alias){
		$cat = $this -> getCategory();
		if(empty($cat)) return false;
		
		return $cat -> getRelationByAlias($alias);
	}	
	
	/**
	 * получение объекта категории данного товара
	 * @return false | Category
	 */
	public function getCategory(){		
		return Yii::app() -> getModule('lcatalog') -> getCategory($this -> categoryId);
	}
	
	/**
	 * Возвращает урл главной превьюшки товара
	 * @param $preview
	 */
	public function getMediaPreview($previewNum='0',$mode='relative'){
		$arr = $this -> getMediaFiles('jpg');
		return isset($arr[0]) ? $arr[0]->getUrl($previewNum,$mode) : NULL;
	}


	/**
	 * Получить набор медиафайлов, с фильтром по типу
	 * @return array of mediafile models
	 *
	 * @author yura
	 */
	public function getMediaFiles($extension='all'){
		if($extension == 'all') return $this -> Media;

		$res = array();
		if(is_array($this->Media)){
			foreach($this->Media as $media){
				if($media -> Type -> Extension != $extension) continue;
				$res[] = $media;
			}
		}

		return $res;
	}	
	
	
	/**
	 * Сколько у товара медиафайлов
	 */
	public function countMediaFiles(){
		$sql =
		'SELECT COUNT(*) FROM lcatalog__mediafiles AS media'
		. ' INNER JOIN lcatalog__goods_mediafiles AS gm ON gm.mediafilesId=media.Id'
		. " WHERE gm.goodsId='$this->Id'";
		
		return $this->dbConnection->createCommand($sql)->queryScalar();
	}
	
	public static function getImageTag($imageUrl,$htmlOptions = array()){
		if(empty($imageUrl)) return;
		return CHtml::image(Yii::app() -> request -> hostInfo . "/media/" . $imageUrl,'медиа',$htmlOptions);
	}
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Good the static model class
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
		return 'lcatalog__Good';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('categoryId, Name', 'required'),
			array('categoryId, IsVisible, creatorId', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>256),
			array('Description', 'length', 'max'=>1000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, categoryId, Name, Description, IsVisible', 'safe', 'on'=>'search'),
		);
	}
	
	/**
	 * get data provider for media files
	 */
	public function getMediaDataProvider(){
		$criteria = new CDbCriteria;
		
		$criteria -> join = "INNER JOIN lcatalog__goods_mediafiles AS rel ON t.Id=rel.mediafilesId";
		$criteria -> compare('rel.goodsId',$this->Id);
		
		return new CActiveDataProvider('MediaFile',array(
			'criteria' => $criteria,
			'pagination' => false,
		));
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			//'Category' => array(self::BELONGS_TO,'Category','categoryId'),
			'Producer' => array(self::BELONGS_TO,'Good','producerId'),
			'Media'    => array(self::MANY_MANY, 'MediaFile', 'lcatalog__goods_mediafiles(goodsId,mediafilesId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$propertyNames = array();
		
		return $propertyNames + array(
			'Id' => 'ID',
			'categoryId' => 'Категория',
			'Name' => 'Имя',
			'Description' => 'Описание',
			'IsVisible' => 'Видимость',
			'creatorId' => 'Создатель',
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
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('IsVisible',$this->IsVisible);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 * цены настраеваемых характеристик
	 * @var unknown_type
	 */
	private $_prices = false;
	
	/**
	 * получить цены настраеваемых характеристик
	 */
	public function getCustomPropertiesPrices($shopId){
		if(false !== $this -> _prices) return $this -> _prices;
		
		$criteria = new CDbCriteria;
		$criteria -> compare('t.shopId',$shopId);
		$criteria -> compare('t.goodsId',$this -> Id);
		$criteria -> addCondition('t.prlistsId > 0');
		
		$prices = Prices::model() -> findAll($criteria);
		$this -> _prices = array(); 
		if(is_array($prices)) foreach($prices as $price){
			$this -> _prices[$price -> prlistsId] = $price;
		}
		
		return $this -> _prices;
	}
	
	/**
	 * получить цену одной настраеваемой характеристики
	 * @param int $shopId
	 * @param int $prlistId
	 */
	public function getPrlistPrice($shopId, $prlistId, $display = true){
		$prices = $this -> getCustomPropertiesPrices($shopId);
		if(isset($prices[$prlistId])) return $display ? $prices[$prlistId] -> Price : $prices[$prlistId];
		
		return $display ? 0 : NULL;
	}
		
	
	/**
	 * TODO: восстановить
	 */
	public function getTotalPrice($prlistIds = NULL){
		if(empty(Yii::app()->params['shopid'])) return 0;

		$prices = $this->getPrices();
		if(!is_array($prices)) return 0;

		$total = 0;
		foreach($prices as $price){
			if($price['prlistsId']==0
			|| (is_array($prlistIds) && in_array($price['prlistsId'],$prlistIds)) ){
				$total += intval($price['Price']);
			}
		}

		return $total;
	}
    
    private $_priceValue = false;

	public function getPriceValue(){
	    if(false !== $this -> _priceValue) return $this -> _priceValue;
        $price = $this -> getPrice();
        return empty($price) ? false : $price -> Price;
    }
	
	/**
	 * получить основную цену товара
	 * @return Prices | false
	 */
	public function getPrice(){
		$price = Prices::model() -> findByPk($this -> priceId);		
		return !empty($price) ? $price : false;
	}
    
    public function setPrice($value){
        $this -> _priceValue = $value;
    }
	
	/**
	 * создани ли данный товар данным пользователем
	 * @param int $userId
	 */
	public function isCreatedBy($userId){
		return intval($userId) > 0 && $this -> creatorId == $userId;
	}
	
	
	/**
	* получить список пользователей, которые могут являться создателями товара
	* @return array
	*/
	public function getPossibleCreators(){
		$criteria = new CDbCriteria();
	
		$criteria -> select .= ',CONCAT(t.firstname," ",t.lastname) AS lastname';
		$criteria -> join = 'INNER JOIN {{users}} AS u ON u.id=t.user_id'
		;
		$criteria -> compare('u.status',1);
	
		return CHtml::listData(Profile::model() -> findAll($criteria),'user_id','lastname');
	}
	
	
	/**
	 * При удалении товара - удалить все его связи
	 */
	public function delete(){
		if(!parent::delete()) return false;

		PrvalueBit::model()    -> deleteAllByAttributes(array('goodId' => $this -> Id));
		PrvalueFloat::model()  -> deleteAllByAttributes(array('goodId' => $this -> Id));
		PrvalueList::model()   -> deleteAllByAttributes(array('goodId' => $this -> Id));
		PrvalueText::model()   -> deleteAllByAttributes(array('goodId' => $this -> Id));
		PrvalueEntity::model() -> deleteAllByAttributes(array('goodId' => $this -> Id));
		
		return true;
	}
	
	/**
	 * получить алиас поля модели good, по которому можно получить или изменить значение характеристики
	 * @param Property $property
	 */
	public function getPropertyKey($property){
		return 'property_' . $property -> Id;
	}
	
	/**
	 * обратная функция для $this->getPropertyKey(), используется в $this->__get()
	 * @param string $propertyKey
	 * @return Property | false
	 */
	private function getPropertyByKey($propertyKey){
		$arr = explode('_',$propertyKey);
		if($arr[0] != 'property') return false;
		
		$property = Property::model() -> findByPk($arr[1]);
		return empty($property) ? false : $property;
	}
	
	/**
	* получить алиас поля модели good, по которому можно получить или изменить значение связи с характеристикой
	* @param Property $property
	*/
	public function getRelationKey($relation){
		return 'relation_' . $relation -> Id;
	}
	
	/**
	 * обратная функция для $this->getRelationKey(), используется в $this->__get()
	 * @param string $relationKey
	 * @return Relation | false
	 */
	private function getRelationByKey($relationKey){
		$arr = explode('_',$relationKey);
		if($arr[0] != 'relation') return false;
	
		$relation = Relation::model() -> findByPk($arr[1]);
		return empty($relation) ? false : $relation;
	}
	
	/**
	 * получить список колонок для карточки товара
	 * @return array of string
	 */
	public function getColumnNamesForView(){
		$result = array();
		
		$properties = $this->Category->getProperties(); 
		if(is_array($properties)) foreach($properties as $property)
			$result[] = array(
				'name' => $property->Name,
				'value' => $this->getPropertyValue($property,true),
			);//$this -> getPropertyKey($property) . ':text:' . $property->Name;
		
		$relations = $this->Category->getRelations();
		if(is_array($relations)) foreach($relations as $relation)
			$result[] = array(
				'name' => $relation->EntityCategory->Name,
				'value' => $this -> getRelationValue($relation,true),
			);//$this -> getRelationKey($relation) . ':text:' . $relation->EntityCategory->Name;
		
		return $result;
	}
	
	
	/**
	 * html code <img ...> for first media file
	 * @return html | false
	 */
	public function getFirstMediaImg($htmlOptions = array()){
		if(count($this -> Media) == 0)
			return false;
			
		return CHtml::image($this -> Media[0] -> getUrl(), '',$htmlOptions);
	}
	
	/**
	 * действует ли для данного товара указанное спецпредложение
	 * @param int $offerId
	 * @return bool
	 */
	public function isOfferActive($offerId){
		$price = $this -> getPrice();
		if(false === $price)
			throw new CException('Цены не существует, спецпредложения быть не может');
		
		return OfferPrice::model() -> countByAttributes(array(
			'offerId' => $offerId,
			'priceId' => $price -> Id,
		));
	}
    
    /**
     * @var false | string
     */
    private $_seriaName = false;
    
    /**
     * @var false | string
     */
    private $_colorName = false;
    
    /**
     * @return false | string
     */
    public function getSeriaName(){
        if(false !== $this -> _seriaName) return $this -> _seriaName;
        $seria = $this -> getSeria();
        if(empty($seria)) return false;
        
        $this -> _seriaName = $seria -> Name;
        return $this -> _seriaName; 
    }
    
    public function setSeriaName($value){
        $this -> _seriaName = $value;
    }

    /**
     * @return false | string
     */
    public function getColorName(){
        if(false !== $this -> _colorName) return $this -> _colorName;
        $color = $this -> getColor();
        if(empty($color)) return false;
        
        $this -> _colorName = $color -> Name;
        return $this -> _colorName; 
    }
    
    public function setColorName($value){
        $this -> _colorName = $value;
    }
    
    /**
     * Связанная сущность Серия
     * @return NULL | Good
     */
    public function getSeria(){
        $sql = "SELECT g.* FROM lcatalog__Good AS g"
             . " INNER JOIN lcatalog__PrvalueEntity AS value ON value.entityId=g.Id"
             . " INNER JOIN lcatalog__Relation AS rel ON rel.Id=value.relationId"
             . " WHERE rel.entityCategoryId = 4 AND value.goodId = " . intval($this->Id);
        ;
        
        return Good::model() -> findBySql($sql);
    }
    
    /**
     * Связанная сущность цвет
     * @return NULL | Good
     */
    public function getColor(){
        $sql = "SELECT g.* FROM lcatalog__Good AS g"
             . " INNER JOIN lcatalog__PrvalueEntity AS value ON value.entityId=g.Id"
             . " INNER JOIN lcatalog__Relation AS rel ON rel.Id=value.relationId"
             . " WHERE rel.entityCategoryId = 5 AND value.goodId = " . intval($this->Id);
        ;
        
        return Good::model() -> findBySql($sql);
    }    
    
    /**
     * returns name of the field in good index
     * @return string
     */
    public function getIndexFieldNameByProperty(Property $property){
        return 'property_' . $property -> Id;
    }
    
    /**
     * returns value of the field in good index
     * @return mixed
     */
    public function getIndexFieldValueByProperty(Property $property,$display){
        return $this -> getPropertyValue($property, $display);
    }
    
    /**
     * returns name of the field in good index
     * @return string
     */
    public function getIndexFieldNameByRelation(Relation $relation){
        return 'relation_' . $relation -> Id;
    }
    
    /**
     * returns value of the field in good index
     * @return mixed
     */
    public function getIndexFieldValueByRelation(Relation $relation,$display){
        return $this -> getRelationValue($relation, $display);
    }    
}