<?php


/**
 * Модель фильтра товаров
 * @author yura
 */
class GoodFilter extends CModel{
    
    /**
     * goods per page
     * @var int
     */
    public $gpp = 20;
    
    /**
     * use index or not
     * @var bool
     */
    public $useIndex = false;
    
    
    /**
     * характеристика по которой следует проводить сортировку
     * @var string
     */
    public $order = false;
    
    /**
     * типы условий, который бывают у этого фильтра
     * используется для автоопределения:
     * 1. при формировании ссылок pager'а
     * 2. при определении какие параметры брать из $_POST или $_GET
     * @var array
     */
    private $_conditionTypes = array('category','goodColumn','range','checkbox','radio','relation','price');
    
    /**
     * имя параметра, который отвечает за номер текущей страницы
     * @var string
     */
    public $pageVar = 'p';

    /**
     * массив условий фильтра
     * @var array_of_GoodFilterCondition
     */
    private $_conditions = array();
    
    /**
     * массив условий типа LPropertyCondition
     * фактически подмассив для $_conditions
     * @var array
     */
    private $_propertyConditions = array();
        
    /**
     * объект CDbCriteria, соотв. запросу к бд, за который отвечает данный фильтр
     * @var CDbCriteria
     */
    private $_criteria = NULL;
    
    /**
     * параметры фильтра, которые должны сохраняться при проходе по пейджеру
     * @var array
     */
    private $_betweenPageParameters = array();
    
    /**
     * получить условия фильтра
     */
    public function getConditions(){
        return $this -> _conditions;
    }
    
    
    public function getPropertyConditions(){
        return $this -> _propertyConditions;
    }
    
    
    /**
     * возвращает параметры фильтра, которые должны сохраняться при проходе по пейджеру 
     */
    public function getParamsSavedBetweenPages(){
        $this -> _betweenPageParameters['GoodFilter[gpp]']   = $this -> gpp;
        $this -> _betweenPageParameters['GoodFilter[order]'] = $this -> order;
        
        return $this -> _betweenPageParameters;
    }
    
    /**
     * Инициализация данных фильтра по данным, переданным пользователем
     */
    public function analizeInput(){
        if    (count($_POST) > 0) $input = $_POST;
        elseif(count($_GET)  > 0) $input = $_GET;
        else  return;
        
        if(isset($input['GoodFilter']))
            $this -> attributes = $input['GoodFilter'];

        if(is_array($this -> _conditionTypes)) foreach($this -> _conditionTypes as $type){
            $className = $this -> buildConditionClassnameByType($type);
            
            if(isset($input[$className]) && is_array($input[$className])) foreach($input[$className] as $propertyAlias => $params){
                if(false !== $condition = $this -> getFilterCondition($type,$propertyAlias)){
                    $condition -> attributes = $params;
                    $safeAttributes = $condition -> getSafeAttributeNames();
                    
                    if(is_array($safeAttributes)) foreach($safeAttributes as $attribute){
                        $this -> _betweenPageParameters["{$className}[{$propertyAlias}][{$attribute}]"] = $condition -> $attribute;
                    }
                }
            }
        }
    }
    
    /**
     * построение имени класса по типу условия
     * @param string $type
     */
    private function buildConditionClassnameByType($type){
        return 'L' . ucfirst($type) . 'Condition';
    }
    
    /**
     * Получить объект условия по типу и характеристике
     * @param string $type
     * @param Property $property
     * @return false | GoodFilterCondition
     */
    public function getFilterCondition($type,$propertyAlias){
        $className = $this -> buildConditionClassnameByType($type);
        
        if(is_array($this -> _conditions)) foreach($this -> _conditions as $condition){
            if(isset($condition -> propertyAlias) &&  $condition -> propertyAlias == $propertyAlias && get_class($condition) == $className)
                return $condition;
        }
        
        return false;
    }
    
    
    /**
     * Создать объект условия по типу и характеристике
     * @param string $type
     * @param Property $property
     * @return false | GoodFilterCondition
     */
    public function createPropertyFilterCondition($type,$propertyAlias,$params=NULL){
        if(empty($type) || empty($propertyAlias))
            throw new CException('Некорректное создание компонента фильтра');
        
        $className = $this -> buildConditionClassnameByType($type);
        
        $condition = new $className;
        if(is_array($params)) $condition->attributes = $params;
        $condition -> setPropertyByAlias($propertyAlias);
        
        if(false !== $this -> addCondition($condition))
            return $condition;
        
        throw new CException('Некорректная работа с фильтром');
    }
    
    /**
     * построение CDbCriteria по $_conditions
     * @return CDbCriteria
     */
    public function getCriteria(){
        if($this -> _criteria !== NULL) return $this -> _criteria;
        
        $criteria = new CDbCriteria;
        $criteria -> select = 't.*';
        foreach($this -> _conditions as $condition){
            $condition -> addConditions($criteria);
        }
        
        if(false !== $this -> order){           
            $criteria -> order = $this -> order;
        }                                       

                
        $this -> _criteria = $criteria;
        return $this -> _criteria;
    }
    
    /**
     * поиск товаров по индексу
     * @return ...
     */
    public function indexSearchByShop(Shop $shop){
        $query = '';
        foreach($this -> _conditions as $condition){
            $condition -> addIndexCondition($query);
        }
        
        return GoodIndex::index($shop) -> search($query);
    }
    
    
    /**
     * построение CActiveDataProvider
     */
    public function getDataProvider($additionalParams=array()){
        $params = array_merge(
            $this->getParamsSavedBetweenPages(),
            $additionalParams
        );
        
        $modelClass = $this -> useIndex ? 'GoodIndex' : 'Good';
        
        return new CActiveDataProvider($modelClass,array(
            'criteria'   => $this -> getCriteria(),
            'pagination' => array(
                'pageSize' => $this->gpp,
                'pageVar'  => $this->pageVar,
                'params'   => $params,
            ),
        ));
    }
    
    /**
     * список общих колонок (характеристик, сущностей и пр. у товаров)
     * у всех товаров, представленных этим фильтром
     * @return array_of_strings
     */
    public function getColumns(){
        $result = array();
        
        $cat = Yii::app() -> controller -> getCategory();       
        if(!empty($cat)){
            $properties = $cat -> getProperties();
            if(is_array($properties)) foreach($properties as $property){
                $divId = '\"datacell_{$data->Id}_'.$property->Id.'\"';
                $var   = '$data->getPropertyValueByAlias("'.$property->Alias.'",true)';
                $result[] = array(
                    'name'  => $property->Name,
                    'value' => '"<div id='.$divId.'>" .'.$var.'."</div>"',
                    'type'  => 'raw',
                );
            }
            
            $relations = $cat -> getRelations();
            if(is_array($relations)) foreach($relations as $relation){
                $divId = '\"relationcell_{$data->Id}_'.$relation->Id.'\"';
                $var   = '$data->getRelationValueByAlias('.$relation->Alias.',true)';
                $result[] = array(
                    'name'  => $relation->EntityCategory->Name,
                    'value' => '"<div id='.$divId.'>" .'.$var.'."</div>"',
                    'type'  => 'raw',
                );
            }
        }
        
        return $result;
    }

    /**
    /**
     * получить колонки с ценами для настраеваемых характеристик
     * @return mixed
     */
    public function getCustomPropertiesPricesColumns(){
        $result = array();
        $cat = Yii::app() -> controller -> getCategory();
        if(!empty($cat)){
            $properties = $cat -> getCustomProperties();
            if(is_array($properties)) foreach($properties as $property){
                $listValues = $property -> getListValues();
                if(is_array($listValues)) foreach($listValues as $listValue){
                   $result[] = array(
                      'name'  => $listValue -> Name,
                      'value' => '"<div id=\'pricecustompropertycell_{$data->Id}_'.$listValue->Id.'\'>" . $data -> getPrlistPrice('.Yii::app() -> controller -> getShop() -> Id.','.$listValue->Id.'). "</div>"',
                      'type'  => 'raw',
                   );
               }
           }
       }
       return $result;
   }


    /**
     * получить колонки для спецпредложений
     * @return mixed
     */
    public function getOffersColumns(){
       $result = array();
       $offers = Offer::findAllByShop(Yii::app() -> controller -> getShop() -> Id);
       if(is_array($offers)) foreach($offers as $offer){
            $result[] = array(
                'name'  => "$offer->Name",
                'value' => 'CHtml::checkBox("offer_".$data->getPrice()->Id."_'.$offer->Id.'",$data->isOfferActive('.$offer->Id.'),array())',
                'type'  => 'raw',
            );
        }
        return $result;
    }
    
    /**
     * добавить условие фильтрации
     * @param GoodFilterCondition $condition
     * @return bool
     */
    public function addCondition($condition){
        if(! $condition instanceof LGoodFilterCondition) return false;
        $this -> _conditions[] = $condition;
        if($condition instanceof LPropertyCondition) $this -> _propertyConditions[] = $condition;
        
        return true;
    }
    
    public function attributeNames(){
        return array();
    }
    
    public function rules(){
        return array(
            array('gpp, order','safe'),
        );
    }
}

/**
 * условие фильтрации
 * @author yura
 */
abstract class LGoodFilterCondition extends CModel{
    
    /**
     * Получить тип объекта по имени класса
     */
    public function getType(){
        $str = get_class($this);
        return strtolower(substr($str,1,count($str) - 10)); 
    }
    
    /**
     * добавляет в $criteria правильные фрагменты для sql-запроса в случае, если используется индексирование
     * если в дочернем классе не переопределен - делегирует свою функцию методу addConditions
     */
    public function addIndexConditions(&$query){
        throw new CException('Эту функцию нужно переопределить');
    }
    
    /**
     * добавляет в $criteria правильные фрагменты для sql-запроса
     * @param CDbCriteria $criteria
     */
    abstract public function addConditions(CDbCriteria $criteria);
    
    public function getSafeAttributeNames(){
        throw new CException('Эту функцию нужно переопределить'); 
    }
    
    public function attributeNames(){
        return array();
    }
}

/**
 * условие фильтрации по некоторой характеристике
 * @author yura
 */
abstract class LPropertyCondition extends LGoodFilterCondition{ 
    protected $_property;
    
    public function __construct($property = NULL){
        
        if(!empty($property))
            $this -> _property = $property;
    }
    
    public function setPropertyByAlias($alias){
        $this -> _property = Property::model() -> findByAttributes(array('Alias' => $alias));
        
        if(empty($this -> _property))
            throw new CException("Характеристика $alias не существует");
    }
    
    private function checkProperty(){
        if(empty($this -> _property)) throw new CException('Характеристика не иницализирована');
    }
    
    public function getProperty(){
        $this -> checkProperty();
        return $this -> _property;
    }
    
    public function getPropertyId(){
        $this -> checkProperty();       
        return $this -> _property -> Id;
    }
    
    public function getPropertyAlias(){
        $this -> checkProperty();
        return $this -> _property -> Alias;
    }
    
    public function getPropertyName(){
        $this -> checkProperty();
        return $this -> _property -> Name;
    }
    
}

/**
 * условие соответствия товара по id категории
 * @author yura
 */
class LCategoryCondition extends LGoodFilterCondition{
    /**
     * id категории
     * @var int
     */
    private $_value;
    
    /**
     * Отображать или нет - товары дочерних категорий
     * @var bool
     */
    private $_displayInherited;
    
    public function __construct($value,$displayInherited = false){
        $this -> _value = intval($value);
        $this -> _displayInherited = $displayInherited;
    }
    
    public function addConditions(CDbCriteria $criteria){
        if(!$this -> _displayInherited){
            $criteria->compare('t.categoryId',$this->_value);
            return;
        }
        
        $criteria -> join .= " INNER JOIN lcatalog__Category AS cat ON cat.LLeaf>=:leftcategory AND cat.RLeaf<=:rightcategory";
        $criteria -> addCondition('t.categoryId=cat.Id');
        $criteria -> params[':leftcategory']  = Yii::app() -> controller -> getCategory() -> LLeaf;
        $criteria -> params[':rightcategory'] = Yii::app() -> controller -> getCategory() -> RLeaf;
    }
}

/**
 * Фильтрация по производителям
 * @author yura
 */
class LProducersCondition extends LGoodFilterCondition{
    
    /**
     * @var array of producers ids
     */
    private $_producers;
    
    public function __construct($producers){
        $this -> _producers = $producers;
    }
    
    public function addConditions(CDbCriteria $criteria){
        if(count($this -> _producers) == 0) return;
        
        foreach($this -> _producers as $key => $value)
            $this -> _producers[$key] = intval($value);
        
        $str = implode(',',$this -> _producers);
        $criteria -> addCondition("t.producerId IN ($str)");
    }
}


/**
 * условие для характеристики типа float
 * @author yura
 *
 */
class LRangeCondition extends LPropertyCondition{
    public $left  = '';
    public $right = '';
    
    public function getSafeAttributeNames(){
        return array('left', 'right');
    }

    public function addConditions(CDbCriteria $criteria){
        if($this -> left === '' && $this -> right === '') return; 
        
        if(empty($this -> propertyAlias) || empty($this -> propertyId))
            throw new CException('Не указана характеристика');
        
        $propertyName = "prvaluefloat_{$this->propertyAlias}";
        $criteria -> join .= " INNER JOIN lcatalog__PrvalueFloat AS $propertyName ON $propertyName.goodId=t.Id";
        $criteria -> addCondition("$propertyName.propertyId=:{$propertyName}_id");
        $criteria -> params[":{$propertyName}_id"] = $this -> propertyId;
        
        if(!empty($this -> left)){
            $criteria -> addCondition("$propertyName.Value>=:{$propertyName}_left");
            $criteria -> params[":{$propertyName}_left"] = $this -> left;
        }

        if(!empty($this -> right)){
            $criteria -> addCondition("$propertyName.Value<=:{$propertyName}_right");
            $criteria -> params[":{$propertyName}_right"] = $this -> right;
        }               
    }

}

/**
 * условие соответствия товара по значению характеристики типа List
 * @author yura
 */
class LCheckboxCondition extends LPropertyCondition{
    public $values = array();
    
    public function getSafeAttributeNames(){
        return array('values');
    }
    
    public function addIndexConditions(&$query){
        $tempCondition = array();
        if(is_array($this -> values)) foreach($this -> values as $key => $value){
            $fieldName = Good::model() -> getIndexFieldNameByProperty($this -> getProperty());
            $tempCondition[] = "$fieldName:\"$value\"";
            $criteria -> params[":{$propertyName}_{$key}"] = $value;    
        }
        
        $query = implode(' OR ',$tempCondition);
    }
    
    public function addConditions(CDbCriteria $criteria){
        if(empty($this -> values)) return;
                
        if(empty($this -> propertyAlias) || empty($this -> propertyId))
            throw new CException('Не указана характеристика');
            
        $propertyName = "prvaluelist_{$this->propertyId}";
        $criteria -> join .= " INNER JOIN lcatalog__PrvalueList AS $propertyName ON $propertyName.goodId=t.Id";
        $criteria -> addCondition("$propertyName.propertyId=:$this->propertyId");
        $criteria -> params[":$this->propertyId"] = $this -> propertyId;
        
        $tempCondition = array();
        if(is_array($this -> values)) foreach($this -> values as $key => $value){
            $tempCondition[] = "$propertyName.valueId=:{$propertyName}_{$key}";
            $criteria -> params[":{$propertyName}_{$key}"] = $value;    
        }
        
        //var_dump($tempCondition);
        //exit();
        $criteria -> addCondition(implode(' OR ',$tempCondition));
    }
}


/**
 * условие соответствия товара по значению характеристики типа List
 * @author yura
 */
class LRadioCondition extends LPropertyCondition{
    public $value = 'пусто';
    
    public function getSafeAttributeNames(){
        return array('value');
    }
    
    public function addConditions(CDbCriteria $criteria){
        if(empty($this -> value) || $this -> value === 'пусто') return;
                
        if(empty($this -> propertyAlias) || empty($this -> propertyId))
            throw new CException('Не указана характеристика');
            
        $propertyName = "prvaluelist_{$this->propertyId}";
        
        $criteria -> join .= " INNER JOIN lcatalog__PrvalueList AS $propertyName ON $propertyName.goodId=t.Id";
        $criteria -> addCondition("$propertyName.propertyId=:$this->propertyId");
        $criteria -> params[":$this->propertyId"] = $this -> propertyId;
        
        $criteria -> addCondition("$propertyName.valueId=:{$propertyName}");
        $criteria -> params[":{$propertyName}"] = $this -> value;   
    }
}



/**
 * условие соответствия товара некоторой сущности
 * @author yura
 */
class LRelationCondition extends LPropertyCondition{
    public $value;
    
    public function setPropertyByAlias($alias){
        $this -> _property = Relation::model() -> findByAttributes(array('Alias' => $alias));
        
        if(empty($this -> _property))
            throw new CException("Связь $alias с сущностью не существует");
    }
        
    public function getSafeAttributeNames(){
        return array('value');
    }
    
    public function getPropertyName(){
        return $this -> _property -> Category -> Name;
    }
        
    public function addConditions(CDbCriteria $criteria){
        $property = $this -> getProperty();
        
        if(empty($this -> value) || $this -> value == 'пусто') return;
        
        $relationValue = 'relationvalue_' . $property -> Id;
        $relationName  = 'relation_' . $property -> Id;
        
        $criteria -> join .= " INNER JOIN lcatalog__PrvalueEntity AS $relationValue ON $relationValue.goodId=t.Id"
                          .  " INNER JOIN lcatalog__Good AS $relationName ON $relationName.Id=$relationValue.entityId"
        ;
        
        $criteria -> compare("$relationName.Id",$this -> value);
    }
    
}


/**
 * фильтрация по цене и соответствия магазину
 * @author yura
 */
class LPriceCondition extends LPropertyCondition{
    public $minPrice = '';
    public $maxPrice = '';
    
    public $displayGoodsWithZeroPrice = 0;
    
    private $_alias = 'shop';
    
    public function __construct(Shop $shop = NULL){
        if(!empty($shop))
            $this -> _property = $shop;
    }
    
    /**
     * (non-PHPdoc)
     * @see lite/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LPropertyCondition::setPropertyByAlias()
     */
    public function setPropertyByAlias($alias){
        $this -> _alias = $alias;
        
        
        if($alias === 'shop'){
            $this -> _property = Yii::app() -> controller -> getShop();
            
            if(empty($this -> _property))
                throw new CException("Магазин $alias не существует");
        }
    }
        
    /**
     * (non-PHPdoc)
     * @see lite/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LGoodFilterCondition::getSafeAttributeNames()
     */
    public function getSafeAttributeNames(){
        return array('minPrice','maxPrice','displayGoodsWithZeroPrice');
    }
    
    /**
     * (non-PHPdoc)
     * @see hp2/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LPropertyCondition::getPropertyName()
     */
    public function getPropertyName(){
        return 'Цена';
    }
    
    /**
     * (non-PHPdoc)
     * @see hp2/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LPropertyCondition::getPropertyAlias()
     */
    public function getPropertyAlias(){     
        return $this -> _alias;
    }
    
    /**
     * (non-PHPdoc)
     * @see hp2/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LGoodFilterCondition::addConditions()
     */
    public function addConditions(CDbCriteria $criteria){
        if(false === $this -> minPrice && false === $this -> maxPrice && NULL === $this -> getProperty())
            return;
            
        $criteria -> join .= " INNER JOIN lcatalog__prices AS price ON price.goodsId=t.Id";
        $criteria -> compare("price.prlistsId",0);
        $criteria -> select .= ',price.Id AS priceId';
        
        if( $this -> getProperty() !== false )
            $criteria -> compare("price.shopId",$this -> getProperty() -> Id);
        
        if($this -> minPrice != ''){
            $criteria -> addCondition("price.Price >= :min");
            $criteria -> params[':min'] = $this -> minPrice;
        }
        
        if($this -> maxPrice != ''){
            $criteria -> addCondition("price.Price <= :max");
            $criteria -> params[':max'] = $this -> maxPrice;
        }
        
//      var_dump($this -> minPrice);
//      var_dump($this -> maxPrice);
//      var_dump($this -> displayGoodsWithZeroPrice);
//      var_dump(false === $this -> minPrice && false === $this -> maxPrice && !$this->displayGoodsWithZeroPrice);
//      exit();
        
        if('' == $this -> minPrice && '' == $this -> maxPrice && !$this->displayGoodsWithZeroPrice){
            $criteria -> addCondition("price.Price > 0");
        }
    }
    
}


/**
 * фильтрация по производителю
 * @author yura
 */
class LGoodColumnCondition extends LPropertyCondition{  
    public  $value   = false;
    private $_column = false;
    
    /**
    * (non-PHPdoc)
    * @see lite/scripts/modules/lcatalog/widgets/LCatalogFilter/models/LGoodFilterCondition::getSafeAttributeNames()
    */
    public function getSafeAttributeNames(){
        return array('value');
    }   
    
    public function setPropertyByAlias($alias){
        $this -> _column = $alias;
    }
    
    public function getPropertyAlias(){
        return $this -> _column;
    }
    
    public function getPropertyName(){
        $labels = Good::model() -> attributeLabels();
        return isset($labels[$this -> _column]) ? $labels[$this -> _column] : $this -> _column;
    }
    
    public function addConditions(CDbCriteria $criteria){
        $column = $this -> _column;
        if($this -> value === false) return;
        $criteria -> compare('t.' . $this -> _column,$this -> value);
    }
    
    /**
     * список возможных значений для CHtml::dropDown
     */
    public function possibleValues(){   
        return array('' => 'Неважно') + Good::getPossibleCreators();
    }
    
}