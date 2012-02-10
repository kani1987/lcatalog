<?php

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('relation-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Управление связями с сущностями</h1>

<a href='/lcatalog/relation/create?catid=<?php echo $this->getCategory()->Id?>'>Добавить связь с сущностью</a>

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
	'id'=>'relation-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		array(
			'class'=>'CButtonColumn',
		),
		'Id',
		'EntityCategory.Name',
		'Alias',
		'Description',
	),
)); ?>
