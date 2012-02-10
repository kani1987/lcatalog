
<h1>Просмотр Категории #<?php echo $model->Id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'Id',
		'Name',
		'Alias',
		'Description',
		'LLeaf',
		'RLeaf',
		'Level',
		'IsVisible',
	),
)); ?>
