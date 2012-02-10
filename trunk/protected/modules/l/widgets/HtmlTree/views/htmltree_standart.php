<?php 
/**
 * Шаблон для виджета CatalogTree
 * @var html $tree - само дерево  
 */
?>
<script type='text/javascript'>
// определить какая нужна иконка у первого узла
function initFirstNodePlusOrMinus(){
	var treeStatus = $.cookie('<?php echo $this->name; ?>');
	if(treeStatus == null)
		treeStatus = '<? echo $this -> getDefaultLeaves() ?>';

	if(!treeStatus.search('_1_'))
		$('#i1').attr('src','/lite/img/treeminus.gif');
	else
		$('#i1').attr('src','/lite/img/treeplus.gif');
}

// развернуть или свернуть узел дерева и запомнить в куке
function expandLeaf(img){
	ulid=img.id.replace("i","u");
	ul=document.getElementById(ulid);

	var treeStatus = $.cookie('<?php echo $this->name; ?>');
	if(treeStatus == null)
		treeStatus = '<? echo $this -> getDefaultLeaves() ?>';
	  
	var imgNumber = img.id.replace("i", "");
	treeStatus = treeStatus.replace("_" + imgNumber + "_", "_");	// Возможно, будет несколько номеров
	  
	if(ul.className=='visible'){
		img.src='/lite/img/treeplus.gif';
		ul.className='hidden';
	}else{
		img.src='/lite/img/treeminus.gif';
		ul.className='visible';

		treeStatus += imgNumber + "_";	// Добавление номера картинки
	}

	$.cookie('<?php echo $this->name; ?>', treeStatus, {path: '/lcatalog'});	// Сохранение куки
}

// cворачивание пунктов, номера которых есть в куке
$(document).ready(function(){
	initFirstNodePlusOrMinus();
	
	var treeStatus = $.cookie('<?php echo $this->name; ?>');	
	if(treeStatus == null)
		treeStatus = '<? echo $this -> getDefaultLeaves() ?>';
	
	var elements = treeStatus.split('_');
	for(var i = 0; i < elements.length; ++i)
	{
		$('#i' + elements[i]).each(function(){			
			expandLeaf(this);
		});
	}
});
</script>

<ul class='visible ULRoot' id='<?php  echo $this -> name; ?>'>
	<li>
		<img alt=' ' src='/lite/img/treeminus.gif' id='i1' onclick='expandLeaf(this)' />
		<?php echo $this -> displayLeaf($this -> root); echo $tree;?>
	</li>
</ul>