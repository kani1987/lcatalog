<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'property-form',
	'enableAjaxValidation'=>false,
)); 

$form->hiddenField($model,'categoryId');
?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<?php /*
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
	*/ ?>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>256)); ?></td>
		<td class="column"><?php echo $form->error($model,'Name'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Alias'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Alias',array('size'=>50,'maxlength'=>50)); ?></td>
		<td class="column"><?php echo $form->error($model,'Alias'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Type'); ?></td>
		<td class="column"><?php echo $form->dropDownList($model,'Type',$model->getPossibleTypes(),array('rows'=>6, 'cols'=>50)); ?></td>
		<td class="column"><?php echo $form->error($model,'Type'); ?></td>
	</tr>
	
	<?php if($model -> Type == 'list'):?>
	<tr class="row">
		<td>
			Возможные значения
			<br>
			<?php echo CHtml::link('Добавить',array('/lcatalog/prListValue/create','propertyid'=>$model->Id))?>
		</td>
		<td colspan='2'><?php
			$this -> widget('zii.widgets.grid.CGridView',array(
				'dataProvider' => $model -> getPossibleListValuesDataProvider(),
				'columns'      => array(
					array(
						'class' => 'CButtonColumn',
						'deleteButtonUrl' => '"/lcatalog/prListValue/delete?id={$data->Id}"',
						'updateButtonUrl' => '"/lcatalog/prListValue/update?id={$data->Id}"',
						'viewButtonUrl'   => '"/lcatalog/prListValue/view?id={$data->Id}"',
					),
					'Id','Name'
				),
			));
		?></td>
	</tr>
	<?php endif;?>
	
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'IsCustom'); ?></td>
		<td class="column"><?php echo $form->checkBox($model,'IsCustom'); ?></td>
		<td class="column"><?php echo $form->error($model,'IsCustom'); ?></td>
	</tr>
	

	<?php /* 
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'IsVisible'); ?></td>
		<td class="column"><?php echo $form->checkBox($model,'IsVisible'); ?></td>
		<td class="column"><?php echo $form->error($model,'IsVisible'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'IsMultivalued'); ?></td>
		<td class="column"><?php echo $form->checkBox($model,'IsMultivalued'); ?></td>
		<td class="column"><?php echo $form->error($model,'IsMultivalued'); ?></td>
	</tr>
	*/?>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->