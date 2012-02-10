<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'category-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>
	
	<?php echo CHtml::hiddenField('parentid', $_GET['parentid'], $htmlOptions)?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?>
</td>
		<td class="column"><?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>256)); ?>
</td>
		<td class="column"><?php echo $form->error($model,'Name'); ?>
</td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Alias'); ?>
</td>
		<td class="column"><?php echo $form->textField($model,'Alias',array('size'=>50,'maxlength'=>50)); ?>
</td>
		<td class="column"><?php echo $form->error($model,'Alias'); ?>
</td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Description'); ?>
</td>
		<td class="column"><?php echo $form->textArea($model,'Description',array('rows'=>6, 'cols'=>50)); ?>
</td>
		<td class="column"><?php echo $form->error($model,'Description'); ?>
</td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'IsVisible'); ?>
</td>
		<td class="column"><?php echo $form->checkBox($model,'IsVisible'); ?>
</td>
		<td class="column"><?php echo $form->error($model,'IsVisible'); ?>
</td>
	</tr>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->