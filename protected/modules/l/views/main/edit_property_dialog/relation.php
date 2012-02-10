<?php 

/**
 * диалог редактирования связи сущности и товара
 * @var Good     $good
 * @var Relation $relation
 */

$id    = "{$good->Id}_{$relation->Id}";

$js = <<<JS
$('#save_good_relation_value_{$good->Id}_{$relation->Id}').click(function(){
	var value = $('#value_$id').first().attr('value');

	ajax2('/lcatalog/main/editrelation',{
		relationid: $relation->Id,
		goodid: $good->Id,
		value: value 
	},function(res){
		popup_close();
		$('#relationcell_$id').html(res);
	});
});
JS;

Yii::app() -> clientScript -> registerScript("save_good_relation_value_$id",$js,CClientScript::POS_LOAD);

$saveButton = "<a id='save_good_relation_value_$id'>Сохранить!</a>"; 

echo
	  CHtml::form()
	. CHtml::hiddenField('goodid', $good->Id)
	. CHtml::hiddenField('relationid', $relation->Id)
	. '#' . $good -> Id . ' ' . $good -> Name . '<br>' . $relation->EntityCategory->Name . ' = '
	. CHtml::dropDownList('value',$good->getRelationValue($relation),$relation->EntityCategory->getGoodList(),array('id' => "value_$id"))
	. '<br>' . $saveButton
	. CHtml::endForm();