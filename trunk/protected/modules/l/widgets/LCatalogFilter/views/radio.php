<?php 

/**
 * фрагмент фильтра, соотв. выбору по числовой характеристике
 * @var LListCondition $condition
 */

$values = $condition -> property -> getPossibleValues(true);

echo "<b>$condition->propertyName:</b><br>"
     . CHtml::activeRadioButtonList($condition, "[$condition->propertyAlias]value", $values)
     ;