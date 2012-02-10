<?php

/**
 * Виджет дерева
 * @author yura
 */
class HtmlTree extends CWidget{
	
	/**
	 * Модель корнего узла дерева
	 * @var CategoryTree
	 */
	public $root;
	
	/**
	 * Имя дерева
	 * @var string
	 */
	public $name;
	
	/**
	 * имя функции, отображающей лист дерева
	 * @var string
	 */
	public $displayLeafFunctionName;
	
	/**
	 * имя функции, возврающей детей узла дерева
	 * @var string
	 */
	public $getChildrenFunctionName;
	
	/**
	 * имя функции, возвращающей id узлов, которые должны быть свернуты по умолчанию в виде:
	 * _id1_id2_ или _
	 * @var string
	 */
	public $getDefaultLeavesFunctionName;
	
	public function init(){
		if(empty($this -> name))
			throw new CException('У дерева должно быть имя');
		
		if(!function_exists($this -> displayLeafFunctionName))
			throw new CException('Не определена функция, отображающая лист дерева');
			
		if(!function_exists($this -> getChildrenFunctionName))
			throw new CException('Не определена функция, возвращающая детей узла дерева');
	}
	
	
	/**
	 * функция, печатающая само дерево
	 * рекурсивно вызывает сама себя
	 */
	private function buildTreeRecursive($children,$id){
		if(empty($children)) return '';
		
		$result = '';		
		foreach($children as $child){
			$ownChildren = $this -> getChildren($child);
			$result .= '<li>';
			$result .= count($ownChildren) > 0 ?
					"<img alt=' ' src='/lite/img/treeplus.gif' id='i{$child->Id}' onclick='expandLeaf(this)' />" :
					"<img alt=' ' src='/lite/img/treeleaf.gif' />";
			
			$result .= $this -> displayLeaf($child)
					. $this -> buildTreeRecursive($ownChildren,$child->Id)
					. "</li>";	
		}
		
		return  "<ul class='hidden' id='u$id'>".$result."</ul>";
	}
	
	public function getDefaultLeaves(){
		$getDefaultLeaves = $this -> getDefaultLeavesFunctionName;
		if(empty($getDefaultLeaves)) return '_';
		return $getDefaultLeaves($this->root);
	}
	
	
	public function getChildren($node){
		$getChildren = $this -> getChildrenFunctionName;
		return $getChildren($node);
	}
	
	public function displayLeaf($node){
		$displayLeaf = $this -> displayLeafFunctionName;
		return $displayLeaf($node);
	}

	public function run(){
		Yii::app() -> clientScript -> registerCoreScript('jquery');
		$this -> render('htmltree_standart',array(
			'tree' => $this -> buildTreeRecursive($this -> getChildren($this->root),'1'),
		));
	}
	
}