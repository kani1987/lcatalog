<?php echo CHtml::beginForm('','post',array('enctype' => 'multipart/form-data'))?>
<?php /*echo CHtml::activeHiddenField($model, 'shopId')*/?>

<style type='text/css'>
.errorSummary{
	color: red;
}
table.normaltable td{
	padding: 5px;
}
</style>
<?php echo Chtml::errorSummary($model)?>

<table class='normaltable' style='padding-bottom: 10px'>
<tr>
	<td><?php echo CHtml::activeLabelEx($model, 'Name')?></td>
	<td><?php echo CHtml::activeTextField($model, 'Name')?></td>
</tr>
<tr>
	<td><?php echo CHtml::activeLabelEx($model, 'Description')?></td>
	<td><?php echo CHtml::activeTextArea($model, 'Description',array('cols' => 50, 'rows' => 6))?></td>
</tr>
<tr>
	<td><?php echo CHtml::activeLabelEx($model, 'FullDescription')?></td>
	<td><?php echo CHtml::activeTextArea($model, 'FullDescription',array('cols' => 50, 'rows' => 6))?></td>
</tr>
<tr>
	<td><?php echo CHtml::activeLabelEx($model, 'Start')?></td>
	<td><?php echo CHtml::activeTextField($model, 'Start',array('class' => 'date'))?></td>
</tr>
<tr>
	<td><?php echo CHtml::activeLabelEx($model, 'Finish')?></td>
	<td><?php echo CHtml::activeTextField($model, 'Finish',array('class' => 'date'))?></td>
</tr>
<tr>
	<td>Иконка</td>
	<td><?php echo CHtml::activeFileField($model, 'IconFile')?></td>
</tr>
</table>

<?php echo CHtml::submitButton($model -> isNewRecord ? 'Добавить' : 'Сохранить')?>
<?php echo CHtml::endForm()?>