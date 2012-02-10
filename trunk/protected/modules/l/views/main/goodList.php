<?php 

/**
 * простой список товаров
 * @var CWidget $goodFilter
 * @var CActiveDataProvider $dataProvider
 */

$css = <<<CSS
img.mediafile{
	height: 100px;
	cursor: pointer;
}
div.title{
	cursor: pointer;
}
CSS;
Yii::app() -> clientScript -> registerCss('grid_style',$css);

$js = <<<JS
$('#addgood_button').click(function(){
	ajax2(this.href,{},function(res){
		popup_open(res,600,'Добавить товар',undefined,'addgood');		
	});
	return false;
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
JS;
Yii::app() -> clientScript -> registerScript('update_button_popup',$js,CClientScript::POS_READY);

$goodTable  = $this -> createWidget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $dataProvider,
	'ajaxUpdate'   => false,
	'columns'      => array_merge(array(
		array(
			'class' => 'CButtonColumn',
			'template' => '{delete}',	
			'deleteButtonUrl' => '"/lcatalog/good/delete?id=$data->Id"',
		),
		'Id',
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
	)),
));

?>
<script type='text/javascript'>
function popitup(url) {
	newwindow=window.open(url,'name','height=300,width=300');
	if (window.focus) {
		newwindow.focus()}
		return false;
}
</script>
<?php $goodFilter->run();?>
<a href='/lcatalog/good/create?catid=<?php echo $this->getCategory()->Id?>' id='addgood_button'>Добавить товар</a>
<?php $goodTable->run();?>