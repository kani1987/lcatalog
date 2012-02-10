<?php

Yii::import('lcatalog.widgets.HpCatalog.models.*');

/**
 * Виджет каталога
 * @author katsuba, yura
 */
class HpCatalog extends CWidget{
	
	public $options = array();	
	
	/**
	 * Id магазина
	 * @var Id
	 */
	const SHOP_ID = 123891;
	
	/**
	 * Количество товаров на странице по умолчанию
	 * @var int
	 */
	public $defaultGoodsPerPage = 30;
	
	/**
	 * Категория по умолчанию
	 * @var int
	 */
	public $defaultCategoryId = 110105;
	
	
	/**
	 * имена GET-параметров, которые влияют на отображение каталога
	 * @var array
	 */
	public $paramName = array(
		'GoodsPerPage'  => 'gpp',
		'Producers'		=> 'producers',
		'Entities'		=> 'entities',
		'Properties'	=> 'properties',
		'GoodId'        => 'goodid',
		'Basket'        => 'basket',
		'CategoryId'    => 'catid',
	);
	
	/**
     * 
     * @var enum (full,filter,basket,ajax,cattree)
     */
    public $mode='full';
	
	/**
	 * Хлебные крошки каталога
	 * @var array('Name'=>'имя элемента', 'Url'=>'урл элемента')
	 */
	public $breadcrumbs=array();
	
		
	/**
	 * Получить значение параметра из GET или POST
	 */
	protected function getValue($param,$type='int'){
		return Yii::app()->controller->getValue($param,$type);	
	}
	
	/**
	 * Модель магазина
	 */
	public function getShop(){
		return shop::model() -> findByPk(HpCatalog::SHOP_ID);		
	}
	
	
	/**
	 * Добавить хлебную крошку
	 * @param string $name
	 * @param url    $url
	 */
	public function addBreadCrumb($name,$url=NULL){
		$arr['Name'] = $name;
		if(!empty($url)) $arr['Url'] = $url;
		
		$this->breadcrumbs[] = $arr;
	}
	
	/**
	 * Изменить заголовок страницы
	 * @param unknown_type $newtitle
	 */
	public function changeTitle($newtitle){
		Yii::app()->controller->changeTitle($newtitle);
	}
	
	/**
	 * Инициализация корзины
	 */
	private function initBasketGoods(){
		$session = Yii::app()->getSession();
		$session->open();

		$goodOptions = $session['goods'];

		$basket = array();
		if(is_array($goodOptions)){
			foreach($goodOptions as $goodId => $goodsModifications){
				$arr = array();
				$arr['model'] = Good::model()->findByPk($goodId);
				$arr['options'] = array();

				if(is_array($goodsModifications)) foreach($goodsModifications as $optionNum => $optionList){
					$optionArray = array();
					if(is_array($optionList)) foreach($optionList as $prlistId){
						$prlist = PrListElement::model()->with('Property')->findByPk($prlistId);
						if($prlist != NULL) $optionArray[]= $prlist;
					}
					$arr['options'][$optionNum]=$optionArray;
				}
				$basket[]=$arr;
			}
		}

		$session->close();
		return 	$basket;
	}
	
	/**
	 * Форма заказа	 
	 */
	public function actionOrder(){
		return $this->render('order',array(), true);
	}
	
	/**
	 * Отправка заказа
	 */
	public function actionRegisterOrder(){
		$basket = $this->initBasketGoods();
		$basketMessage = "Корзина:";
		$sum = 0;
		foreach($basket as $goodOptions){
			foreach($goodOptions['options'] as $key => $options){
				$basketMessage .= "\n\nТовар: ".$goodOptions['model']->Producer->Name.' '.$goodOptions['model']->Name;
				$prlists = array();
				foreach($options as $option){
					$prlists[] = $option->Id;
					$basketMessage .= "\n".$option->Property->Name. ' - ' .$option->Name;
				}
				$price = $goodOptions['model']->getTotalPrice($prlists);
				$number = intval($_POST['goods_'.$goodOptions['model']->Id.'_'.$key]);
				
				$price = $number * $price;
				$sum += $price;
				$basketMessage .= "\nКоличество: $number \nЦена: $price руб.";
			}
		}
		
		$City    = $_POST['City'];
		$Street  = $_POST['Street'];
		$House   = $_POST['House'];
		$Flat    = $_POST['Flat'];
		$Code    = $_POST['Code'];
		$Comment = $_POST['Comment'];
		$Surname = $_POST['Surname'];
		$Name    = $_POST['Name'];
		$SecName = $_POST['SecName'];
		$Phone   = $_POST['Phone'];
		$Mail    = $_POST['Mail'];

		$message = "Сумма заказа: $sum руб."
		."\nФамилия: $Surname"
		."\nИмя: $Name"
		."\nОтчество: $SecName"
		."\nТелефон: $Phone"
		."\nПочта: $Mail"
		
		."\n\nГород: $City"
		."\nУлица: $Street"
		."\nДом: $House"
		."\nКвартира: $Flat"
		."\nКод подъезда: $Code"
		."\nКомментарий: $Comment"		
		
		."\n\n".$basketMessage;
		
		$m = new Mail;		
		$m->Priority(1);
		
		$m->From('robot@' . Yii::app() -> request -> hostInfo);
		
		$m->To(Yii::app()->params['adminEmail']);
		$m->To("yurap@nibs-team.ru");
		
		$m->Subject('Запрос с сайта ' . Yii::app() -> request -> hostInfo);
		$m->Body($message);
		$mail = $m -> Send();
		
		
		if($mail) echo 'Запрос отправлен успешно';
		else echo 'Ошибка: не удалось отправить запрос';
	}
	
	
	/**
	 * Действие формирование кнопки "Корзина"
	 */
	public function actionBasketButton(){
		// ## Кнопка корзины
		$catPageId = '';//Yii::app()->params['catalogpageId'];

		$session = Yii::app()->getSession();
		$session->open();

		$savedGoods = $session['goods'];
		$goodsNum = 0;
		if(is_array($savedGoods)) foreach($savedGoods as $goodModifications){
			//foreach($goodModifications as $goods){
			$goodsNum += count($goodModifications);
			//}
		}

		$numHtml = ($goodsNum > 0)?'('.$goodsNum.')</b>':'';

		$session->close();
		$basketButton = "<a href='/?pageid=$catPageId&basket'>Корзина$numHtml</a>";

		return $basketButton;
	}
	
	/**
	 * Действие добавления товара в корзину.
	 * Товары хранятся в сессиях в таком виде:
	 * $session['goods'][$goodId] = array(
	 * 		'0' => array($prlistsId1, $prlistsId2, ...),
	 * 		'1' => array($prlistsId1, $prlistsId2, ...),
	 * 		...
	 * @return unknown_type
	 */
	public function actionAddToCart(){
		// ## определяем Id товара и ищем соотв. модель
		$goodId  = isset($_POST['goodid']) ? intval($_POST['goodid']) : NULL;
		if($goodId == NULL)
		return 'Ошибка: не указан товар';
			
		$good = Good::model()->findByPk($goodId);
		if($good == NULL)
		return 'Ошибка: товар не существует';

		// ## набор характеристик, с которыми был выбран этот товар
		$properties = array();
		$propStr = isset($_POST['goodproperties']) ? $_POST['goodproperties'] : '';

		if( $propStr != '' ){
			$properties = explode('_',$propStr);

			// ## если последний элемент пустой (а обычно это так) - спопнуть его
			if($properties[count($properties)-1] == '') array_pop($properties);
		}

		// ## в какой комплектации нам передали товар?
		//return print_r($properties,true);

		// ## (не)сохранение товара в сессиях
		$res = '';
		$session = Yii::app()->getSession();
		$session->open();

		$savedGoods   = ($session['goods'] === NULL) ? array() : $session['goods'];

		// ## проверить, был ли товар в такой комлектации уже сохранен
		$add = true;
		if(count($savedGoods[$goodId]) > 0){
			foreach($savedGoods[$goodId] as $arr){
				if(( count(array_diff($arr,$properties)) == 0 && count($arr)>0 && count($properties)==count($arr) )
				||( count($arr)==0 && count($properties)==0 )
				){
					$add = false;
					break;
				}
			}
		}

		// ## непосредственно сохранение
		$goodName = $good->Producer->Name.' '.$good->Name;
		if($add === true){
			$savedGoods[$goodId][] = $properties;
			$session['goods'] = $savedGoods;
			$res = "Товар $goodName добавлен";
		}else{
			$res = "Товар $goodName уже добавлен. Чтобы изменить количество, перейдите в корзину";
		}

		$session->close();
		echo $res;
	}
	
	/**
	 * Удалить товар в некоторой комплектации из корзины
	 * @return unknown_type
	 */
	public function actionRemoveFromCart(){
		$goodId    = isset($_POST['goodid'])    ? intval($_POST['goodid'])    : NULL;
		$optionNum = isset($_POST['optionnum']) ? intval($_POST['optionnum']) : NULL;

		if($goodId == NULL)
		return 'Ошибка: не указан товар';

		$session = Yii::app()->getSession();
		$session->open();


		$goods = $session['goods'];

		//return $optionNum.'::'.print_r($goods[$goodId],true);

		if(isset($goods[$goodId][$optionNum])) unset($goods[$goodId][$optionNum]);
		if(count($goods[$goodId]) == 0)        unset($goods[$goodId]);

		$session['goods'] = $goods;
		$session->close();
		return 'Товар убран из Корзины';
	}
	
	
	/**
	 * Страница корзины
	 */
	public function actionBasket(){
		$this->addBreadCrumb('Корзина');
		
		$orderHtml = $this -> actionOrder();

		$session = Yii::app()->getSession();
		
		$basket = $this->initBasketGoods();
		return $this->render('basket',array(
			'basket'=>$basket,
			'orderHtml'=>$orderHtml,
		),true);
	}
	
	/**
	 * Форма фильтра товаров
	 */
	public function actionFilter($mode=NULL){
		$catId = $this->getValue('catid');
		if(empty($catId)) $catId = $this -> defaultCategoryId;
		
		$category = Category::model()->findByPk($catId);
		if($category === NULL) return 'Нет категории - нет фильтра!! (Укажите категорию)';

		
		if($mode!='default')$template = $this -> model -> Template;
		$template = !empty($template) ? $template : 'filter';
		
		return $this->render($template,array(
			'Category'=>$category,
		),true);
	}
	
	/**
	 * список категорий, в которых есть цены у данного магазина
	 */
	public function actionShopCategories(){
		$rootCat = Category::model() -> findByPk(12);
		$categories = $rootCat -> getChildCategoriesByShopId(HpCatalog::SHOP_ID);
		
		echo count($categories);
	}
	
	
	/**
	 * Текущая категория
	 * @var int
	 */
	public $categoryId = false;
	
	/**
	 * отобразить список производителей категории
	 */
	public function actionProducersByCat()
	{
		$categoryId = $this -> categoryId;
		$category   = Category::model() -> findByPk($categoryId);
		if(empty($category)) return 'У списка производителей не указана категория';
		
				
		return $this -> render('producersByCat',array(
			'producers' => $category -> getProducerList(),
			'category'  => $category			
		),true);
	}
	
	/*
	public function actionSerias($Page,$fatherPage)
	{
		$producerName = $Page->getData( 'ProducerName' );
		$description  = $Page->getData( 'ProducerName', 1 );
		$producer     = Good::model()->findByAttributes(array('Name'=>$producerName));

		// ## Получаем серии
		//$serias = Relation::getByEntity($producer->Id,110222);
		$serias = Good::model()->findAllByAttributes(array('producersId'=>$producer -> Id, 'categoriesId'=>110222));

		return $this->renderPartial('serias',array(
			'serias'       => $serias,
			'description'  => $description,
		 	'producer'     => $producer,
		),true);
	}
	
	public function actionSerias_full($Page,$fatherPage)
	{
		$producerName = $Page->getData( 'ProducerName' );
		$description  = $Page->getData( 'ProducerName', 1 );
		$producer     = Good::model()->findByAttributes(array('Name'=>$producerName));

		// ## Получаем серии
		//$serias = Relation::getByEntity($producer->Id,110222);
		$serias = Good::model()->findAllByAttributes(array('producersId'=>$producer -> Id, 'categoriesId'=>110222));

		return $this->renderPartial('serias_full',array(
			'serias'       => $serias,
			'description'  => $description,
		 	'producer'     => $producer,
		),true);
	}*/
	
	
	/**
	 * Отфильтрованный список товаров
	 */
	public function actionGoodList(){
		$category = Category::model() -> findByPk($_GET['catid']);
		
		if($_GET['pid'] == 'all'){
			$producer = new Good;
			$producer -> Id = 'all';
			$producer -> Name = 'Все';
		}else
			$producer = Good::model() -> findByPk($_GET['pid']);
		
		if(empty($category))
			return 'Нет такой категории';
			
		$serias = Good::getSerias($_GET['pid'],HpCatalog::SHOP_ID);
		$seria  = Good::model() -> findByPk($_GET['sid']);
		
		if(empty($seria)){
			$seria = new Good;
			$seria -> Name = 'Все';
			$seria -> Id = 'all';
			$dataProvider = Good::filter(array(
				'catId'  => intval($_GET['catid']),
				'shopId' => HpCatalog::SHOP_ID,
				'producers' => array($_GET['pid']),
			));
		}else
			 $dataProvider = $seria -> getGoodsDataProvider();
		
		return $this -> render('goodList',array(
			'category'  => $category,
			'producer'  => $producer,
			'serias'    => $serias,
			'seria'     => $seria,
			'producers' => $category -> getProducerList(),
			'dataProvider' => $dataProvider,
		),true);
	}
	
	/**
	 * Карточка товара
	 * @return html
	 */
	public function actionGood(){
		$goodId = $_GET['goodid'];
		
		$good = Good::model()->findByPk($goodId);
		if ($good == NULL){
			throw new CHttpException(404,'Не указан товар или указан несуществующий');
		}
		
//		$catId   = $good -> categoriesId;
//		$catName = $good -> Category -> Name;		
//		$catUrl  = $this->createUrl('/',array('pageid'=>Yii::app()->params['catalogpageId'],'catid'=>$catId));		
//		$this->addBreadCrumb($catName,$catUrl);
				
//		$producerId   = $good -> producersId;
//		$producerName = $good -> Producer -> Name;
//		$producerUrl  = $this->createUrl('/',array('pageid'=>Yii::app()->params['catalogpageId'],'catid'=>$catId, 'producers[]'=>$producerId));		
//		$this->addBreadCrumb($producerName,$producerUrl);
		
		$this->addBreadCrumb($producerName .' '.$good -> Name);
		$shop = shop::model()->findByPk( HpCatalog::SHOP_ID );
		
		return $this->render('good',array(
			'shop'=>$shop,
			'Good'=>$good,
		));
	}
	
	/**
	 * Инициализация всех необходимых js
	 */
	public function registerScript(){	
		$catalogCss = Yii::app() -> assetManager -> publish(YiiBase::getPathOfAlias('HpCatalog.css').'/catalog.css');
		Yii::app() -> clientScript -> registerCssFile($catalogCss);
		
		Yii::app() -> clientScript -> registerCoreScript('jquery');
		
		$catalogJs  = Yii::app() -> assetManager -> publish(YiiBase::getPathOfAlias('HpCatalog.js').'/catalog.js');
		Yii::app() -> clientScript -> registerScriptFile($catalogJs);
	}
	
	/**
	 * Задать заголовок страницы
	 */
	public function setPageHeader(){
		$catId = $this->getValue('catid');
		if(empty($catId)) $catId = $this -> defaultCategoryId;
		
		$title = CHtml::link("Каталог",'/');
		
		$category = CategoryTree::model()->findByPk($catId);
		$parents = $category->getParents();
		if(is_array($parents)) foreach($parents as $parent){
			$catName = $parent -> Id == $_GET['catid'] ?
				$parent -> Category -> Name :
				CHtml::link(trim($parent -> Category -> Name),'/catalog?catid='.$parent->Id);
			
			$title .= ' > ' . $catName;
		}
		
		Yii::app() -> controller -> setPageTitle($title);
	}
	
	/**
	 * страница категории товаров
	 * отображает список серий категории
	 */
	public function actionCategoryPage(){
		$category = Category::model() -> findByPk($_GET['catid']);
		
		if($_GET['pid'] == 'all'){
			$producer = new Good;
			$producer -> Id = 'all';
			$producer -> Name = 'Все';
		}else
			$producer = Good::model() -> findByPk($_GET['pid']);
		
		if(empty($category))
			return 'Нет такой категории';
						
		$producerId = intval($_GET['pid']) > 0 ? intval($_GET['pid']) : NULL;		
		$serias = Good::getSerias($producerId,HpCatalog::SHOP_ID);
		
		return $this -> render('categoryPage',array(
			'category' => $category,
			'producer' => $producer,
			'serias'   => $serias,
			'producers' => $category -> getProducerList(),
		),true);
	}
	

	/**
	 * Отображение результатов поиска
	 */
	public function run(){
		$this -> registerScript();

        $this -> addBreadCrumb('Каталог','/catalog');
        
        switch ($this->mode){
            case 'full':
            	if(false /*isset($_GET['catid']) && isset($_GET['pid']) && !isset($_GET['sid'])*/)
            		echo $this -> actionCategoryPage();
                elseif( isset($_GET['goodid']) )
                    echo $this->actionGood();
                elseif( isset($_GET['AddToCart']))
                    echo $this->actionAddToCart();
                elseif( $_GET['mode']=='ajax'){
                    switch ($_POST['action']){
                        case 'BasketButton':
                            echo $this->actionBasketButton();
                            break;
                        case 'AddToCart':
                            echo $this->actionAddToCart();
                            break;
                        case 'RemoveFromCart':
                            echo $this->actionRemoveFromCart();
                            break;
                        case 'RegisterOrder':
                            echo $this->actionRegisterOrder();
                            break;
                    }
                    exit;
                }else
                    echo $this->actionGoodList();
                break;
            case 'filter':
                echo $this -> actionFilter();
                break;
            case 'basket':
                echo $this -> actionBasket();
                break;
            case 'cattree':
            	echo $this -> actionCatTree();
            	break;
            case 'basketButton':
            	echo $this -> actionBasketButton();
            	break;
            case 'producersByCat':
            	echo $this -> actionProducersByCat();
            	break;
            case 'good':
            	echo $this -> actionGood();
            	break;
        }
	}
	
	/**
	 * Рекурсивное построение меню для дерева категорий
	 * @param arary_of_Category $activeCategory
	 * @param Category $categories текущая активная категория
	 */
	public function buildMenuRecursive($activeCategory,$categories){
		$res = array();
		
		if(is_array($categories)) foreach($categories as $category){
			$isActive    = $activeCategory->Id == $category->Id;
			$hasChildren = $activeCategory->isDescendantOf($category);
			$goodNumber  = 10;//intval($category->Category->getGoodNum());

			$catName = $hasChildren ?
				$category->Category->Name : 
				$category->Category->Name . " (". $goodNumber .")";
			
				$temp = array(
					'url'    => '/?catid='.$category->Id,
					'label'  => $catName,
					'active' => $isActive,		
				);
			
			if($hasChildren || $isActive) $temp['items'] = $this -> buildMenuRecursive($activeCategory,$category->Category->fetchChildren());
			if($goodNumber > 0) $res[] = $temp;
		}
		
		return $res;
	}
	
	/**
	 * Построение дерева категорий
	 */
	public function actionCatTree(){
		$category = Category::model() -> findByPk(12);
				
		$activeCategory = CategoryTree::model() -> findByPk($_GET['catid']);
		if(empty($activeCategory)){
			if(isset($_GET['goodid'])){
				$good = Good::model() -> findByPk($_GET['goodid']);
				if(!empty($good)) $activeCategory = CategoryTree::model() -> findByPk($good -> categoriesId);
			}
			if(empty($activeCategory)) $activeCategory = $category->Tree;//CategoryTree::model() -> findByPk($this -> defaultCategoryId);
		}
		
		//var_dump($category -> fetchChildren());
		
		/*$menu[-1] = array(
			'url'    => '/catalog?catid='.$category->Id,
			'label'  => 'Все',
			'active' => $category -> Id == $activeCategory->Id,		
		);*/
		
		$menu = $this -> buildMenuRecursive($activeCategory,$category -> fetchChildren());
				
		return $this->render('cattree',array(
			'menu' => $menu,
		),true);
	}
	
	
	/**
	 * Форма настройки виджета в админке
	 * @param unknown_type $model
	 */
	public function displayOptionsForm(){		
		echo CHtml::dropDownList('LContent[Raw][mode]',$this -> mode,array(
			'full'   => 'full',
			'filter' => 'filter',
			'basket' => 'basket',
			'ajax'   => 'ajax',
			'cattree' => 'cattree',
		));
	}
	
	/**
	 * Сохраняем настройки виджета в админке
	 */
	public function save($options){
		$this -> mode = $options['mode'];
		return true;
	}
	
	/**
	 * Удаляем настройки виджета в админке
	 */
	public function delete(){
		return true;
	}
	
	/**
	 * Возвращает массив имен свойств, которые у виджета надо сохранить в настройках
	 */
	public function options(){
		return array('mode');
	}
	
}