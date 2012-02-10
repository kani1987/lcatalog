<?

/**
 * good catalog basket
 * @author yurap
 */
class LBasket extends CModel {
    
    public function attributeNames(){
        return array();
    }
    
    /**
     * Товары хранятся в сессиях в таком виде:
     * $session['goods'][$goodId] = array(
     *      '0' => array($prlistsId1, $prlistsId2, ...),
     *      '1' => array($prlistsId1, $prlistsId2, ...),
     *      ...
     * )
     */
    private $_goods = false;
    
    public function __construct(){
        $session = Yii::app()->getSession();
        $session->open();
        $this -> _goods = $session['goods'];
        $session->close();
    }

    /**
     * add good to basket
     * @param Good   $good - модель добавляемого товара
     * @param string $propertiesString - id характеристик с которыми товар добавляется через символ '_'
     * @return bool
     */
    public function addGood($goodId, $propertiesString = ''){
        if(intval($goodId) <= 0) return false;

        // ## набор характеристик, с которыми был выбран этот товар
        $properties = array();

        if( $propertiesString != '' ){
            $properties = explode('_',$propertiesString);
            // ## если последний элемент пустой (а обычно это так) - спопнуть его
            if($properties[count($properties)-1] == '') array_pop($properties);
        }

        $res = false;
        $session = Yii::app()->getSession();
        $session -> open();

        $savedGoods   = ($session['goods'] === NULL) ? array() : $session['goods'];

        // ## проверить, был ли товар в такой комлектации уже сохранен
        if(count($savedGoods[$goodId]) > 0){
            foreach($savedGoods[$goodId] as $arr){
                if(( count(array_diff($arr,$properties)) == 0 && count($arr)>0 && count($properties)==count($arr) )
                //||( count($arr)==0 && count($properties)==0 )
                ){
                    return false;
                }
            }
        }

        $this -> _goods[$goodId][] = $properties;
        $savedGoods[$goodId][] = $properties;
        $session['goods'] = $savedGoods;
        
        $session->close();
        return true;
    }

    /**
     * Delete good from basket
     * @param int $goodId
     * @param int $optionNum
     * @return bool
     */
    public function removeGood($goodId, $optionNum){
        if($goodId == NULL) return false;

        $session = Yii::app()->getSession();
        $session->open();

        $goods = $session['goods'];

        if(isset($goods[$goodId][$optionNum])) unset($goods[$goodId][$optionNum]);
        if(count($goods[$goodId]) == 0)        unset($goods[$goodId]);

        $session['goods'] = $goods;
        $session->close();
        return true;
    }
    
    /**
     * @return bool
     */
    public function runOrder($orderOptions) {
        if ($orderOptions['action'] == 'send') {
            $form = new BasketOrderForm;
            $form -> attributes = $orderOptions;
            return $form -> sendLetter($basket->_goods);
        } elseif($orderOptions['action'] == 'add') {
            $good = Good::model() -> findByPk($orderOptions['goodid']);
            $properties = $orderOptions['goodproperties'];
            return $this -> addGood($good, $properties);
        } elseif($orderOptions['action'] == 'remove') {
            $goodId = $orderOptions['goodid'];
            $optionNum = $orderOptions['optionnum'];
            return $this -> removeGood($goodId, $optionNum);
        }
    }
    
    /**
     * @return array of BasketGood
     */
    public function getGoods(){
        if(is_array($this -> _goods)) foreach($this -> _goods as $goodId => $goodOptions){
            $goods[] = new BasketGood($goodId, $goodOptions);
        }
        return $goods;
    }
    
}