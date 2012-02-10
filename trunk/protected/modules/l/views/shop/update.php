
<h1>Редактировать Магазин <?php echo $model->Id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php

echo CHtml::beginForm('/lcatalog/shop/updateIndex?id=' . $model->Id)
     . CHtml::submitButton('Обновить индекс товаров')
     . CHtml::endForm()
;