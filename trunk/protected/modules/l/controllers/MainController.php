<?php

class MainController extends LCController{
	
	public $defaultAction = 'goodlist';
	
	/**
	 * filter widget object
	 * @var LCatalogWidget
	 */
	public $filterWidget = false;
	
	/**
	 * (non-PHPdoc)
	 * @see LCController::filterCheckAccessControl()
	 */
	public function filterCheckAccessControl($chain)
	{
		if(Yii::app() -> user -> isGuest)
			Yii::app() -> user -> loginRequired();
		
		if(
			Yii::app() -> getModule('user') -> isAdmin()
			|| $this -> action -> Id == 'goodlist'
			|| $this -> action -> Id == 'tableeditor'
			|| $this -> action -> Id == 'goods'
			|| $this -> goodIsCreatedByUser()
		) return $chain -> run();
		
		throw new CHttpException(Yii::t('Не хватает прав'));
	}
	
	/**
	 * Is the current good created by current user
	 * @return bool
	 */
	private function goodIsCreatedByUser(){
		if ($this -> action -> Id != 'editproperty' && $this -> action -> Id != 'editrelation') return false;

		if(NULL !== $good = $this -> getGoodModel())
			return $good->isCreatedBy(Yii::app() -> user -> Id);
		
		return false;
	}
	
	/**
	 * good list
	 */
	public function actionGoodList(){
		$goodFilter = $this -> createWidget('lcatalog.widgets.LCatalogFilter.LCatalogFilter',array(
			'category' => $this -> getCategory(),
		));
		$goodFilter -> model -> addCondition(new LCategoryCondition($this -> getCategory() -> Id, false));
		$dataProvider = $goodFilter -> model -> getDataProvider(array('catid' => $this -> getCategory() -> Id));
		
		$this -> filterWidget = $goodFilter;
		
		$this -> render('goodList',array(
			'goodFilter'   => $goodFilter,
			'dataProvider' => $dataProvider,
		));
	}


	/**
	 * good list table editor
	 */
	public function actionTableEditor(){
		$goodFilter = $this -> createWidget('lcatalog.widgets.LCatalogFilter.LCatalogFilter',array(
			'category' => $this -> getCategory(),
		));
		$goodFilter -> model -> addCondition(new LCategoryCondition($this -> getCategory() -> Id, false));
		$dataProvider = $goodFilter -> model -> getDataProvider(array('catid' => $this -> getCategory() -> Id));
		
		$this -> filterWidget = $goodFilter;
		
		$this -> render('tableeditor',array(
			'goodFilter'   => $goodFilter,
			'dataProvider' => $dataProvider,
		));
	}

	private $_good = NULL;
	
	/**
	 * @return LGood
	 */
	private function getGoodModel(){
		if(!empty($this -> _good)) return $this -> _good;
		
		$goodId     = $_POST['goodid'];
		$this -> _good = LGood::model() -> findByPk($goodId);
		
		return $this -> _good;
	}
	
	/**
	 * ajax-form for editing good properties
	 */
	public function actionEditProperty(){
		$propertyId = $_POST['propertyid'];
		$value      = isset($_POST['value']) ? $_POST['value'] : false;

		$property = Property::model() -> findByPk($propertyId);
		$good     = $this -> getGoodModel();

		if(false !== $value){
			$good -> setPropertyValue($property, $value);
			echo $good->getPropertyValue($property, true);
			return;
		}

		if(empty($property) || empty($good))
		throw new CHttpException('Что-то пошло не так. То ли характеристики не та, то ли товар....');
			
		$template = 'edit_property_dialog/'.$property->Type;
			
		echo $this -> processOutput($this -> renderPartial($template,array(
			'property' => $property,
			'good'     => $good,
		),true));
	}


	/**
	 * ajax-form for editing good-entity relations
	 */
	public function actionEditRelation(){
		$relationId = $_POST['relationid'];
		$value      = isset($_POST['value']) ? $_POST['value'] : false;

		$relation = Relation::model() -> findByPk($relationId);
		$good     = $this -> getGoodModel();

		if(false !== $value){
			$good -> setRelationValue($relation, $value);
			echo $good->getRelationValue($relation, true);
			return;
		}

		if(empty($relation) || empty($good))
		  throw new CHttpException('Something goes wrong...');
			
		$template = 'edit_property_dialog/relation';
			
		echo $this -> processOutput($this -> renderPartial($template,array(
			'relation' => $relation,
			'good'     => $good,
		),true));
	}

}