<?php 

/**
 * диалог редактирования значения характеристики типа "float"
 * @var Good     $good
 * @var Property $property
 */

$id    = "{$good->Id}_{$property->Id}";

$js = <<<JS
$('#save_good_property_value_{$good->Id}_{$property->Id}').click(function(){
	var value = $('#value_$id').first().attr('value');

	ajax2('/lcatalog/main/editproperty',{
		propertyid: $property->Id,
		goodid: $good->Id,
		value: value 
	},function(res){
		popup_close();
		$('#datacell_$id').html(res);
	});
});
JS;

Yii::app() -> clientScript -> registerScript("save_good_property_value_$id",$js,CClientScript::POS_LOAD);

$saveButton = "<a id='save_good_property_value_$id'>Сохранить!</a>"; 

echo
	  CHtml::form()
	. CHtml::hiddenField('goodid', $good->Id)
	. CHtml::hiddenField('propertyid', $property->Id)
	. '#' . $good -> Id . ' ' . $good -> Name . '<br>' . $property->Name . ' = '
	. CHtml::textField('value',$good->getPropertyValue($property),array('id' => "value_$id"))
	. '<br>' . $saveButton
	. CHtml::endForm();