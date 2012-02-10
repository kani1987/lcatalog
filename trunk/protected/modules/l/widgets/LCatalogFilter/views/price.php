<?php 

/**
 * фрагмент фильтра, соотв. выбору по числовой характеристике
 * @var LPriceCondition $condition
 */

echo 
	"<b>{$condition->propertyName}</b> от "
	.CHtml::activeTextField($condition, "[$condition->propertyAlias]minPrice",array('style' => 'width: 50px'))
	.' до '
	.CHtml::activeTextField($condition, "[$condition->propertyAlias]maxPrice",array('style' => 'width: 50px'))
	;