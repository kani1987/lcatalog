<?php

// ## отображает массив текста в виде таблицы; фактически - это рукописный виджет
if(!function_exists('displayTable')){
	function displayTable($arr,$colNum,$tableClass='',$tdStyle='vertical-align: top; text-align: justify; padding: 5px 5px 5px 5px'){
		$width = intval(100/intval($colNum));
		$i     = 0;
		$td    = '';
		$table = '';
		$num   = count($arr);
		$end   = $num % $colNum;
		foreach($arr as $elem){
			$td .= '<td style="width: '.$width.'%; '.$tdStyle.'">'.$elem.'</td>';
			$i++;
			if($i == $num){
				for($j=0;$j<$colNum - $end;$j++){
					//echo ':j:';
					$td .= '<td>&nbsp;</td>';
					$i++;
				}
			}
			if(($i % $colNum) == 0){
				$table .= '<tr>'.$td.'</tr>';
				$td = '';
			}
		}

		$table = '<table class="'.$tableClass.'" width=100%>'.$table.'</table>';
		return $table;
	}
}

$res = '';

//$defaultUrl = $this->createUrl('/',array('catid'=>$Category->Id));
$catPageId  = Yii::app()->params['catalogpageId'];
$catId      = $Category->Id;

$defaultUrl = "/?pageid=$catPageId&catid=$catId";
$searchUrl  = '';

// ## формируем отображение фильтра производителей
$producers  = $Category->getProducerList();
$producersHtml  = array();
$producerTableClass = 'invisible';

if(count($producers)>0){
	foreach($producers as $producer){
		$isChecked = '';
		if(is_array($_GET['producers']) && false !== array_search($producer->Id,$_GET['producers'])){
			$isChecked = 'checked="true"';
			$searchUrl .='&producers[]='.$producer->Id;
			$producerTableClass = '';
		}
		//var_dump(array_search($producer->Id,$_GET['producers']));
		$producersHtml[] .= '<input type="checkbox" class="filter_producer_checkbox" producerid="'.$producer->Id.'" linkid="filter_button" '.$isChecked.'><label>'.$producer -> Name.'</label>';
	}
}

// ## формируем отображение фильтра по характеристикам
$properties = $Category->getAllProperties('StandartSelect');
$propertiesHtml = array();
$tempHtml = '';

if(count($properties)>0){
	foreach($properties as $property){
		if(strtolower($property->Type) == 'list'){
			$propertyCheckBoxes = array();
			$tableClass = 'invisible';
			
			foreach($property->PrLists as $prlist){
				if(trim($prlist->Name) != ''){
					$isChecked = '';
					
					if(is_array($_GET['properties'])){
						if(array_key_exists($property->Id.'_'.$prlist->Id,$_GET['properties'])){
							$tableClass = '';
							$isChecked = 'checked="true"';
							$searchUrl.='&properties['.$property->Id.'_'.$prlist->Id.'][Id]='.$property->Id.'&properties['.$property->Id.'_'.$prlist->Id.'][Value]='.$prlist->Id;
						}
					}
					
					$propertyCheckBoxes[] = '<input type="checkbox" class="filter_property_checkbox"'
										.' propertyid="'.$property->Id.'" propertyvalue="'
										. $prlist->Id
										.'" propertytype="list" linkid="filter_button" '.$isChecked.'><label>'
									 	. $prlist->Name .'</label>';
				}
			}
			
			$propertyOpenClass = $tableClass=='invisible' ? 'close' : 'open';
			$tempHtml = '<span class="blackDashedUnderline expandable '.$propertyOpenClass.'" 
							expandedClass="open" minimizedClass="close"><span class="pointer"></span>' . $property->Name.'</span>';			
			$tempHtml .= displayTable($propertyCheckBoxes,3,$tableClass);
			$propertiesHtml[] = $tempHtml;
		}
	}
}

// ## формируем отображение фильтра по сущностям (связанным характеристикам)
$entities = $Category -> getNewdoorFilterEntities();
$entitiesHtml = array();
$tempHtml = '';
foreach ($entities as $entity){
	$tableClass = 'invisible';
	$binded = $Category -> getBindedEntities($entity->Id);
	$entitiesCheckBoxes = array();
	foreach($binded as $entityInstance){
					$isChecked = '';
					
					if(is_array($_GET['entities'])){
						if(false !== array_search($entityInstance->Id,$_GET['entities'])){
							$tableClass = '';
							$isChecked = 'checked="true"';
							$searchUrl.='&entities[]='.$entityInstance->Id;
						}
					}
					
					$entitiesCheckBoxes[] = '<input type="checkbox" class="filter_entity_checkbox" linkid="filter_button"'
					                      . ' entityid="'.$entityInstance->Id.'"'
					                      . ' '.$isChecked.'><label>'
									 	  . $entityInstance->Name .'</label>';
	}
	$entityOpenClass = $tableClass=='invisible' ? 'close' : 'open';
	$tempHtml = '<span class="blackDashedUnderline expandable '.$entityOpenClass.'" 
					expandedClass="open" minimizedClass="close"><span class="pointer"></span>' . $entity->Name.'</span>';
	$tempHtml .= displayTable($entitiesCheckBoxes,3,$tableClass);
	$entitiesHtml[] = $tempHtml;
}


//$button = CHtml::link('Подобрать!!',$searchParams,array('id'=>'filter_button'));
$filterButton = "<a id='filter_button' class='submit' href='".$defaultUrl.$searchUrl."#goods'>Подобрать</a>";

$producerOpenClass = $producerTableClass=='invisible' ? 'close' : 'open';
$filterHtml  = '<span class="blackDashedUnderline expandable '.$producerOpenClass.'" expandedClass="open" minimizedClass="close"><span class="pointer"></span>Производители:</span>';
$filterHtml .= displayTable($producersHtml,5,$producerTableClass);
$filterHtml .= displayTable($propertiesHtml,1,'','vertical-align: top');
$filterHtml .= displayTable($entitiesHtml,1,'','vertical-align: top');

$res = <<<TEMPLATE
<div class="options-border">
    <div class="options">
		$filterHtml
		$filterButton
		<div class='hr'></div>    
    </div>
</div>

<script type="text/javascript">
$('.filter_producer_checkbox').click(function(){
	var value      = this.checked;
	var producerId = this.getAttribute('producerid');
	var linkid     = this.getAttribute('linkid');

	var link = document.getElementById(linkid);
	if(link){
		var oldhref = link.getAttribute('href');

		var tailArr = oldhref.split('#');
		var tail = tailArr[1] ? tailArr[1] : '';
		oldhref  = tailArr[0];

		var newhref = '';
		if(value == true){
			newhref = oldhref + '&producers[]=' + producerId;
		}else{
			var temp    = oldhref.split('&');
			for(var i=0;i<temp.length;i++){
				var tempstr = temp[i].split('=');
				if(tempstr[1] && producerId == tempstr[1]){
					temp[i] = '';
				}
			}
			for(var i=0;i<temp.length;i++){
				if(temp[i]!='') newhref +=temp[i];
				if(temp[i+1] && temp[i+1]!='') newhref +='&';
			}
		}
		link.href = newhref + "#" + tail;
	}
});

$('.filter_entity_checkbox').click(function(){
	var value      = this.checked;
	var entityId = this.getAttribute('entityid');
	var linkid     = this.getAttribute('linkid');

	var link = document.getElementById(linkid);
	if(link){
		var oldhref = link.getAttribute('href');

		var tailArr = oldhref.split('#');
		var tail = tailArr[1] ? tailArr[1] : '';
		oldhref  = tailArr[0];

		var newhref = '';
		if(value == true){
			newhref = oldhref + '&entities[]=' + entityId;
		}else{
			var temp    = oldhref.split('&');
			for(var i=0;i<temp.length;i++){
				var tempstr = temp[i].split('=');
				if(tempstr[1] && entityId == tempstr[1]){
					temp[i] = '';
				}
			}
			for(var i=0;i<temp.length;i++){
				if(temp[i]!='') newhref +=temp[i];
				if(temp[i+1] && temp[i+1]!='') newhref +='&';
			}
		}
		link.href = newhref + "#" + tail;
	}
});

$('.filter_property_checkbox').click(function(){
	var value         = this.checked;
	var propertyid    = this.getAttribute('propertyid');
	var propertyvalue = this.getAttribute('propertyvalue');
	var propertytype  = this.getAttribute('propertytype');
	var linkid        = this.getAttribute('linkid');

	var link = document.getElementById(linkid);
	if(link){
		var oldhref = link.getAttribute('href');

		var tailArr = oldhref.split('#');
		var tail = tailArr[1] ? tailArr[1] : '';
		oldhref  = tailArr[0];


		var countelements = propertyid + '_' + propertyvalue;
		var newhref = '';
		if(value == true){
			newhref = oldhref + '&properties['+countelements+'][Id]=' + propertyid + '&properties['+countelements+'][Value]='+propertyvalue;
		}else{
			var temp    = oldhref.split('&');
			//alert(Dump(temp));

			for(var i=0;i<temp.length;i++){
				if(temp[i].match(/^properties\[.+\]\[Value\]/) != null){
					var tempstr = temp[i].split('=');

					if(tempstr[1] && propertyvalue == tempstr[1]){

						temp[i] = '';
						if(temp[i-1]) temp[i-1] = '';
					}
				}
			}

			for(var i=0;i<temp.length;i++){
				if(temp[i]!='') newhref +=temp[i];
				if(temp[i+1] && temp[i+1]!='') newhref +='&';
			}
		}
		link.href = newhref + "#" + tail;
	}
});
</script>
TEMPLATE;

echo $res;