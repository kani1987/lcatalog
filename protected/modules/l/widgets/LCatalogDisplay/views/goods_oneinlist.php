<?php 

/**
 * One good in good list
 * @var Good $data
 */

$image = false === $firstMediaFile = $data->getFirstMediaImg(array('class' => 'good-in-list-image')) ?
	'<div class="noimage">No image</div>' : $firstMediaFile;

?><div class='good'>
	<?php echo $image?>
	<div class='good-attributes'>
		<span class='good-title'><?php echo CHtml::link($data->Name,'?goodid=' . $data->Id)?></span><br>
		
		<?php foreach($data -> Category -> properties as $property):
				if('пусто' == $value = $data -> getPropertyValue($property,true)) continue;?>
		
		<b><?php echo $property->Name?></b>: <?php echo $value?><br>
		<?php endforeach;?>

		<?php foreach($data -> Category -> relations as $relation):
				if('пусто' == $value = $data -> getRelationValue($relation,true)) continue;?>
		
		<b><?php echo $relation->Name?></b>: <?php echo $value?><br>
		<?php endforeach;?>
	</div>
</div>