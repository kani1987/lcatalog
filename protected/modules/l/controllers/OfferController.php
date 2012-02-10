<?php

class OfferController extends LCController{
	
	public $defaultAction = 'offers';
	
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
		throw new CHttpException('Не хватает прав');
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
			throw new CHttpException(Yii::t('Нет такого магазина'));
		
		return $this -> _shop;
	}	
	
	/**
	 * special offer control
	 */
	public function actionOffers(){		
		$offers = LOffer::getDataProviderByShop($this -> getShop() -> Id);
		
		$this -> render('admin',array(
			'offers' => $offers,
		));
	}
	
	/**
	 * add offer
	 */
	public function actionCreate($id){
		$offer         = new LOffer;
		$offer->shopId = $this -> getShop() -> Id;
		
		if(isset($_POST['LOffer'])){
			$offer -> attributes = $_POST['LOffer'];
			
			if($offer -> save())
				$this -> redirect($this -> createUrl("/lcatalog/offer",array('id' => $this -> getShop() -> Id)));
		}
		
		$this -> render('addeditoffer',array(
			'model' => $offer,
		));
	}
	
	/**
	 * delete offer
	 */
	public function actionDelete($id,$offerid){
		$offer   = LOffer::model() -> findByAttributes(array(
			'shopId' => $this -> getShop() -> Id,
			'Id'     => intval($offerid),
		));

		if(empty($offer))
			throw new CException('Такого спецпредложения не существует');
			
		if($offer -> delete()){
			echo 1;
		}else{
			echo 'Ошибка при удалении';
		}
	}
	
	
	/**
	 * edit offer
	 */
	public function actionUpdate($id,$offerid){	
		$offer   = LOffer::model() -> findByPk($offerid);
		
		if(empty($offer)){
			echo 'Такого спецпредложения не существует';
			return;
		}
		
		if(isset($_POST['Offer'])){
			$offer -> attributes = $_POST['Offer'];
			if($offer -> save()){
				$this -> redirect("/lcatalog/offer?id=$offer->shopId");
			}
		}	
		
		$this -> render('addeditoffer',array(
			'model' => $offer,
		));
	}
	
	/**
     * update offer price
     */
	public function actionCheckPriceOffer(){
		$offerId = $_POST['offerid'];
		$priceId = $_POST['priceid'];
		$check   = $_POST['value'] === 'true';
		
		if(!$check){
			LOfferPrice::model() -> deleteAllByAttributes(array(
				'priceId' => $priceId,
				'offerId' => $offerId
			));
			
			echo 1;
			return;
		}
		
		$priceOffer = new LOfferPrice();
		$priceOffer -> priceId = $priceId;
		$priceOffer -> offerId = $offerId;
		$priceOffer -> Price = 0;
		echo $priceOffer -> save();
	}	
	
}