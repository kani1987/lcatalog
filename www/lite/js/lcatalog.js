/**
 * Привязывает к кнопке открытие попапа товара с тремя вкладками:
 * - просмотром товара
 * - редактированием товара
 * - управлением медиафайлами
 * @param object      DOM Object or jquery selector - кнопка, по нажатию на которую должен открываться попап
 * @param goodid      int                           - id товара
 * @param rightsLevel enum('full','truncated')      - если 'truncated' - отображается только вкладка просмотр товара  
 * @return undefined
 */
function good_popup_window(object,goodid,rightsLevel){
	if(rightsLevel != 'full' && rightsLevel != 'truncated'){
		alert('Ошибка: Неизвестный уровень прав');
		return;
	}
	var updateUrl = '/lcatalog/good/update?id=' + goodid;
	var viewUrl   = '/lcatalog/good/view?id='   + goodid;
	var mediaUrl  = '/lcatalog/media/good?id='  + goodid;
	var href      = object.href;
	var offset    = $(object).offset().top;
	
	$(object).click(function(){
		var mode = $.cookie('goodcard_mode');
		if(rightsLevel == 'truncated') mode = 'view';
		if(mode != null){
			if(mode == 'update')     href = updateUrl;
			else if(mode == 'view')  href = viewUrl;		
			else if(mode == 'media') href = mediaUrl;
		}
		
		ajax2(href,{},function(res){
			var text = '';
			if(rightsLevel == 'truncated')
				text = '<a id="viewgood_'+goodid+'" style="cursor: pointer">Просмотр</a>';
			else
				text =
				'<a id="updategood_'+goodid+'" style="cursor: pointer">Редактирование</a>'
				+' | <a id="viewgood_'+goodid+'" style="cursor: pointer">Просмотр</a>'
				+' | <a id="mediagood_'+goodid+'" style="cursor: pointer">Медиа</a>'
				;			
			popup_open(res,600,text,offset,'goodwindow_' + goodid);
			$('#updategood_'+goodid).click(function(){
				ajax2(updateUrl,{},function(res){
					$('#popupbodygoodwindow_'+goodid).html(res);
				});
			});
			$('#viewgood_'+goodid).click(function(){
				ajax2(viewUrl,{},function(res){
					$('#popupbodygoodwindow_'+goodid).html(res);
				});
			});
			$('#mediagood_'+goodid).click(function(){
				ajax2(mediaUrl,{},function(res){
					$('#popupbodygoodwindow_'+goodid).html(res);
				});
			});
		});
		
		return false;
	});	
}