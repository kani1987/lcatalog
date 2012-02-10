<h1>View FilterOption #<?php echo $model->Id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'Id',
		'categoryId',
		'Name',
		'Template',
	),
)); ?>
