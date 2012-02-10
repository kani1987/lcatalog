<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'shop-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Поля с <span class="required">*</span> обязательны для заполнения.</p>

	<?php echo $form->errorSummary($model); ?>

<table class="model_form_table">
	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Name'); ?></td>
		<td class="column"><?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>255)); ?></td>
		<td class="column"><?php echo $form->error($model,'Name'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Description'); ?></td>
		<td class="column"><?php echo $form->textArea($model,'Description',array('rows'=>6, 'cols'=>50)); ?></td>
		<td class="column"><?php echo $form->error($model,'Description'); ?></td>
	</tr>

	<tr class="row">
		<td class="column"><?php echo $form->labelEx($model,'Type'); ?></td>
		<td class="column"><?php echo $form->dropDownList($model,'Type',$model->getPossibleTypes()); ?></td>
		<td class="column"><?php echo $form->error($model,'Type'); ?></td>
	</tr>
	
	<tr class="row">
		<td class="column">
			Администраторы
			<br>
			<a href='/lcatalog/shop/createadmin?id=<?php echo $model->Id?>'>Добавить</a>
		</td>
		<td class="column"><?php $this->widget('zii.widgets.grid.CGridView',array(
			'dataProvider' => $model -> getAdmins(),
			'columns' => array(
				array(
					'class' => 'CButtonColumn',
					'template' => '{delete}',
					'deleteButtonUrl' => '"/lcatalog/shop/deleteadmin?uid={$data->id}&sid='. $model->Id .'"',
				),
				array(
					'name'  => 'Имя Фамилия',
					'value' => '$data->profile->firstname ." ".$data->profile->lastname ',
				),
			),
		)) ?></td>
		<td class="column"><?php echo $form->error($model,'Type'); ?></td>
	</tr>

</table>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Добавить' : 'Сохранить'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->