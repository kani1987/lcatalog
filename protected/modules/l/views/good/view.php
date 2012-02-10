<?php 

$js = <<<js

$.cookie('goodcard_mode','view',{path: '/lcatalog'});

js;
	
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery-ui-1.8.min.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.cookie.js');
Yii::app() -> clientScript -> registerScript('update_good_script',$js,CClientScript::POS_READY);
?>

<h1>Просмотр Товара #<?php echo $model->Id . ' ' . $model -> FullName; ?></h1>

	<?php if(is_array($model -> Media) && count($model -> Media) > 0):?>
		<?php foreach($model -> Media as $media):
			$url   = $media->getUrl('0','absolute');
			$url2  = $media->getUrl('1','absolute');
			$ext   = $media->Type->Extension;
				
			$mediaHtml = $ext == 'jpg' ?
				"<img style='float:left; padding: 10px; margin: 10px; border: 1px solid #eee' onclick='popitup(\"$url2\")' src='$url' alt='' />"
				: "<div style='float:left; padding: 10px; margin: 10px; border: 1px solid #eee'><a href='$url'>ссылка</a><br>тип: <b>$ext</b></div>";
			echo $mediaHtml; ?>
		<?php endforeach; ?>
		<div style='clear: both'></div>
	<?php endif;?>

<?php 
$columns = array_merge(array('Id','Name','Description'),$model -> getColumnNamesForView());
$this -> widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>$columns,
));

echo "<br><br><br><b>Предложения от магазинов:</b>";
$this -> widget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $model -> getShopsPrices(),
	'columns' => array(
		'Id',array(
			'name'  => 'магазин',
			'value' => 'CHtml::link($data->Shop->Name,$data->Shop->pageUrl)',
			'type'  => 'raw',
		),'Price',
	),
));