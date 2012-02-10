$(document).ready(function(){
	
// ## работа с Корзиной
function reloadCart(id){
	  ajaxWP({
		  hpaction: "BasketButton",
	  },function(data){
		  $(document.getElementById(id)).html(data);
	  });	
	
	/*$.ajax({
		    "type": "POST",
		    "url": "/?r=page/ajax",
			"data":{
		      "action": "BasketButton"
		    },
		    "success":function(data){
		    	$(document.getElementById(id)).html(data);
		    }
	});	*/
}


$('.add_to_cart_button').click(function(){
  var optionsStr = '';
  if(goodproperties && goodproperties.length > 0){
	  for(var i in goodproperties){
		  if(parseInt(goodproperties[i]) != 0) optionsStr += goodproperties[i] + "_";
	  }
  }
  //alert(optionsStr);
  
  ajaxWP({
	  hpaction: "AddToCart",
	  goodid: this.getAttribute('goodid'),
	  goodproperties: optionsStr,
  },function(res){
	  alert(res);
  });

});

function reloadSum(){
	var sum = 0;
	$(".basket_good_price").each(function(){
		sum += parseInt($(this).text());		
	});

	$(document.getElementById("total_price")).text(sum);	
}

$(".goodsnumber").change(function(){
	var newnum = parseInt(this.value);
	if(newnum < 0) return;
	var obj = this.id.split('_');
	var goodid = obj[1];
	var key    = obj[2];

	var priceObj = document.getElementById("goodprice_"+goodid+"_"+key);
	$(priceObj).text(parseInt(priceObj.getAttribute('price')) * newnum);

	reloadSum();
});

$('.remove_from_cart_button').click(function(){
	var goodparam = this.getAttribute('goodid').split('_');
	var goodid    = goodparam[0];
	var optionnum = goodparam[1];
	  
	ajaxWP({
		  hpaction: "RemoveFromCart",
	      goodid: goodid,
	      optionnum: optionnum
	},function(data){
        alert(data);
    	
    	reloadCart('basket_container');
    	var goodNode = document.getElementById('basket_good_'+goodid+'_'+optionnum);
    	goodNode.parentNode.removeChild(goodNode.nextSibling);
    	goodNode.parentNode.removeChild(goodNode);
    	
    	var cont1 = document.getElementById('basket_container_1');
    	var cont2 = document.getElementById('basket_container_2');
    	
    	if(document.getElementsByClassName('good_in_cart').length == 0){
    		$(cont1).addClass('invisible');
    		$(cont2).removeClass('invisible');
    	}
    	
    	reloadSum();
	});  
});

$('#register_order').click(function(){
	var orderform = {};
	$('.orderinput').each(function(){
		var key = this.name;
		orderform[key] = this.value;
	});
	
	var arr = [];
	$(".goodsnumber").each(function(){
		var parObj = this.id.split('_');
		var par = parObj[1]+'_'+parObj[2];
		orderform['goods_'+par] = this.value;
	});
	
	var data = orderform;
	data.hpaction = "RegisterOrder";
	ajaxWP(data,function(res){
		  alert(res);
	});
});


// ## "раскрываемые" элементы страницы
$('.expandable').click(function(){
	if(this.nextSibling){
		var obj = $(this.nextSibling);
		var expandedClass  = this.getAttribute('expandedClass');
		var minimizedClass = this.getAttribute('minimizedClass');
		
		if(obj.hasClass('invisible')){
			if(minimizedClass) $(this).removeClass(minimizedClass);
			if(expandedClass)  $(this).addClass(expandedClass);
			obj.removeClass('invisible');
		}else{
			// alert('make invisible');
			if(minimizedClass) $(this).addClass(minimizedClass);
			if(expandedClass)  $(this).removeClass(expandedClass);
			obj.addClass('invisible');
		}
	}
}); 


});