<?php 

/**
 * Страница категории (список привязанных серий)
 * @var Category $category
 * @var array of Good $serias
 * @var array of Good $producers
 */

?>
<table style='width: 100%; height: 100%'>
<tr>
	<td class='pbg' style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatll.png'); padding-left:7px;"></td>
	<td style="background-image: url('/wp-content/themes/topdoor/Images/design/lightbg.gif'); width:100%; height:100%">


<table cellspacing="0" cellpadding="0" width="100%" style="height:100%;">
<tr>
	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatlt.png'); padding-left:7px; height:7px; background-position:bottom;"></td>
	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincattt.png'); background-position:bottom;"></td>
	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatrt.png'); padding-left:3px; height:7px; background-position:bottom;"></td>
</tr>
<tr>
	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatll.png');padding-left:7px;"></td>
	<td align="left" width="100%" valign="top" id="catalog" style="background-image: url('/wp-content/themes/topdoor/Images/design/lightbg.gif'); width:100%; height:100%">
		<div class="rubyh15">
			<a href="/">Каталог</a> » <?php echo $category -> Name?>
		</div>
		<div class="proizvodlist">
    	    <span style="font-weight:bold;">Производитель:</span>
    	    
    	    <?php if($producer->Id == 'all'):?>
    	    <b>Все</b>
    	    <?php else:?>
    	    <a href="<?php echo Yii::app() -> controller -> createUrl('',array('catid'=>$category->Id,'pid'=>'all'))?>">Все</a>
        	<?php endif;?>
        	
        	
			<?php if(is_array($producers)): foreach($producers as $producerTemp):?>
        	<?php if($producerTemp->Id == $producer -> Id):?>
        	<nobr><b><?php echo $producerTemp->Name?></b></nobr>
        	<?php else:?>
        	<nobr><a href="<?php echo Yii::app() -> controller -> createUrl('',array('catid'=>$category->Id,'pid'=>$producerTemp->Id))?>"><?php echo $producerTemp->Name?></a></nobr>
        	<?php endif;?>
     	   <?php endforeach; endif;?>
   	 	</div>
		
		<table style='width: 100%'>	
		<tr><td colspan="2" style="padding-top:10px; padding-bottom:10px;"><div class="proizvodlist"><b>Cерии <?php echo $producer->Name?>:</b></div></td></tr>
		<tr>
             <td valign="top" style="padding-bottom:25px;">
             <?php $i=0; if(is_array($serias)): foreach($serias as $seria): $i++;?>
             <?php $seriaUrl = Yii::app() -> controller -> createUrl('',array('catid'=>$category->Id,'pid'=>$producer->Id,'sid'=>$seria->Id)) ?>
             <table cellspacing="0" cellpadding="0" width="50%" style='float: left'>
             <tr>
             	<td align="center" width="150" style="padding-bottom:5px;">
             		<div class="proizvodlist">
             			<a href="<?php echo $seriaUrl?>"><?php echo $seria->Name?></a>
             		</div>
             	</td>
             </tr>
             <tr>
             	<td align="center" width="100%" valign="top" style="padding:0 5px;">
             		<a href="<?php echo $seriaUrl?>"><img border="0" alt='' src="http://homeprice.ru/media/<?php if(NULL != $media = $seria->Media[0]) echo $media->createMediaUrl(1)?>"></a>
             	</td>
             </tr>
             </table>
             <?php if($i%2 == 0):?>
             	<div style='clear: both; padding: 20px'></div> 
             <?php endif;?>
             <?php endforeach; endif;?> 
             </td>
        </tr>
        <tr>
        	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatlb.png'); padding-left:7px; height:7px;"></td>
        	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatbb.png'); height:7px;"></td>
        	<td class="pbg" style="background-image: url('/wp-content/themes/topdoor/Images/design/maincatrb.png'); padding-left:3px; height:7px;"></td>
        </tr>
		</table>
	</td>
</tr>
</table>

	</td>
</tr>
</table>