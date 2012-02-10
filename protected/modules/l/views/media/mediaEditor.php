<?php

/**
 * Отображает редактор медиа
 * @var Good $good
 */

$css = <<<CSS
table.mediafile{
	padding: 10px;
}
table.mediafile td{
	padding: 5px;
}
CSS;
Yii::app() -> clientScript -> registerCss('grid_style',$css);

$js = <<<JS
$.cookie('goodcard_mode','media',{path: '/lcatalog'});

$('[id^="deletemediafile"]').bind('click', function() {
	if(!confirm('Удалить медиафайл?')) return;
	
	var idarr   = this.id.split('_');
	var goodid  = idarr[1];
	var mediaid = idarr[2];

	ajax2('/lcatalog/media/delete?id='+goodid,{
		mediaid: mediaid,
	}, function(res){
		if(res == 1)
			ajax2('/lcatalog/media/good?id='+ $good->Id,{},function(res){
				$('#popupbody').html(res);
			});
		else
			alert(res);
	});
	
	return false;
});

$('#upload_mediafile_form').ajaxForm({
	success: function(res){
		if(res == 1)
			ajax2('/lcatalog/media/good?id='+ $good->Id,{},function(res){
				$('#popupbody').html(res);
			});
		else
			alert(res);
	}
});

$('#edit_media_files').ajaxForm({
	success: function(res){
		if(res == 1) popup_close('goodwindow_{$good->Id}');
		else         alert(res);
	}
});

JS;
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery-ui-1.8.min.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.form.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.cookie.js');
Yii::app() -> clientScript -> registerScript('media_files_js',$js,CClientScript::POS_READY);
?>
<h1>Медиафайлы Товара #<?php echo $good->Id . ' ' . $good -> FullName?></h1>

<div style='padding: 10px'>
	<?php echo CHtml::beginForm("/lcatalog/media/upload?id=$good->Id",'post',array('enctype'=>'multipart/form-data','id'=>'upload_mediafile_form'))?>	
	<?php echo CHtml::fileField('mediafile')?>
	<?php echo CHtml::submitButton('Загрузить')?>
	<?php echo CHtml::endForm()?>	
</div>

<?php
echo CHtml::beginForm('/lcatalog/media/update?id=' . $good->Id,'post',array('id' => 'edit_media_files'));

echo '<table>';
$this -> widget('zii.widgets.CListView',array(
	'dataProvider' => $good->getMediaDataProvider(),
	'itemView'     => 'mediaEditorOneFile',
	'viewData'     => array(
		'goodId' => $good->Id,
	),
));
echo '</table>';

echo CHtml::submitButton('Сохранить');
echo CHtml::endForm();