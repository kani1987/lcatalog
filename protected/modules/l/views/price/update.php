<?php 
/**
 * форма редактирования цены товара
 * @var Prices $price
 */

$id    = "{$price->Id}";
$shopId = $this -> getShop() -> Id;

$js = <<<JS
$('#save_good_price_{$id}').click(function(){
	var value = $('#value_$id').first().attr('value');

	ajax2('/lcatalog/price/update?priceid='+$price->Id,{
		value: value,
		id: $shopId
	},function(res){
		popup_close();
		$('#pricecell_$id').html(res);
	});
});
JS;

Yii::app() -> clientScript -> registerScript("save_good_property_value_$id",$js,CClientScript::POS_LOAD);

$saveButton = "<a id='save_good_price_$id'>Сохранить!</a>"; 

echo CHtml::form()
     . 'Цена: '
     . CHtml::activeTextField($price, 'Price', array('id' => "value_$id"))
     . $saveButton
     . CHtml::endForm()
;