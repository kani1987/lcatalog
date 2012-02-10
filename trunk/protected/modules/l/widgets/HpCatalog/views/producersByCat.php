<?php 

/**
 * Список производителей по категории
 * @var Category $category
 * @var array of Good $producers
 */

?><table cellspacing="0" cellpadding="0" width="100%">
<tr>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatlt.png'); padding-left:7px; height:7px; background-position:bottom;"></td>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincattt.png'); background-position:bottom;"></td>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatrt.png'); padding-left:3px; height:7px; background-position:bottom;"></td>
</tr>
<tr>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatll.png');padding-left:7px;"></td>
	<td align="left" style="background-image: url('wp-content/themes/topdoor/Images/design/lightbg.gif'); width:100%; height:100%">
		<div style="padding:5px 4px 5px 10px;" class="rubyh15">
			<a style="text-decoration:none;" class="rubyh15" href="/?catid=22&amp;state=1"><?php echo $category->Name?></a>
		</div>
		<table cellspacing="0" cellpadding="0" width="100%" style="height:100%;">
            <tr>
            	<td width="84" valign="top">
                	<a href="/?catid=22&amp;state=1"><img height="123" border="0" width="76" style="margin-right:8px;" class="png" src="wp-content/themes/topdoor/Images/design/icon_mezhkomn.png"></a>
                </td>
                <td width="100%" valign="top">
                	<div class="vendorlist">
                         <?php if(is_array($producers)): foreach($producers as $producer):?>
                         <nobr><a href="<?php echo Yii::app()->controller->createUrl('',array('catid'=>$category->Id,'pid'=> $producer->Id))?>"><?php echo $producer->Name?></a></nobr>
                         <?php endforeach; endif;?>
	                </div>
	            </td>
            </tr>
        </table>
	</td>
</tr>
<tr>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatlb.png'); padding-left:7px; height:7px;"></td>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatbb.png'); height:7px;"></td>
	<td class="pbg" style="background-image: url('wp-content/themes/topdoor/Images/design/maincatrb.png'); padding-left:3px; height:7px;"></td>
</tr>
</table>