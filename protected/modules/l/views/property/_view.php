<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('Id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->Id), array('view', 'id'=>$data->Id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('categoryId')); ?>:</b>
	<?php echo CHtml::encode($data->categoryId); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Name')); ?>:</b>
	<?php echo CHtml::encode($data->Name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Alias')); ?>:</b>
	<?php echo CHtml::encode($data->Alias); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('Type')); ?>:</b>
	<?php echo CHtml::encode($data->Type); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('IsVisible')); ?>:</b>
	<?php echo CHtml::encode($data->IsVisible); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('IsMultivalued')); ?>:</b>
	<?php echo CHtml::encode($data->IsMultivalued); ?>
	<br />


</div>