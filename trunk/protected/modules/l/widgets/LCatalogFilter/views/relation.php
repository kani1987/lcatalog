<?php 

/**
 * фрагмент фильтра, соотв. выбору связанной сущности
 * @var LRelationCondition $condition
 */

$values = $condition -> property -> getPossibleValues(false);
$className = !empty($condition -> value) ? 'open' : '';


echo "<div class='title'><b>$condition->propertyName</b></div>"
	 . "<div class='checkboxlist $className'>"
     . CHtml::activeCheckBoxList($condition, "[$condition->propertyAlias]value", $values)
     . "</div>"
     ;