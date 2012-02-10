<?php 

/**
 * Табличный редактор товаров
 * @var CWidget $goodFilter
 * @var CActiveDataProvider $dataProvider
 */

$css = <<<CSS
img.mediafile{
	height: 100px;
	cursor: pointer;
}

CSS;
Yii::app() -> clientScript -> registerCss('grid_style',$css);

$js = <<<JS
$('[id^=datacell]').click(function(){
	var top_offset = $(this).offset().top;
	var id         = this.id;
	var confArr    = id.split('_');
	
	var goodid     = confArr[1];
	var propertyid = confArr[2];
		
	ajax2('/lcatalog/main/editproperty',{
		goodid: goodid,
		propertyid: propertyid	
	},function(res){
		popup_open(res, 400, 'Редактировать значение свойства товара', top_offset);		
	});
});

$('[id^=relationcell]').click(function(){
	var top_offset = $(this).offset().top;
	var id         = this.id;
	var confArr    = id.split('_');
	
	var goodid     = confArr[1];
	var relationid = confArr[2];
		
	ajax2('/lcatalog/main/editrelation',{
		goodid: goodid,
		relationid: relationid	
	},function(res){
		popup_open(res, 400, 'Редактировать значение связи товара и сущности', top_offset);		
	});
});
JS;
Yii::app() -> clientScript -> registerScript('edit_datacell',$js,CClientScript::POS_READY);

$js = <<<JS
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
JS;
Yii::app() -> clientScript -> registerScript('update_button_popup',$js,CClientScript::POS_READY);

$goodTable  = $this -> createWidget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $dataProvider,
	'ajaxUpdate'   => false,
	'columns'      => array_merge(array(
		array(
			'class' => 'CButtonColumn',
			'updateButtonUrl' => '"/lcatalog/good/update?id=$data->Id"',
			'viewButtonUrl'   => '"/lcatalog/good/view?id=$data->Id"',
			'deleteButtonUrl' => '"/lcatalog/good/delete?id=$data->Id"',
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
		)
	), $goodFilter -> model -> getColumns()),

));

?>

<script type='text/javascript'>
function popitup(url) {
    newwindow=window.open(url,'name','height=300,width=300');
    if (window.focus) {newwindow.focus()}
    return false;
}
</script>
<?php $goodFilter->run();?>
<a href='/lcatalog/good/create?catid=<?php echo $this->getCategory()->Id?>'>Добавить товар</a>
<?php $goodTable->run();?>