<?php 

/**
 * фрагмент фильтра, соотв. выбору по числовой характеристике
 * @var LListCondition $condition
 */

$values = $condition -> property -> getPossibleValues(false);
$className = !empty($condition -> values) ? 'open' : '';

echo "<div class='title'><b>$condition->propertyName</b></div>"
	 . "<div class='checkboxlist $className'>"
     . CHtml::activeCheckBoxList($condition, "[$condition->propertyAlias]values", $values)
     . '</div>'
     ;