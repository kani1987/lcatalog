<?php

Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.jjmenu.js');

if(!function_exists('display_categorytree_leaf')){	
	function display_categorytree_leaf($node){
		$route      = '/' . $controller -> route;
				
		return CHtml::link(
			$node -> Name,
			array($route,'id'=>$_GET['id'],'catid' => $node->Id),
			array(
				'class' => 'categorytree_leaf',
				'catid' => $node->Id,
			))
		;
	}
}

if(!function_exists('get_categorytree_children')){
	function get_categorytree_children($node){
		return $node -> getChildCategoriesByShop(Yii::app() -> controller -> getShop());
	}
}


$this -> widget('lcatalog.widgets.HtmlTree.HtmlTree',array(
	'name' => 'shopcategorytree',
	'root' => Category::model() -> findByPk(1),
	'displayLeafFunctionName' => 'display_categorytree_leaf',
	'getChildrenFunctionName' => 'get_categorytree_children',
));