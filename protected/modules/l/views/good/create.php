<?php 

$js = <<<js
$('#good-form [type=submit]').click(function(){
	$('#good-form').ajaxSubmit({
		success: function(res){
			if(res == '1'){
				popup_close();
			}else{
				alert(res);
			}
		}
	});		
	return false;
});

js;

Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.cookie.js');
Yii::app() -> clientScript -> registerScriptFile('/lite/js/jquery.form.js');
Yii::app() -> clientScript -> registerScript('create_good_script',$js,CClientScript::POS_READY);

?>

<h1>Добавить Товар</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>