<? if(count($basket->goods) == 0): ?>
<h1>Ваша корзина пуста.</h1>
<? else: ?>
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
<? foreach ($basket->goods as $good): ?>
<tr>
<td>
    <table>
    <tr>
        <td style="vertical-align: top; padding: 5px 20px 11px 0px">
            <? echo $good->Image ?>
        </td>
        <td style="vertical-align: top">
            <? echo CHtml::link($good->Name,array('/','goodid'=>$good->Id))?>
            <br>
            <? if(is_array($good->properties)) foreach($good->properties as $propertyName => $property): ?>
            <? echo $propertyName.': '.$property->Name ?>
            <? endforeach ?>
        </td>
    </tr>
    </table>
</td>
<td><input type="text" value="1"></td>
<td>
    <div class="orange px24 price">
        <? echo $good -> price ?>
    </div>          
</td>
<td>
    <div id='removegood_<?echo $good->Id?>'>удалить</div>
</td>
</tr><tr><td colspan="4">
    <div class="hr"></div>
</td></tr>
<? endforeach?>
<tr><td>Всего: <span id="total_price"><? echo $good->Price ?></span> руб.</td></tr>
<tr>
   <td colspan="4" class="title">
      <div style='height: 25px'></div>
      <?php echo $orderHtml?>
   </td>
</tr>
</table>

<? endif ?>