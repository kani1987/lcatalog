<?php

/**
 * список товаров каталога
 * @var CActiveDataProvider $dataProvider
 */

$css = <<<CSS
div.goodlist div.good{
	float: left;
	width: 600px;
	margin: 10px;
	border: 1px solid grey;
	padding: 5px;
}

div.good div.good-attributes{
	float: left;
}

div.good div.good-in-list-image{
	margin: 10px;
	float: left;
}

div.goodlist div.noimage{
	margin-right: 10px;
	width: 50px;
	height: 50px;
	border: 1px solid black;
	float: left;
}
CSS;

Yii::app() -> clientScript -> registerCss('good-list-style',$css);

$goodListHtml = $this -> widget('zii.widgets.CListView',array(
	'dataProvider' => $dataProvider,
	'itemView'     => 'goods_oneinlist',
	'template'     => '{items}<div style="clear: both"></div>{pager}'
),true);

?><div class='goodlist'>
	<?php echo $goodListHtml?>	
</div>