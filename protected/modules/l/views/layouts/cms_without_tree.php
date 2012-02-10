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
$tabs = $this -> widget('lcatalog.components.CBoldMenuWidget',array(
	'items'            => $this -> getTabs(),
	//'linkLabelWrapper' => 'b',
	'activeCssClass'   => 'active',
	'activateItems'    => false,
),true);

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
      		Здравствуйте, <b>Администратор!</b>
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
			<div id='content' style='padding: 0 20px'>
			<div class='tabs'>
				<?php echo $tabs;?>
			</div>
			<div id='editor' style='padding: 10px'>
				<div>Категория <b><?php echo $this->getCategory()->Name?></b></div>
 				<?php echo $content;?>
			</div><!--#editor-->
			</div><!-- #content-->
		</div><!-- #container-->
		
	</div><!-- #middle-->
	
	

</div><!-- #wrapper -->

<div id='footer'>
	<div class='hr'></div>
	<div class='cr'><?php echo $footer;?></div>
</div>

<script type='text/javascript'>
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