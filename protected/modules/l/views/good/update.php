<?php 

$js = <<<js
$('#good-form [type=submit]').click(function(){
	$('#good-form').ajaxSubmit({
		success: function(res){
			if(res == '1'){
				popup_close('goodwindow_{$model->Id}');
				// uncomment here if need to open view good mode after submiting update good form
				/* ajax2('/lcatalog/good/view?id='+ $model->Id,{},function(res){
					$('#popupbody').html(res);
				});*/
			}else{
				alert(res);
			}
		}
	});		
	return false;		
});

$.cookie('goodcard_mode','update',{path: '/lcatalog'});

js;

Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery-ui-1.8.min.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.form.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.cookie.js');
Yii::app() -> clientScript -> registerScript('update_good_script',$js,CClientScript::POS_READY);

?>

<h1>Редактировать Товар #<?php echo $model->Id . ' ' . $model -> FullName; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>