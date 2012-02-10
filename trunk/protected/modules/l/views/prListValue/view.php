
<h1>Просмотр PrListValue #<?php echo $model->Id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'Id',
		'propertyId',
		'Name',
	),
)); ?>
