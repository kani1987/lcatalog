<?php

/**
 * Catalog displaying widget
 * @author yurap
 */
class LCatalogDisplay extends CWidget{
	
	public $filter = false;
    
    public function init(){
        $this -> filter = $this -> createWidget('lcatalog.widgets.LCatalogFilter.LCatalogFilter');
    }
    
    public function run(){
        throw new CException('Wrong use of lcatalog display widget.');
    }
	
    /**
     * Карточка товара
     */
    public function actionGood($goodId){
        $good = Good::model() -> findByPk($goodId);
        if(empty($good)){
            throw new CHttpException(404,'Такого товара не существует');
        }
        
        $this -> render('good',array(
            'model' => $good,
        ));
    }    
	
	/**
	 * Список товаров
	 */
	public function actionGoods(){
		return $this -> render('goods',array(
			'dataProvider' => $this -> filter -> model -> getDataProvider(),
		));
	}
	
	/**
	 * Фильтр товаров
	 */
	public function actionFilter(){
		return $this -> render('filter',array(
			'filter' => $this -> filter,
		));
	}
    
    /**
     * Корзина
     */
    public function actionBasket(){
        $this -> render('basket', array(
            'basket' => Yii::app() -> getModule('lcatalog') -> basket,
        ));
    }
	
}