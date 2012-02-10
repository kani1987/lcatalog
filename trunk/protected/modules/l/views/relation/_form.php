<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'relation-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'categoryId'); ?></td>
		<td class="column"><?php
			$options = array(); 
			if (empty($model->categoryId))
				$options[$this->getCategory()->Id] = array('selected' => 'selected');
			echo $form->dropDownList($model,'categoryId',Category::getOrderedList(),array('options' => $options));?></td>
		<td class="column"><?php echo $form->error($model,'categoryId'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'entityCategoryId'); ?></td>
		<td class="column"><?php echo $form->dropDownList($model,'entityCategoryId',Category::getOrderedEntities()); ?></td>
		<td class="column"><?php echo $form->error($model,'entityCategoryId'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Alias'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Alias',array('size'=>50,'maxlength'=>50)); ?></td>
		<td class="column"><?php echo $form->error($model,'Alias'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Description'); ?></td>
		<td class="column"><?php echo $form->textArea($model,'Description',array('cols'=>50,'rows'=>6)); ?></td>
		<td class="column"><?php echo $form->error($model,'Description'); ?></td>
	</tr>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->