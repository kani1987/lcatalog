<?php 
/**
 * форма редактирования цены товара
 * @var Prices $price
 */


$id    = "{$goodId}_{$listvalueId}";
$shopId = $this -> getShop() -> Id;

$js = <<<JS
$('#save_custom_price_{$id}').click(function(){
	var value = $('#value_$id').first().attr('value');

	ajax2('/lcatalog/price/updatecustomproperty?goodid='+$goodId+'&listvalueid=' + $listvalueId,{
		value: value,
		id: $shopId
	},function(res){
		popup_close();		
		$('#pricecustompropertycell_{$id}').html(res);
	});
});
JS;

Yii::app() -> clientScript -> registerScript("save_good_custom_property_value_$id",$js,CClientScript::POS_LOAD);

$saveButton = "<a id='save_custom_price_$id'>Сохранить!</a>"; 

echo CHtml::form()
     . 'Цена: '
     . CHtml::activeTextField($price, 'Price', array('id' => "value_$id"))
     . $saveButton
     . CHtml::endForm()
;