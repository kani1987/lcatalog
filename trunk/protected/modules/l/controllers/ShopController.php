<?php

class ShopController extends LCController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='/layouts/cms_without_tree';
    
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
     * модель текущего магазина
     * @var Shop
     */
    private $_shop;
    
    /**
     * Инициализировать объект текущего магазина
     * @throws CHttpException
     */
    public function getShop(){
        if(!empty($this -> _shop)) return $this -> _shop;
        
        $id = $_GET['id'];
        if(empty($id)) $id = $_POST['id'];
        
        $this -> _shop = Shop::model() -> findByPk($id);        
        if(empty($this -> _shop))
            throw new CHttpException(Yii::t('Нет такого магазина'));
        
        return $this -> _shop;
    }    
	
	/**
	 * действие добавляющее связку магазина и пользователя
	 * @param int $id
	 */
	public function actionCreateadmin($id){
		$model = $this->getShop();
		
		if(isset($_POST['LShopUser'])){			
			$model = new LShopUser;
			$model -> attributes = $_POST['LShopUser'];
			if($model -> save())
				$this -> redirect('/lcatalog/shop/update?id=' . $model->shopId);
		}
		
		$this -> render('createadmin',array(
			'model' => $model
		));
	}
	
	
	/**
	 * удалить все связи заданного магазина и пользователя
	 * @param int $uid
	 * @param int $id
	 */
	public function actionDeleteadmin($uid,$id){
		echo LShopUser::model() -> deleteAllByAttributes(array(
			'userId' => $uid,
			'shopId' => $id,
		));
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Shop;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['LShop']))
		{
			$model->attributes=$_POST['LShop'];
			if($model->save())
				$this->redirect(array('/lcatalog/shop'));
				//$this->redirect(array('view','id'=>$model->Id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
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

		if(isset($_POST['LShop']))
		{
			$model->attributes=$_POST['LShop'];
			if($model->save())
				$this->redirect(array('/lcatalog/shop'));
				//$this->redirect(array('view','id'=>$model->Id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
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
		return $this -> actionAdmin();
		
		$dataProvider=new CActiveDataProvider('Shop');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Shop('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Shop']))
			$model->attributes=$_GET['Shop'];

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
		$model=LShop::model()->findByPk((int)$id);
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
		if(isset($_POST['ajax']) && $_POST['ajax']==='shop-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
