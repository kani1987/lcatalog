<?

class LBasketGood extends CModel {
    
    public function attributeNames(){
        return array();
    }
        
    private $_Id = false;
    private $_good = false;
    private $_properties = false;
    private $_Price = false;
    private $_Name = false;
    
    public function __construct($goodId, $properties){
        $this -> _good = Good::model() -> findByPk($goodId);
        if(empty($this -> _good))
            throw new CException('Невозможно добавить товар в корзину: такого товара нет');
        
        $this -> properties = $properties;
        
        $this -> _Price = $this -> good -> getTotalPrice($properties);
        $this -> _Id = $goodId;
        $this -> _Name = $this -> _good -> Producer -> Name . ' ' . $this -> _good -> Name;
    }
    
    public function getGood(){
        return $this -> _good;
    }
    
    public function getImage(){
        return $this -> _good -> getImage();
    }
    
    public function getProperties(){
        return $this -> _properties;
    }
    
    public function setProperties($properties){
        if(is_array($properties)) foreach($properties as $propertyId) {
            $value = PrListValue::model() -> with('Property') -> findByPk($propertyId);
            $key   = $value -> Property -> Name;
            
            $this -> _properties[$key] = $value;
        }
    }
    
    public function getPrice(){
        return $this -> _Price;
    }
    
    public function getName(){
        return $this -> _Name;
    }
    
    public function getId(){
        return $this -> _Id;
    }
    
}