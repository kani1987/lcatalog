<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pr-list-value-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'propertyId'); ?>
</td>
		<td class="column"><?php echo $form->textField($model,'propertyId');?>
</td>
		<td class="column"><?php echo $form->error($model,'propertyId'); ?>
</td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?>
</td>
		<td class="column"><?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>1000)); ?>
</td>
		<td class="column"><?php echo $form->error($model,'Name'); ?>
</td>
	</tr>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->