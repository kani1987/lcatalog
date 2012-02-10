<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'good-form',
	'enableAjaxValidation'=>false,
));

$form->hiddenField($model,'categoryId');
?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Name',array('style' => 'width: 400px','maxlength'=>256)); ?></td>
		<td class="column"><?php echo $form->error($model,'Name'); ?></td>
	</tr>
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Description'); ?></td>
		<td class="column"><?php echo $form->textArea($model,'Description',array('style' => 'width:400px; height:200px')); ?></td>
		<td class="column"><?php echo $form->error($model,'Description'); ?></td>
	</tr>
	
	<?php $properties = $model -> Category -> getProperties();
	if(is_array($properties)) foreach($properties as $property):?>
		<tr class="row">
			<td class="column"><?php echo $property->Name; ?></td>
			<td class="column"><?php
			if($property->Type == 'bit')
				echo $form->checkBox($model,$model->getPropertyKey($property));
			elseif($property->Type == 'text')
				echo $form->textField($model,$model->getPropertyKey($property),array('style' => 'width:400px; height:200px'));
			elseif($property->Type == 'list')
				echo $form->dropDownList($model,$model->getPropertyKey($property),$property->getPossibleValues());
			elseif($property->Type == 'float')
				echo $form->textField($model,$model->getPropertyKey($property));
			?></td>
			<td class="column"><?php echo $form->error($model,$model->getPropertyKey($property)); ?></td>
		</tr>
	<?php endforeach;?>

	<?php $relations = $model -> Category -> getRelations();
	if(is_array($relations)) foreach($relations as $relation):?>
		<tr class="row">
			<td class="column"><?php echo $relation -> EntityCategory -> Name; ?></td>
			<td class="column"><?php echo $form->dropDownList($model,$model->getRelationKey($relation),$relation->EntityCategory->getGoodList()); ?></td>
			<td class="column"><?php echo $form->error($model,$model->getRelationKey($relation)); ?></td>
		</tr>
	<?php endforeach;?>
</table>

<div class="row buttons">
	<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->