<?php 

/**
 * Good card
 * @var Good $model
 */

$image = false === $firstMediaFile = $model->getFirstMediaImg(array('class' => 'good-in-list-image')) ?
	'<div class="noimage">No image</div>' : $firstMediaFile;

?>

<div>
	<?php echo $image?>
	<h2><?php echo $model->Name?></h2>
	
	<?php foreach($model -> Category -> properties as $property):
			if('пусто' == $value = $model -> getPropertyValue($property,true)) continue;?>
	<b><?php echo $property->Name?></b>: <?php echo $value?><br>
	<?php endforeach;?>

	<?php foreach($model -> Category -> relations as $relation):
			if('пусто' == $value = $model -> getRelationValue($relation,true)) continue;?>
	
	<b><?php echo $relation->Name?></b>: <?php echo $value?><br>
	<?php endforeach;?>

    <? echo CHtml::form() ?>
    <? echo CHtml::hiddenField('BasketGood[Id]', $model->Id)?>
    <? echo CHtml::submitButton('Добавить товар в корзину')?>
    <? echo CHtml::endForm() ?>
</div>