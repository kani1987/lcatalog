<?php

class PriceController extends LCController{
	
    /**
     * @var LShop | NULL
     */
	private $_shop = NULL;
	
	/**
	 * (non-PHPdoc)
	 * @see lite/scripts/modules/lcatalog/components/LCController::filterCheckAccessControl()
	 */
	public function filterCheckAccessControl($chain)
	{
		if(Yii::app() -> user -> isGuest)
			Yii::app() -> user -> loginRequired();
					
		if(
			Yii::app() -> getModule('user') -> isAdmin()
			|| $this -> getShop() -> isAdmin(Yii::app() -> user -> Id)
		)
			return $chain -> run();
		throw new CHttpException(Yii::t('Не хватает прав'));
	}
	
    /**
     * init object of current shop
     * @throws CHttpException
     */
	public function getShop(){
		if(!empty($this -> _shop)) return $this -> _shop;
		
		$id = $_GET['id'];
		if(empty($id)) $id = $_POST['id'];
		
		$this -> _shop = LShop::model() -> findByPk($id);		
		if(empty($this -> _shop))
			throw new CHttpException('Нет такого магазина');
		
		return $this -> _shop;
	}
	
	/**
	 * get price object
	 * @param int $priceId
     * @return LPrices
	 */
	private function getPrice($priceId){
		$price = LPrices::model() -> with('Shop') -> findByPk($priceId);
		
		if(empty($price) || empty($price -> Shop))
			throw new CHttpException('Нет такой цены или магазина');
		
		if(!Yii::app() -> getModule('user') -> isAdmin() && !$price -> Shop -> isAdmin(Yii::app() -> user -> Id))
			throw new CHttpException('Не хватает прав на изменение цены');
					
		return $price;
	}
	
	/**
	 * shop prices
	 * @param int $id
	 */
	public function actionIndex($id){		
		$this -> render('prices',array(
			'dataProvider' => NULL,
		));
	}
	
	/**
	 * delete price
	 * @param int $priceid
	 */
	public function actionDelete($priceid){
		$price = $this -> getPrice($priceid);
		echo $price -> delete();
	}
	
	/**
	 * update price
	 * @param int $priceid
	 */
	public function actionUpdate($priceid){
		$price = $this -> getPrice($priceid);
		
		if(isset($_POST['value'])){
			$price -> Price = $_POST['value'];
			$price -> save();
			echo $price -> Price;
			return;
		}
		
		echo $this -> processOutput($this->renderPartial('update',array('price' => $price)));
	}
	
	/**
	 * update property price
	 */
	public function actionUpdateCustomProperty($goodid, $listvalueid){
		$good = Good::model() -> findByPk($goodid);
		if(empty($good))
			throw new CException('Товар не указан');
		
		$price = $good -> getPrlistPrice($this -> getShop() -> Id, $listvalueid, false);		
		
		if(empty($price)){
			$price = new LPrices;
			$price -> shopId    = $this -> getShop() -> Id;
			$price -> goodsId   = intval($goodid);
			$price -> prlistsId = intval($listvalueid);
		}
		
		if(isset($_POST['value'])){
			$price -> Price = $_POST['value'];
			$price -> save();
			echo $price -> Price;
			return;
		}
		
		echo $this -> processOutput($this->renderPartial('updatecustomproperty',array(
			'price' => $price,
			'goodId' => $goodid,
			'listvalueId' => $listvalueid,
		)));
	}
	
	/**
	 * import
	 */
	public function actionImport($id){
		$this -> render('import',array(
			'shop' => $this -> getShop(),
		));
	}
	
	
	/**
	 * add goods
	 * @param int $id
	 */
	public function actionCreate($id){
		$shop = $this -> getShop();	

		$goodIds = $_POST['checkboxes'];
		if(is_array($goodIds)) foreach($goodIds as $goodId){
			$price = LPrices::model() -> findByAttributes(array(
				'shopId'  => $shop -> Id,
				'goodsId' => $goodId,
				'prlistsId' => 0,
			));
		
			if(!empty($price)) continue;
			$price = new LPrices;
			$price -> shopId = $shop -> Id;
			$price -> goodsId = $goodId;
			$price -> prlistsId = 0;
			$price -> Price = 0;
			$price -> DateTime = date('Y-m-d H:i:s');
			$price -> save();
		}
		
		echo 1;
	}
	
}