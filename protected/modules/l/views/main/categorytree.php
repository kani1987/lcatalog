<?php

Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.jjmenu.js');

if(!function_exists('display_categorytree_leaf')){	
	function display_categorytree_leaf($node){
		$params = array('catid' => $node->Id);
		
		$controller = Yii::app()  -> controller;
		$route      = '/' . $controller -> route;
		
		if($controller->id == 'price')
			$params['id'] = $_GET['id'];
		elseif($controller->id == 'good')
			$route = '/lcatalog/main/goodlist';
		elseif($controller->id == 'filterOption' && $controller -> action -> Id == 'update')
			$route = '/lcatalog/filterOption';
		
		return CHtml::link(
			$node -> Name,
			array_merge(array($route),$params),			
			array(
				'class' => 'categorytree_leaf',
				'catid' => $node->Id,
			))
		;
	}
}

if(!function_exists('get_categorytree_children')){
	function get_categorytree_children($node){
		return $node -> getChildNodes();
	}
}

if(!function_exists('get_default_categorytree_leaves')){
	function get_default_categorytree_leaves($rootNode){
		$str = '_';
		$categories = $rootNode -> getChildNodes();
		$str .= $rootNode -> Id . '_';
		if(is_array($categories)) foreach($categories as $cat) $str .= $cat->Id . '_';
		return $str;
	}
}

$this -> widget('lcatalog.widgets.HtmlTree.HtmlTree',array(
	'name' => 'categorytree',
	'root' => Category::model() -> findByPk(1),
	'displayLeafFunctionName' => 'display_categorytree_leaf',
	'getChildrenFunctionName' => 'get_categorytree_children',
	'getDefaultLeavesFunctionName' => 'get_default_categorytree_leaves',
));

if(Yii::app() -> controller -> id != 'price' && Yii::app() -> getModule('user') -> isAdmin())
	$actionList = array(
		'add'      => '/lcatalog/category/create',
		'edit'     => '/lcatalog/category/update',
		'delete'   => '/lcatalog/category/delete',
		'moveup'   => '/lcatalog/category/moveup',
		'movedown' => '/lcatalog/category/movedown',
	);
else
	$actionList = array();

?>
<script type="text/javascript">
	$('.categorytree_leaf').each(function(){
		var catid   = this.getAttribute('catid');
		var catname = $(this).text();

		var menuitems = []; 
        <?php if(isset($actionList['add'])){?>
		menuitems.push({
			 title : 'Добавить',
			 action: {
			 	type: 'gourl',
			 	url:  '/lcatalog/category/create?parentid=' + catid
			 } 
		});
		<?php }?>
        <?php if(isset($actionList['edit'])){?>
		menuitems.push({
			 title : 'Редактировать',
			 action:{
			 	type: 'gourl',
			 	url:  '/lcatalog/category/update?id=' + catid
		 	 }
		});	
		<?php }?>
        <?php if(isset($actionList['delete'])){?>
		menuitems.push({
			 title : 'Удалить',
			 action: {
			 	type: 'fn',
			 	callback: function(){	 			
		 			$('#jjmenu_main').remove();
		 			if(!confirm('Действительно удалить страницу "'+ catname +'" вместе со всеми дочерними страницами?')) return;
		 			ajax2('<?php echo $actionList['delete'];?>?id=' + catid,{
		 				pageid: catid			 			
			 		}, function(res){
						if(res == 1) window.location.replace('/lcatalog/'); 
						else alert(res);
			 		});
		 		}
			 } 
		});		
		<?php }?>
        <?php if(isset($actionList['moveup'])){?>
		menuitems.push({
			 title : 'Вверх',
			 action: {
			 	type: 'fn',
			 	callback: function(){	 			
		 			$('#jjmenu_main').remove();
		 			ajax2('<?php echo $actionList['moveup'];?>',{
		 				pageid: catid			 			
			 		}, function(res){
						window.location.reload();
			 		});
		 		}
			 } 
		});		
		<?php }?>
        <?php if(isset($actionList['movedown'])){?>
		menuitems.push({
			 title : 'Вниз',
			 action: {
			 	type: 'fn',
			 	callback: function(){ 			
		 			$('#jjmenu_main').remove();
		 			ajax2('<?php echo $actionList['movedown'];?>',{
		 				pageid: catid			 			
			 		}, function(res){
						window.location.reload();
			 		});
		 		}
			 } 
		});		
		<?php }?>

		if(menuitems.length > 0) $(this).jjmenu('rightClick',menuitems,{},{yposition: "bottom"});
	});
</script>