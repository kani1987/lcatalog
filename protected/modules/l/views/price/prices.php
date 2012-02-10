<?php

$css = <<<CSS
img.mediafile{
	height: 100px;
	cursor: pointer;
}
CSS;
Yii::app() -> clientScript -> registerCss('grid_style',$css);

$shopId = $this -> getShop() -> Id;

$js = <<<JS
$('[id^=pricecell]').click(function(){
	var top_offset = $(this).offset().top;
	var id         = this.id;
	var confArr    = id.split('_');
	
	var priceid    = confArr[1];	
		
	ajax2('/lcatalog/price/update?priceid=' + priceid,{
		id: $shopId
	},function(res){
		popup_open(res, 400, 'Редактировать цену', top_offset);		
	});
});

$('[id^=pricecustompropertycell]').click(function(){
	var top_offset = $(this).offset().top;
	var id         = this.id;
	var confArr    = id.split('_');
	
	var goodid      = confArr[1];
	var listvalueid	= confArr[2];
		
	ajax2('/lcatalog/price/updatecustomproperty?goodid=' + goodid + '&listvalueid=' + listvalueid,{
		id: $shopId,
	},function(res){
		popup_open(res, 400, 'Редактировать цену настраеваемой характеристики', top_offset);		
	});
});

$('[type=checkbox][id^=offer]').click(function(){
	var id         = this.id;
	var confArr    = id.split('_');
	var obj        = this;
	
	var priceid     = confArr[1];
	var offerid    = confArr[2];
	
	ajax2('/lcatalog/offer/checkpriceoffer',{
		id: $shopId,
		offerid: offerid,
		priceid: priceid,
		value: obj.checked
	},function(res){
		if(res != '1'){
			obj.checked = !obj.checked;
			alert(res);
		}
	});
});
JS;
Yii::app() -> clientScript -> registerScript('edit_pricecell',$js,CClientScript::POS_READY);

$js = <<<JS
$('#addgood_button').click(function(){
	ajax2(this.href,{},function(res){
		popup_open(res,600,'Добавить товар');		
	});
	return false;
});

$('.goodbutton, .mediafile').each(function(){	
	var idarr     = this.id.split('_');
	var goodid    = idarr[1];
	good_popup_window(this,goodid,'$this->rightsMode');

});

$('div.filter div.checkboxlist').each(function(){
	var obj = $(this);
	if(!obj.hasClass('open')) obj.toggle(400);
});
$('div.filter div.title').click(function(){
	$(this).next().toggle(400);
});
JS;
Yii::app() -> clientScript -> registerScript('update_button_popup',$js,CClientScript::POS_READY);


$goodFilter = $this -> createWidget('lcatalog.widgets.LCatalogFilter.LCatalogFilter',array(
	'template' =>
		"<div style='border: 1px solid grey; padding: 10px'>"
		. CHtml::beginForm()
		. "<div>{property|shop,price,displayGoodsWithZeroPrice=1}</div>"
		. "<div>На странице: {gpp1,20,50,100}</div>"
		. CHtml::submitButton('Поиск')
		. CHtml::endForm()
		. "</div>"
));
$goodFilter -> model -> addCondition(new LCategoryCondition($this -> getCategory() -> Id, false));

$goodTable = $this -> createWidget('zii.widgets.grid.CGridView',array(
	'dataProvider' => $goodFilter -> model -> getDataProvider(array('id' => $this -> getShop() -> Id, 'catid' => $this -> getCategory() -> Id)),
	'ajaxUpdate'   => false,
	'columns'      => array_merge(
		array(
			array(
				'class' => 'CButtonColumn',
				//'updateButtonUrl' => '"/lcatalog/good/view?id=$data->Id"',
				//'viewButtonUrl'   => '"/lcatalog/good/update?id=$data->Id"',
				'deleteButtonUrl' => '"/lcatalog/good/delete?id=$data->Id"',
				'template' => '{delete}',
			),
			array(
				'class'   => 'CCheckBoxColumn',
				'header'  => 'YML',
				'checked' => '$data -> Price -> YML == 1',
				'selectableRows' => 0,
			),
			array(
				'name'  => 'Товар',
				'value' => 'CHtml::link($data->FullName,$data->pageUrl,array("id" => "goodbutton_" . $data->Id, "class"=>"goodbutton"))',
				'type'  => 'raw',
			),
			/*array(
				'name'  => 'Медиа',
				'value' => 'Good::getImageTag($data->mediaPreview,array("id" => "media_".$data->Id, "class" => "mediafile"))',
				'type'  => 'raw',
			),*/
			array(
				'name'  => 'Цена',
				'value' => '"<div id=\'pricecustompropertycell_{$data->priceId}\'>" . Prices::model() -> findByPk($data->priceId)->Price . "</div>"',
				'type'  => 'raw',
			),
		),$goodFilter -> model -> getCustomPropertiesPricesColumns(),$goodFilter -> model -> getOffersColumns()),
	)
);

$goodFilter->run();
$goodTable->run();