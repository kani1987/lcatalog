<?php 

/**
 * карточка медиафайла
 * @var int $goodId
 * @var MediaFile $data
 */

$url   = $data->getUrl('0','absolute');
$url2  = $data->getUrl('1','absolute');
$ext   = $data->Type->Extension;

$mediaHtml = $ext == 'jpg' ?
			"<img onclick='popitup(\"$url2\")' src='$url' />"
			: "<a href='$url'>ссылка</a><br>тип: <b>$ext</b>";

?><tr>
	<td style='width: 100px'>
		<?php echo $mediaHtml?>
		<a href="#" id="deletemediafile_<?php echo $goodId?>_<?php echo $data->Id?>">удалить</a>
	</td>
	<td>

		<table class='mediafile'>
		<tr>
			<td><?php echo CHtml::activeLabelEx($data, 'Name', $htmlOptions)?></td>
			<td><?php echo CHtml::activeTextField($data, "[$data->Id]Name",array('style'=>'width: 250px'))?></td>
		</tr>
		<tr>
			<td><?php echo CHtml::activeLabelEx($data, 'Description', $htmlOptions)?></td>
			<td><?php echo CHtml::activeTextArea($data, "[$data->Id]Description",array('style'=>'width: 250px; height: 50px'))?></td>
		</tr>
		</table>

	</td>
</tr>