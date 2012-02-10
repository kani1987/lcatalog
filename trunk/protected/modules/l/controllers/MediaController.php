<?php

class MediaController extends LCController{
	
	/**
	 * (non-PHPdoc)
	 * @see LCController::filterCheckAccessControl()
	 */
	public function filterCheckAccessControl($chain){
		if(Yii::app() -> user -> isGuest)
			Yii::app() -> user -> loginRequired();

		if(
			Yii::app() -> getModule('user') -> isAdmin()
			|| $this -> action -> Id == 'view'
			|| $this -> action -> Id == 'create'
			|| $this -> goodIsCreatedByUser()
		) return $chain -> run();
		
		throw new CHttpException(Yii::t('Не хватает прав'));		
	}
	
	/**
	 * media editor
	 */
	public function actionGood($id = NULL){
		if(empty($id)) $id = $_POST['id'];
		$good = Good::model()->with('Media')->findByPk($id);
		
		if(empty($good))
			throw new CHttpException("No good with id = #$goodId");
			
		echo $this->processOutput($this->renderPartial('mediaEditor',array(
			'good' => $good
		),true));
	}
	
	/**
	 * returns html good image preview
	 * @param int $goodId
     * @return string
	 */
	public function actionShowOneGoodMediaPreview($goodId=NULL){
		if(empty($goodId)) $goodId = $this -> getValue('goodid');
		
		$good = Good::model()->findByPk($goodId);
		$url = $good -> getMediaPreview('0','absolute');
		if($url !== NULL) return $url;
		else return 'images/design/private/camera.gif';
	}
	
	
	/**
	 * Edit mediafile name&description
	 */
	public function actionUpdate($id){
		if(isset($_POST['MediaFile']) && is_array($_POST['MediaFile'])){
			foreach($_POST['MediaFile'] as $mediaId => $mediaData){
				$mediaFile = MediaFile::model() -> findByPk($mediaId);
				if(empty($mediaFile)) continue;
				$mediaFile -> attributes = $mediaData;
				$mediaFile -> save();
			}
			echo 1;
			return;
		}
	}
	
	
	/**
	 * Load mediafile for good
	 */
	public function actionUpload($id){		
		if(false !== MediaFile::upload(array($id))){
			echo 1;			
		}else{
			echo 'Ошибка при загрузке медиафайла';
		}
	}
	
	/**
	 * Delete mediafile-good connection
	 */
	public function actionDelete($id){
		$mediaId = $_POST['mediaid'];
		
		$media = MediaFile::model()->findByPk($mediaId);
		if(empty($media)){
			echo Yii::t('Нет такого медиафайла');
			return;
		}
		
		if($media -> countLinks() === 1){
			$res = $media->destroyCompletely($id);
		}else{
			GoodsMedia::model()->deleteAllByAttributes(array('goodsId'=>$id,'mediafilesId'=>$mediaId));
			$res = true;
		}
		
		echo $res ? '1' : Yii::t('Ошибка при удалении медиафайла');
	}
	
}