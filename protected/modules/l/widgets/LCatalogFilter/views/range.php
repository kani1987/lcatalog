<?php 

/**
 * фрагмент фильтра, соотв. выбору по числовой характеристике
 * @var LRangeCondition $condition
 */

echo 
	"<b>{$condition->propertyName}</b> от "
	.CHtml::activeTextField($condition, "[$condition->propertyAlias]left",array('style' => 'width: 50px'))
	.' до '
	.CHtml::activeTextField($condition, "[$condition->propertyAlias]right",array('style' => 'width: 50px'))
	;