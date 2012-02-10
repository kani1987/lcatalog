<?php 

echo CHtml::form()
	 . "Добавить администратора магазину $model->Name: "
	 . CHtml::hiddenField('ShopUser[shopId]', $model->Id)
	 . CHtml::dropDownList('ShopUser[userId]', NULL, $model->getPossibleAdminsList())
	 . CHtml::submitButton('Добавить')
	 . CHtml::endForm()
;