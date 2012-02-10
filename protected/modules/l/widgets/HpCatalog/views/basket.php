<?php
$basket_one = 'invisible';
$basket_two = 'invisible';

$goodsHtml = '';

if(count($basket)>0){
	$basket_one = '';
	$sum = 0;
	foreach ($basket as $goodOptions){
		$good             = $goodOptions['model'];
		$showGoodButton   = CHtml::link($good->Producer->Name.' '.$good->Name,array('/','goodid'=>$good->Id),array('class'=>"px18"));
		
		foreach($goodOptions['options'] as $key => $options){
			$deleteGoodButton = '<div goodid="'.$good->Id.'_'.$key.'" class="remove_from_cart_button cross">удалить</div>';
			$propertiesHtml   = $showGoodButton.'<br>';
			$prlists = array();
			if(is_array($options)) foreach($options as $option){
				$propertiesHtml .= '<br>'.$option->Property->Name . ' - ' . $option -> Name;
				$prlists[] = $option -> Id;
			}
			
			$price = $good -> getTotalPrice($prlists);
			$sum += $price;

			$goodsHtml .= '<tr id="basket_good_'.$good->Id.'_'.$key.'" class="good_in_cart">
			<td>
				<table>
				<tr>
					<td style="vertical-align: top; padding: 5px 20px 11px 0px">
						<img src="http://homeprice.ru/media/'.$good->Media[0]->createMediaUrl().'" alt=""/>
					</td>
					<td style="vertical-align: top">
						'.$propertiesHtml.'
					</td>
				</tr>
				</table>
			</td>
			<td><input id="goodnumber_'.$good->Id.'_'.$key.'" class="goodsnumber" type="text" value="1"></td>
			<td>
				<div class="orange px24 price">
					<strong  class="basket_good_price" price="'.$price.'" id="goodprice_'.$good->Id.'_'.$key.'">'.$price.'</strong>
				</div>			
			</td>
			<td>
				'.$deleteGoodButton.'
			</td>
			</tr><tr><td colspan="4">
				<div class="hr"></div>
			</td></tr>';
		}
	}
	$goodsHtml .= '<tr><td style="text-align: right" colspan="4" class="px18">Всего: <span id="total_price">'.$sum.'</span> руб.</td></tr>';
}else{
	$basket_two = '';
}
?>
<div class='<?php echo $basket_one?>' id='basket_container_1'>
<div class="br"></div>
<h1>Ваша корзина</h1>
<div class="br"></div>
Изменить количество единиц товара вы можете поставив нужную цифру.<br />
Удалить товар или его составляющую из корзины нажав крестик.
<div class="br"></div>
<table id='basket_goods_list'>
<tr class="basket-page-titles">
   <td class="title">
		<span class="title">Наименование:</span>
   </td>
   <td class="numbers">
        <span class="title">Кол-во (шт)</span>
   </td>
   <td class="cost">
        <span class="title">Цена (руб)</span>
   </td>
   <td class="options"></td>
</tr>
<tr>
   <td colspan="4">
      <div class="hr"></div>
   </td>
</tr>
<?php echo $goodsHtml?>
<tr>
   <td colspan="4" class="title">
      <div style='height: 25px'></div>
      <?php echo $orderHtml?>
   </td>
</tr>
</table>
</div>



<div class='<?php echo $basket_two?>' id='basket_container_2'>
<h1>Ваша корзина пуста</h1>
</div>