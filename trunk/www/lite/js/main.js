/**
 * Улучшенная функция для работы с ajax
 * 
 * Пример вызова:
 * 	ajax2('PageTree','siteeditor',{
 * 		siteid: $siteId
 *  }, function(res){
 *      $('#cattree').remove();
 *      $('#pagetree_title').after(res);
 *  });
 * 
 * @param action  - имя действия
 * @param module  - имя контроллера
 * @param data    - данные
 * @param success - функция, вызываемая когда придет ответ
 * @param type    - (default) POST или GET
 * 
 */
function ajax2(route,data,success,form,type){
	if(!data) data = {};
	
	var parameters = {
		type : type? type: 'POST',
		url  : route,
		data : data,
		success : success,
		error : function() {
			alert('Не удалось обработать запрос');
		},
		cache : false
	};
	
	if(!form) $.ajax(parameters);
	else{
		$(form).ajaxSubmit(parameters);
	}
}


function countPopups(){
	return $('.popupcontainer').length;
}



function popup_open(html,width,title,top,id){
	// обычно top == $(this).offset().top
	var popupsNum = countPopups() + 1;
	width = parseInt(width);
	var marginleft = (width) / 2;
	
	if(id == undefined) id='';
	$("#popupcontainer"+id).remove();
	
	var position = 'left: 50%; margin-left: -' + marginleft + 'px;';
	if(top != undefined) position = position + 'top: ' + top + 'px';
	
	if(!title) title = '';	
	else       title = '<div class="popuptitle" id="popuptitle'+id+'">' + title + '</div>';
	
	if(!html) html = '';
	else html = '<div class="popupbody" id="popupbody'+id+'">'+html+'</div>';
	
	var closeButton = '<div onclick=\'popup_close("'+id+'");\' class="popupclose"></div>';
	
	popup_init_container();
	
	$('div.popupwindow').css('z-index',500);
	$('#popupscontainer').append(			
			 '<div class="popupwindow" id="popupwindow'+id+'" style="z-index: 501; width: '+width+'px; '+position+'">'
			   + title
			   + html
			   + closeButton
			 +'</div>'
	);
	
	$('#popupwindow'+id).draggable({ handle: '#popuptitle' + id });
	
	$('div.popuptitle').mousedown(function(){
		$('div.popupwindow').css('z-index',500);
		$(this).parent().css('z-index',501);
	});
}

function popup_init_container(){
	if($('#popupscontainer').length == 0)
		$(document.body).append('<div style="position: absolute; top: 100px"><div id="popupscontainer"></div></div>');
}

function popup_close(id){
	if(id == undefined) id='';
	var popupsNum = countPopups() - 1;
	$("#popupwindow"+id).remove();
}