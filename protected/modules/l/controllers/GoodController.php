<?php

class GoodController extends LCController
{
	public $defaultAction = 'view';
	
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
	 * Is the current good created by user
	 * @return bool
	 */
	private function goodIsCreatedByUser(){
		if($this -> action -> Id != 'update' && $this -> action -> Id != 'delete') return false;
		
		if(NULL !== $good = LGood::model() -> findByPk($_GET['id']))
			return $good->isCreatedBy(Yii::app() -> user -> Id);
		
		return false;
	}
	
	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		echo $this->processOutput($this->renderPartial('view',array(
			'model'=>$this->loadModel($id),
		),true));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new LGood;
		
		$model -> categoryId = $this -> getCategory() -> Id;
		$model -> creatorId  = Yii::app() -> user -> Id;
		
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		if(isset($_POST['LGood']))
		{
			$model -> attributes=$_POST['LGood'];
			if($model->save()){
				echo 1;
				return;
				//$this->redirect(array('view','id'=>$model->Id));
			}
		}

		echo $this->processOutput($this->renderPartial('create',array(
			'model'=>$model,
		)),true);
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LGood']))
		{
			$model->setAttributes($_POST['LGood'],true);
			if($model->save()){
				echo 1;
				return;
				//$this->redirect(array('update','id'=>$model->Id));
			}			
		}

		echo $this->processOutput($this->renderPartial('update',array(
			'model'=>$model,
		),true));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('LGood');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Good('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['LGood']))
			$model->attributes=$_GET['LGood'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=Good::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='good-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
