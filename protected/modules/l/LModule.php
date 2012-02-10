<?php

/**
 * Lite Catalog Module
 * @author yurap
 */
class LModule extends CWebModule{
	
	public $layout = 'layouts';
	public $defaultController = 'main';
	
	/**
	 * system path to mediafiles dir
	 * @var string
	 */
	public $mediaDir = false;
    
    /**
     * path of display widget
     * @var string
     */
    public $displayWidgetClassPath = 'l.widgets.LCatalogDisplay.LCatalogDisplay';
    
    /**
     * path of filter widget
     * @var string
     */
    public $filterWidgetClassPath = 'l.widgets.LCatalogFilter.LCatalogFilter';
    
    /**
     * @var LBasket
     */
    public $basket = false;
    
		
	/**
	 * checking mediadir availability
	 * @throws CException
	 */
	private function checkMediaDir(){
		if(false === $this -> mediaDir)
			throw new CException(Yii::t('Не указана директория для медиафайлов')); 
		
		if(!is_dir($this -> mediaDir) || !is_writable($this -> mediaDir))
			throw new CException(Yii::t('Не хватает прав на директорию для медиафайлов'));
	}
	
	/**
	 * checking user module availability
     * @throws CException
	 */
	private function checkUserModule(){
		$userModule = Yii::app() -> getModule('user');
		
		if(empty($userModule))
			throw new CException(Yii::t('Ошибка активации модуля User'));
	}
    
    /**
     * checking admin mail definition
     * @throws CException
     */
    private function checkAdminMail(){
        if(empty(Yii::app()->params['adminEmail']))
            throw new CException(Yii::t('Не указана почта администратора'));
    }
	
	public function init(){		
		$this -> checkMediaDir();
		$this -> checkUserModule();
        $this -> checkAdminMail();
        $this -> initBasket();
		
		Yii::import('lcatalog.components.*');
		Yii::import('lcatalog.models.eav.*');
		Yii::import('lcatalog.models.media.*');
		Yii::import('lcatalog.models.shop.*');
        Yii::import('lcatalog.models.basket.*');
	}
	
    /**
     * the display widget
     * @var LCatalogDisplay | false
     */
    private $_display = false;
    
    /**
     * @return LCatalogDisplay
     */
    public function getDisplay(){
        if(false !== $this -> _display) return $this -> _display;
        $this -> _display = Yii::app() -> controller -> createWidget($this -> displayWidgetClassPath);
        return $this -> _display;
    }
    
    /**
     * set filter options
     */
    public function setFilter($options = array()){
        $this -> display -> filter = Yii::app() -> controller -> createWidget($this -> filterWidgetClassPath, $options);
    }
    
    /**
     * @return Basket
     */
    public function initBasket(){
        if(false !== $this -> basket)
            $this -> basket = new LBasket;
    }
} 