<?php 

/**
 * Parent Controller for administrative interfaces
 * @author yurap
 */
class LCController extends CController{
	
	public $layout = '/layouts/cms';
	
	public $title = 'Редактор каталога';
	
	public function filters(){
		return array('checkAccessControl');
	}
	
	/**
	 * default rights control
	 * some controllers redefine it
     * @throws CHttpException
	 */
	public function filterCheckAccessControl($chain)
	{
		if(Yii::app() -> user -> isGuest)
			Yii::app() -> user -> loginRequired();
		
		if(Yii::app() -> getModule('user') -> isAdmin()) return $chain -> run();
		throw new CHttpException(Yii::t('Не хватает прав'));
	}
	
	
	/**
	 * returns value which defines the user rights level
	 * @return enum('full','truncated')
	 */
	public function getRightsMode(){
		return Yii::app() -> getModule('user') -> isAdmin() ? 'full' : 'truncated';
	}
	
	/**
	 * catalog editor tabs
     * @return array
	 */
	public function getTabs(){
		if($this -> id == 'shop')
			return $this -> getShopTabs();
		
		if($this -> id == 'price' || $this -> id == 'offer')
			return $this -> getPriceTabs();
			
		return $this -> getGoodTabs();
	}
	
	/**
	 * shop prices page tabs
     * @return array
	 */
	private function getPriceTabs(){
		$importUrl        = array('/lcatalog/price/import','id' => $this -> getShop() -> Id);
		$shopUrl          = array('/lcatalog/price','id' => $this -> getShop() -> Id);
		$specialOffersUrl = array('/lcatalog/offer','id' => $this -> getShop() -> Id);
		
		if(isset($_GET['catid'])){
			$importUrl['catid'] = $_GET['catid'];
			$shopUrl['catid'] = $_GET['catid'];
			$specialOffersUrl['catid'] = $_GET['catid'];
		}
		
		return array(
			array(
				'label'  => $this -> getShop() -> Name,
				'url'    => $shopUrl,
				'active' => $this -> Id == 'price' && $this -> action -> Id != 'import',
			),
			array(
				'label'  => Yii::t('Импорт товаров'),
				'url'    => $importUrl, 				
				'active' => $this -> Id == 'price' && $this -> action -> Id == 'import',
			),
			array(
				'label'  => Yii::t('Спецпредложения'),
				'url'    => $specialOffersUrl,
				'active' => $this  -> Id == 'offer',
			),
		);
	}
	
	/**
	 * shops page tabs
     * @return array
	 */
	private function getShopTabs(){
		return array(
			array(
				'label'  => Yii::t('Магазины'),
				'url'    => '/lcatalog/shop/admin',
				'active' => true,  
			),
		);
	}
	
	/**
	 * goods page tabs
     * @return array
	 */
	private function getGoodTabs(){
		$additionalParams = isset($this -> filterWidget) && $this -> filterWidget != false ? 
			$this -> filterWidget -> model -> getParamsSavedBetweenPages() : array();
		
		$goodUrl       = array('/lcatalog/main',            'catid' => $this->getCategory()->Id);
		$tableUrl      = array('/lcatalog/main/tableeditor','catid' => $this->getCategory()->Id);
		$propertyUrl   = array('/lcatalog/property/admin',  'catid' => $this->getCategory()->Id);
		$entitiesUrl   = array('/lcatalog/relation/admin',  'catid' => $this->getCategory()->Id);
		$editFilterUrl = array('/lcatalog/filterOption',    'catid' => $this->getCategory()->Id);
		
		$goodUrl       = array_merge($goodUrl,$additionalParams);
		$tableUrl      = array_merge($tableUrl,$additionalParams);
		
		return array(
			array(
				'label'  => Yii::t('Товары'),
				'url'    => $goodUrl,
				'active' => ( $this -> id == 'main' && $this -> action -> id == 'goodlist' )
				            || $this -> id == 'good',
			),
			array(
				'label'  => Yii::t('Табличный редактор'),
				'url'    => $tableUrl,
				'active' => $this -> id == 'main' &&  $this-> action -> id == 'tableeditor',
			),
			array(
				'label'   => Yii::t('Характеристики'),
				'url'     => $propertyUrl,
				'active'  => $this -> id == 'property',
				'visible' => Yii::app() -> getModule('user') -> isAdmin(),
			),
			array(
				'label'   => Yii::t('Сущности'),
				'url'     => $entitiesUrl,
				'active'  => $this -> id == 'relation',
				'visible' => Yii::app() -> getModule('user') -> isAdmin(),
			),
			array(
				'label'   => Yii::t('Фильтр'),
				'url'     => $editFilterUrl,
				'active'  => $this -> id == 'filterOption',
				'visible' => Yii::app() -> getModule('user') -> isAdmin(),
			),
		);		
	}

	/**
	 * default category
	 * @var int
	 */
	private $_defaultCategory = 1;
	
	/**
	 * temporary category model
	 * @var LCategory | NULL
	 */
	private $_category = NULL;
		
	/**
	 * получить модель текущей категории
	 * @return LCategory | false
	 */
	public function getCategory(){
		if($this -> _category !== NULL)
			return $this -> _category;
		
		if    ( isset($_GET['catid']) ) $catid = $_GET['catid'];
		elseif( isset($_POST['catid'])) $catid = $_POST['catid'];
		elseif( $this -> id == 'good' && NULL != $good = LGood::model() -> findByPk($_GET['id']) ) $catid = $good -> categoryId;
		else                            $catid = $this -> _defaultCategory;
		
		$category = LCategory::model() -> findByPk($catid);
		
		if(empty($category))
			return false;
			
		$this -> _category = $category;
		return $this -> _category;
	}	
	
}