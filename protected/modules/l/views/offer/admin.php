<style type='text/css'>
.items{
	width: 100%;
}

.items td{
	text-align: center;
}
</style>


<?php 
echo CHtml::link('Добавить спецпредложение',array(
	'/lcatalog/offer/create', 'id' => $this->getShop() -> Id
));

$this -> widget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $offers,
	'enableSorting' => false,
	'template' => '{items}',
	'columns' => array(
		array(
			'class'    => 'CButtonColumn',
			'template' => '{update}{delete}',
			'updateButtonUrl' => 'Yii::app()->createUrl("/lcatalog/offer/update", array("id" => $data->shopId, "offerid" => $data->Id))',
			'deleteButtonUrl' => '"/lcatalog/offer/delete?id=$data->shopId&offerid=$data->Id"',
		),
		'IconFile:raw','Name','Start','Finish',		
	),
));

?>