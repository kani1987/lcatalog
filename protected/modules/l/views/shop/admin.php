<?php

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('shop-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<a href='/lcatalog/shop/create'>Добавить магазин</a>

<p>
Вы можете использовать операцию сравнения (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) перед каждым поисковым значением, таким образом, задавая условие поиска.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'shop-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'class'=>'CButtonColumn',
			'viewButtonUrl' => '"/lcatalog/price?id=" . $data->Id'
		),
		'Id',
		'Name',
		'Description',
		'displayType',
	),
)); ?>
