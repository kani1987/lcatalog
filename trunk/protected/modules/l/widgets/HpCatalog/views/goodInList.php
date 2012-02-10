<?php

/**
 * Товар в списке товаров
 * @var Good $data
 */

$Id          =  $data->Id ;
$Name        =  $data->Producer->Name .' '.$data->Name ;
$Description =  $data->Description ;
$IsVisible   =  $data->IsVisible ;
$catId       =  $data->categoriesId ;
$catName     =  $data->Category->Name;
$producerId  =  $data->producersId ;

$producerName = $data->Producer->Name;

// ## значения свойств товара
$properties = $data->Category->getAllProperties('PreviewView');
$propertiesHtml = '';
if(is_array($properties)){
	$propertiesHtml .= '<p class="MARGIN_DESC">';
	foreach($properties as $property){
		$value = $data->getPropertyValue($property);
		if(($value != NULL && $value != '') || count($value)>0){
			$propertiesHtml .= $property->Name . ' - ';
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
	$propertiesHtml .= '</p>';
}


$goodUrl = Yii::app() -> controller -> createUrl('/',array('goodid'=>$Id));
$goodUrl = "javascript: tovar('$goodUrl')";
$showGoodButton  = CHtml::link($Name,$goodUrl,array('class'=>"px18"));

foreach( $data->Media as $images ){
	$image[] = 'http://homeprice.ru/media/'.$images->createMediaUrl();
}

$price = $data -> getTotalPrice();

$seria = $data -> getBindedEntity(110222);
$color = $data -> getBindedEntity(110221);

$seriaName = (!is_null($seria)) ? ' - '.$seria->Name : '';
$colorName = (!is_null($color)) ? ' - '.$color->Name : '';

$imgtitle = $catName.' '.$Name.$seriaName.$colorName.' - Newdoor';

$res = '
<div class="catalog_block">
  <div class="catalog_pict" style="margin-top:0px; position: relative">
    <a href="'.$goodUrl.'"><img title="'.$imgtitle.'" alt="'.$imgtitle.'" src="'.$image[0].'" alt=""/></a>
    '.$icons.'
  </div>
  <div class="catalog_text brown" style="padding-left:40px; margin-top:0px;">
    '.$showGoodButton.'  
    '.$propertiesHtml.'
    <div class="cost" style="text-valign:bottom; padding-bottom:0px; margin-bottom:0px;">
      '.$price.' руб.
    </div>
  </div>
</div>';


echo $res;