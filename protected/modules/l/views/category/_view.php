<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Id), array('view', 'id'=>$data->Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Name')); ?>:</b>
	<?php echo CHtml::encode($data->Name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Alias')); ?>:</b>
	<?php echo CHtml::encode($data->Alias); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Description')); ?>:</b>
	<?php echo CHtml::encode($data->Description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('LLeaf')); ?>:</b>
	<?php echo CHtml::encode($data->LLeaf); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('RLeaf')); ?>:</b>
	<?php echo CHtml::encode($data->RLeaf); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Level')); ?>:</b>
	<?php echo CHtml::encode($data->Level); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('IsVisible')); ?>:</b>
	<?php echo CHtml::encode($data->IsVisible); ?>
	<br />

	*/ ?>

</div>