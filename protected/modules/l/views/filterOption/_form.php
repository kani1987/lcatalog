<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'filter-option-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'categoryId'); ?></td>
		<td class="column"><?php
			$options = array();
			if (empty($model->categoryId))
				$options[$this->getCategory()->Id] = array('selected' => 'selected');
			echo $form->dropDownList($model,'categoryId',Category::getOrderedList(),array('options' => $options));		
		?></td>
		<td class="column"><?php echo $form->error($model,'categoryId'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Name',array('size'=>50,'maxlength'=>50)); ?></td>
		<td class="column"><?php echo $form->error($model,'Name'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Template'); ?></td>
		<td class="column"><?php echo $form->textArea($model,'Template',array('rows'=>15, 'cols'=>100)); ?></td>
		<td class="column"><?php echo $form->error($model,'Template'); ?></td>
	</tr>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->