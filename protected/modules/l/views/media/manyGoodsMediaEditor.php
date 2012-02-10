<?php

/**
 * Отображает форму загрузки медиафайлов,
 * привязваемых к многим товарам
 * 
 * @var array $goods массив моделей товаров
 */

if(empty($goods)){
	echo 'Не указан ни один товар';
	return;	
}

$table = '';

foreach($goods as $good){
	$producerName = $good -> Producer -> Name;
	$tr = "<tr><td>#$good->Id $producerName $good->Name<input type='hidden' name='goods[$good->Id]' value='$good->Id' /></td></tr>";
	$table .= $tr;
}
$table = "<table class='medias_good_list'>$table</table>";

$res = <<<TPL
<div title='Загрузка медиафайла для многих товаров' id='manymediauploader'>
<style type='text/css'>
.medias_good_list td{
	padding: 5px;
}
</style>

<center>
	<form target='uploader' id='uploadmanymediaform' method='POST' enctype='multipart/form-data' action='/'>
	$table
	<table>
	<tr>
		<td style='padding-right: 20px'>Загрузите файл: </td>
		<td>
			<input type='hidden' name='action' value='UploadManyGoodsMedia' />
			<input type='hidden' name='module' value='catalogeditor' />
			<input type="file" name='mediafile' id="manymediafile_upload" / goodid='$good->Id'>
		</td>
	</tr>
	</table>
	</form>
</center>
</div>
TPL;

echo $res;