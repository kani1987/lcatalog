<?php 

Yii::import('zii.widgets.CMenu');

class CBoldMenuWidget extends CMenu{
	
	protected function renderMenuItem($item)
	{
		if(isset($item['url']))
		{
			$label=$this->linkLabelWrapper===null ? $item['label'] : '<'.$this->linkLabelWrapper.'>'.$item['label'].'</'.$this->linkLabelWrapper.'>';
			return '<b>' . CHtml::link($label,$item['url'],isset($item['linkOptions']) ? $item['linkOptions'] : array()) . '</b>';
		}
		else
			return CHtml::tag('span',isset($item['linkOptions']) ? $item['linkOptions'] : array(), $item['label']);
	}
}