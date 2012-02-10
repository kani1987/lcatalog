<?php 

/**
 * фрагмент фильтра, соотв. выбору по числовой характеристике
 * @var LCreatorCondition $condition
 */

echo 
	"<b>{$condition->propertyName}</b>"
	.CHtml::activeDropDownList($condition, "[$condition->propertyAlias]value",$condition->possibleValues(),array('style' => 'width: 300px'))
	;