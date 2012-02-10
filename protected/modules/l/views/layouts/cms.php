<?php 
/**
 * Шаблон страниц верхнего уровня админки
 * @param $hello        "Здравствуй, %username!"
 * @param $logo         логотип cms
 * @param $menu         навигация
 * @param $breadcrumbs  хлебные крошки
 * @param $tabs         табы
 * @param $content      содержательная часть текущей страницы
 * 
 * @param $leftColumnTitle     заголовок  левой колонки
 * @param $leftColumnContent   содержимое левой колонки
 * 
 * @param $footer       подвал
 */

Yii::app() -> clientScript -> registerCoreScript('jquery');

if($this -> id == 'price' && $this -> action -> Id != 'import')
	$leftColumnContent = $this -> renderPartial('/price/categorytree',array(),true);
else
	$leftColumnContent = $this -> renderPartial('/main/categorytree',array(),true);

$tabs = $this -> widget('lcatalog.components.CBoldMenuWidget',array(
	'items'            => $this -> getTabs(),
	//'linkLabelWrapper' => 'b',
	'activeCssClass'   => 'active',
	'activateItems'    => false,
),true);

/*$menu = $this -> widget('lcatalog.components.CBoldMenuWidget',array(
	'items'            => $this -> getUserNavigation(),
	'activeCssClass'   => 'active',
	'activateItems'    => false,
),true);*/

?><!DOCTYPE html PUBLIC '-//W3C//DTD HTML 4.01//EN' 'http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd'>
<html>
<head>
	<meta http-equiv='content-type' content='text/html; charset=utf-8'  >
	<title><?php echo $this->title?></title>
	<link rel='shortcut icon' href='/lite/favicon.gif' type='image/gif' >
	<link rel='stylesheet'    href='/lite/css/style.css' type='text/css' media='screen' >
	<link rel='stylesheet'    href='/lite/css/popup.css' type='text/css' media='screen' >	
	<script language='javascript' src='/lite/js/jquery.form.js'></script>
	<script language='javascript' src='/lite/js/jquery.cookie.js'></script>
	<script language='javascript' src='/lite/js/main.js'></script>
	<script language='javascript' src='/lite/js/lcatalog.js'></script>	
  <!--[if lt IE 7]>
  <![if gte IE 5.5]>
  <style type='text/css'> 
  /*для отображения пнг-картинок в IE*/
  .png
    {
      background:transparent;
      behavior: url('/lite/pngfix.htc');
    }
  </style>
  <![endif]>
  <![endif]-->

  
</head>

<body>

<div id='wrapper'>

	<div id='header'>
    	<div id='logout'>
      		<?php echo $hello;?>
    	</div><!-- #logout-->
    	<div id='logo'>
      		<?php echo $logo;?>
    	</div><!-- #logo-->
    	<div id='menu'>
    		<?php echo $menu;?>
    	</div>
    	<?php echo $breadcrumbs;?>
    </div><!-- #header-->

	<div id='middle'>
		<div id='container'>
			<div id='content' class='ct_normal'>
			<div class='tabs'>
				<?php echo $tabs;?>
			</div>
			<div id='editor' style='padding: 10px'>
				<div>Категория <b><?php echo $this->getCategory()->Name?></b></div>
 				<?php echo $content;?>
			</div><!--#editor-->
			</div><!-- #content-->
		</div><!-- #container-->
		
		<div id='sidebar' class='sb_normal'>
			<div class='shead'>
				<div>
					<h1><?php echo $leftColumnTitle;?></h1>
    			</div>
    		</div><!-- .shead-->
    		<div class='sbody'>
        		<div class='sswitch'>
        			<a id='sswitch' onclick='collapse_sidebar()'>&laquo;</a>
        		</div><!-- .sswitch-->
        		<?php echo $leftColumnContent;?>
        	</div><!-- .sbody-->
        </div><!-- #sidebar -->
	</div><!-- #middle-->

</div><!-- #wrapper -->

<div id='footer'>
	<div class='hr'></div>
	<div class='cr'><?php echo $footer;?></div>
</div>

<script type='text/javascript'>
function collapse_sidebar(){
	  sb=document.getElementById('sidebar');
	  sw=document.getElementById('sswitch');
	  ct=document.getElementById('content');
	  if(sb.className=='sb_normal'&&ct.className=='ct_normal'){
	    sw.innerHTML='&raquo;';
	    sb.className='sb_collapsed';
	    ct.className='ct_wrapped';
	    if(fr=document.getElementById('viewframe')){
	      fr.style.width='958';
	      fr.width='958';
	    }
	    
	    $.cookie('sidebarStatus', 'closed', {path: '/lcatalog'});	// Сохранение закрытого состояния боковой панели в куке
	  }else if(sb.className=='sb_collapsed'&&ct.className=='ct_wrapped'){
	    sw.innerHTML='&laquo;';
	    sb.className='sb_normal';
	    ct.className='ct_normal';
	    if(fr=document.getElementById('viewframe')){
	      fr.style.width='708';
	      fr.width='708';
	    }
	    
	    $.cookie('sidebarStatus', null, {path: '/lcatalog'});	// Сохранение открытого состояния боковой панели в куке
	  }else{
	    alert('Looks like you`ve played with DOM, so i can`t proceess your request((. Check out classes for #content and #sidebar');
	  } 
	}

$(document).ready(function(){
	// Проверка сохраненного состояния боковой панели
	var sidebarStatus = $.cookie('sidebarStatus');
	switch(sidebarStatus)
	{
		case null:
		break;

		case 'closed':
		collapse_sidebar();
		break;

		default:
		alert("Strange content of sidebarStatus cookie");
	}
});
</script>

</body>
</html>