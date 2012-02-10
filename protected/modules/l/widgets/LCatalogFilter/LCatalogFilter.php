<?php

Yii::import('lcatalog.widgets.LCatalogFilter.models.*');

/**
 * Виджет фильтра товаров каталога
 * @author yura
 */
class LCatalogFilter extends CWidget{
	
	/**
	 * структура фильтра, в ней распознаются:
	 * {gpp},{gpp20,30,50} - (goods per page) количество товаров на странице
	 * {property|%propertyAlias,%filterType,%param1=%value1&%param2=%value2&...}
	 * {entity|%entityAlias,relation}
	 * 
	 * Если значение параметра не указано (то есть == false),
	 * то шаблон фильтра строится автоматически на основании параметра $this -> category
	 * 
	 * @var string | false
	 */
	public $template = false;
	
	/**
	 * место применения фильтра, вместе с объектом категории однозначно задает
	 * который html-код фильтра взять из ранее сгенерированных
	 * @var string
	 */
	public $Name = 'filter';
	
	/**
	 * объект категории, используемый для автоматического построения шаблона фильтра
	 * @var Category | false
	 */
	public $category = false;
	
	/**
	 * Содержит пользовательский ввод значений фильтра
	 * @var GoodFilter
	 */
	public $model = NULL;
	
	/**
	 * html-код фильтра, генерируется в результате работы данного виджета
	 * @var html
	 */
	private $_result = NULL;
	
	/**
	 * список возможный значений gpp (goods per page) по умолчанию
	 * @var array
	 */
	private $_defaultGpp = array(20,50,100);
		
	/**
	 * воспринимает {gpp} или {gpp20,30,50}
	 */
	private function renderGpp(){
		$pattern = '/{gpp([^}]*)}/';
		
		preg_match_all($pattern,$this->_result,$matches);
		if(!isset($matches[1])) return false;
		
		foreach($matches[1] as $match){
			$pages = $match == '' ? $this -> _defaultGpp : explode(',',$match);  			
			
			$pages = array_flip($pages);
			foreach($pages as $key=>$value) $pages[$key] = $key;
			
			$html  = CHtml::activeDropDownList($this -> model, 'gpp', $pages);
			$this->_result = preg_replace($pattern,$html,$this->_result,1);
		}
	}
	

	/**
	 * формирование структуры модели фильтра: характеристики
	 * воспринимает {property|propertyalias}
	 */
	private function initProperties(){
		$pattern = '/{property\|([^}]+)}/';
		
		preg_match_all($pattern,$this->_result,$matches);
		if(!isset($matches[1])) return false;
				
		foreach($matches[1] as $match){
			$attributes = explode(',',$match);
			if(count($attributes) < 2) continue;
			
			$propertyAlias = array_shift($attributes);
			$filterType    = array_shift($attributes);
			
			if(false === $this -> model -> getFilterCondition($filterType,$propertyAlias)){
				$params = array();
				if(count($attributes) > 0) parse_str(array_shift($attributes),$params);
				$this -> model -> createPropertyFilterCondition($filterType,$propertyAlias,$params);
			}
		}		
	}
	
	/**
	 * Генерация html по условию
	 */
	private function renderProperties(){
		$conditions = $this -> model -> getPropertyConditions();
		if(is_array($conditions)) foreach($conditions as $condition){
			$filterType = $condition -> getType();
			
			$html = $this -> render($filterType,array(
				'condition' => $condition,				
			),true);
			
			$pattern = '/{property\|'.$condition->propertyAlias.','.$filterType.'[^}]*}/';
			$this->_result = preg_replace($pattern,$html,$this->_result,1);
		}			
	}
	
	/**
	 * Возвращает урл для кнопки "сортировка"
	 * TODO: снять заглушку
	 * @param string $mode
	 */
	public function getOrderButtonUrl($mode){
		return 'http://yandex.ru';
	}
	
	
	public function initResult(){
		if($this -> template === false && $this -> category instanceof Category){
			$template = FilterOption::getTemplate($this -> category,$this -> Name);
		}else{
			$template = $this -> template;
		}
		
		$this -> _result = $template;
	}
	
	/**
	 * формирование структуры модели фильтра на основании шаблона
	 * 
	 * (non-PHPdoc)
	 * @see 1.1.6/framework/web/widgets/CWidget::init()
	 */
	public function init(){		
		$this -> model = new GoodFilter;
		
		$this -> initResult();				
		$this -> initProperties();
		
		$this -> model -> analizeInput();
	}
	
	/**
	 * формирование html на основании структуры модели фильтра
	 * 
	 * (non-PHPdoc)
	 * @see 1.1.6/framework/web/widgets/CWidget::run()
	 */
	public function run(){
		$this -> renderProperties();
		$this -> renderGpp();
		
		echo $this -> _result;
	}
}