<?php

$css = <<<CSS
img.mediafile{
	height: 100px;
	cursor: pointer;
}

CSS;
Yii::app() -> clientScript -> registerCss('grid_style',$css);

$js = <<<JS

$('#addgoodstoshop_button').click(function(){
	var checkboxes = [];
	$('[id^=goodprice]').each(function(){
		if(this.checked && !this.disabled){
			checkboxes.push(this.value);
			this.disabled = true;
		}
	});
	
	ajax2('/lcatalog/price/create?id=' + {$shop->Id},{
		checkboxes: checkboxes,
	},function(res){
		//alert(res);		
	});
});

$('.goodbutton, .mediafile').each(function(){	
	var idarr     = this.id.split('_');
	var goodid    = idarr[1];
	good_popup_window(this,goodid,'$this->rightsMode');

});

$('div.filter div.checkboxlist').each(function(){
	var obj = $(this);
	if(!obj.hasClass('open')) obj.toggle(400);
});
$('div.filter div.title').click(function(){
	$(this).next().toggle(400);
});

$('[checked=checked][id^=goodprice]').each(function(){
	this.disabled = true;
});

JS;
Yii::app() -> clientScript -> registerScript('import_prices',$js,CClientScript::POS_READY);

$goodFilter = $this -> createWidget('lcatalog.widgets.LCatalogFilter.LCatalogFilter',array(
	'category' => $this -> getCategory(),
));

$goodFilter -> model -> addCondition(new LCategoryCondition($this -> getCategory() -> Id, false));

$goodTable = $this -> createWidget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $goodFilter -> model -> getDataProvider(array(
		'catid' => $this -> getCategory() -> Id,
		'id'    => $this -> getShop() -> Id
	)),
	'ajaxUpdate'   => false,
	'columns'      => array_merge(
	array(
		array(
			'class'   => 'CCheckBoxColumn',
			'value'   => '$data -> Id',
			'checked' => '$data -> existsInShop(Yii::app() -> controller -> getShop() -> Id)',
			'selectableRows' => 2,
			'id' => 'goodprice',
		),
		array(
			'name'  => 'Товар',
			'value' => 'CHtml::link($data->FullName,$data->pageUrl,array("id" => "goodbutton_" . $data->Id, "class"=>"goodbutton"))',
			'type'  => 'raw',
		),
		array(
			'name'  => 'Медиа',
			'value' => 'Good::getImageTag($data->mediaPreview,array("id" => "media_".$data->Id, "class" => "mediafile"))',
			'type'  => 'raw',
		),
	), 
	$goodFilter -> model -> getColumns()),

));
?>

<?php $goodFilter->run();?>
<a id='addgoodstoshop_button'>Добавить все отмеченные галочками товары в магазин <?php echo $shop -> Name?></a>
<?php $goodTable->run();?>