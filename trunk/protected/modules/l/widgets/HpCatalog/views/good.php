<?php
$res = '';

$Id          = $Good -> Id;
$catName     = $Good -> Category->Name;
$Name        = $Good -> Producer -> Name .' '.$Good -> Name;
$Description = '';
$price       = $Good -> getTotalPrice();

// ## Медиафайлы
$seria = $Good -> getBindedEntity(110222);
$color = $Good -> getBindedEntity(110221);

$seriaName = (!is_null($seria)) ? ' - '.$seria->Name : '';
$colorName = (!is_null($color)) ? ' - '.$color->Name : '';

$imgtitle = $catName.' '.$Name.$seriaName.$colorName.' - Doorsofia';

// ## задаем заголовок страницы таким же
$this->changeTitle($imgtitle);

$value = $shop->getValues();
$value = $value[0]['value'];

$userId = Yii::app()->user->getId();
$count = 0;

$mediafiles = $Good -> Media;
$mediaHtml = '';
foreach($mediafiles as $media){
	$mediaHtml[] = '<br><img alt="'.$imgtitle.'" title="'.$imgtitle.'" src=http://homeprice.ru/media/'.$media->createMediaUrl(1).'><br>';
}

// ## Описание товара
if($Good->Description != '') $Description.='<h3 class="bottomTitle">Описание</h3><p class="BottomText">'.$Good->Description.'</p>';
$addToCartButton = "<a href='#' class='add_to_cart_button' goodid='$Id'>Добавить в корзину</a>";

// ## Xарактеристики (настраеваемые и обычные)
$properties = $Good->Category->getAllProperties('ProductView');
$propertiesHtml = '<table class="selectBlock">';
if(is_array($properties)){
	foreach($properties as $property){
		$value = $Good->getPropertyValue($property);

		if($property->IsCustom == '1'){

			$propPrices = $Good -> getPropertyValuesPrices($property);
			if($propPrices == NULL){
				continue;
				$propertiesHtml .= 'настраеваемая, но цена не назначена';				
			}else{
				$propertiesHtml .= '<tr>';
				$propertiesHtml .= '<td><label>'.$property->Name.'</label></td>';
				$propertiesHtml .= '<td>';
				
				$propertiesHtml .= '<select  class="calculator_price">';
				$propertiesHtml .= '<option  value="'.$property->Id.'_0_0" selected>Выберите вариант</option>';
				foreach($propPrices as $propPrice){
					$propertiesHtml .= '<option value="'.$property->Id.'_'.$propPrice['prlist']->Id.'_'.$propPrice['Price'].'">'.$propPrice['prlist']->Name.'</option>';
				}
				$propertiesHtml .= '</select>';
			}

		}else{
			// ## не отображать, если значения нет
			if($value == '' || count($value)==0) continue;
				
			$propertiesHtml .= '<tr>';
			$propertiesHtml .= '<td><label>'.$property->Name.'</label></td>';
			$propertiesHtml .= '<td>';
				
			if(($value != NULL && $value != '') || count($value)>0){
				if(!is_array($value)){
					$propertiesHtml .= $value;
				}else{
					$i=0;
					$num = count($value);
					foreach($value as $listelem){
						$i++;
						$propertiesHtml .= $listelem;
						if($i<$num) $propertiesHtml.= ', ';
					}
				}
				$propertiesHtml .= '<br>';
			}
		}
		$propertiesHtml .= '</td>';
		$propertiesHtml .= '</tr>';
	}
}
$propertiesHtml .= '</table>';
$propertyPrices = $Good->getPrices();

// ## Шаблон карточки товара
$res .= <<<TEMPLATE
<h1 class="brown px24">$catName  $Name</h1>

<div class="card_picture">
$mediaHtml[0]
</div>
<div class="cardTovarDoor brown">
 <div class="orange px24 price">
 	<strong id='goodprice'>$price</strong> <strong id='goodprice'>р.</strong>
 </div>

 <span class="title"> Cтоимость набора:</span>
 $propertiesHtml
   
  <div class="addBasket">
  $addToCartButton
  </div>
</div>

<div style="clear:both"></div>
  
  $Description
  
</div>

$specialOffers

<div style="clear:both"></div>

<script type='text/javascript'>
var price = parseInt($price);
var goodproperties = [];

$('.calculator_price').change(function(){
	var priceOptions = this.value.split('_');
	
	var pr_price  = parseInt(this.getAttribute('price'));
	var new_price = parseInt(priceOptions[2]);
	
	this.setAttribute('price',new_price);
	
	if(pr_price  && pr_price  != 'undefined') price = price - pr_price;
	if(new_price && new_price != 'undefined') price = price + new_price;
	
	goodproperties[priceOptions[0]] = priceOptions[1];
	
	$("#goodprice").text(price);
});
</script>
TEMPLATE;


 echo $res;