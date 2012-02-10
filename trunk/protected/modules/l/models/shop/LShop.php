<?php

/**
 * This is the model class for table "lcatalog__Shop".
 *
 * The followings are the available columns in table 'lcatalog__Shop':
 * @property integer $Id
 * @property string $Name
 * @property string $Description
 * @property string $Type
 */
class Shop extends CActiveRecord
{
	private $_types = array(
		'first'  => 'Магазин первого порядка',
		'second' => 'Магазин второго порядка',
		'third'  => 'Магазин третьего порядка',
	);
	
	/**
	 * ссылка на страницу магазина
	 */
	public function getPageUrl(){
		return '/lcatalog/shop/view?id=' . $this -> Id;
	}
	
	public function getPossibleTypes(){
		return $this -> _types;
	}
	
	public function getDisplayType(){
		return $this -> _types[$this -> Type];
	}
	
	
	public static function getByPk($shopId){
		return Shop::model() -> findByPk($shopId);
	}
    
    /**
     * Получить все товары магазина, с ценой > 0
     */
    public function getAllGoods(){
        $sql = "SELECT g.*,pr.Id AS priceId  FROM lcatalog__Good AS g"
             . " INNER JOIN lcatalog__prices AS pr ON pr.goodsId=g.Id"
             . " WHERE pr.shopId=:shopid AND pr.prlistsId=0 AND pr.Price>0"
        ;
        
        return Good::model() -> findAllBySql($sql,array(':shopid' => $this->Id));
    }
	
	/**
	 * Обладает ли пользователь правами администратора магазина
	 * @param int $userId
	 */
	public function isAdmin($userId){	
		return 1 == ShopUser::model() -> countByAttributes(array(
			'shopId' => $this -> Id,
			'userId' => $userId
		));
	}
	
	/**
	 * получить список магазинов по userId
	 * @param int $userId
	 */
	public static function getShopsByUser($userId){
		if (empty($userId)) return array();
		
		$criteria = new CDbCriteria();
		
		$criteria -> join = "INNER JOIN lcatalog__Shop_User AS su ON su.shopId=t.Id";
		$criteria -> compare('su.userId',$userId);
		
		return Shop::model() -> findAll($criteria);
	}
	
	/**
	 * получить список администраторов магазина
	 * @return CActiveDataProvider
	 */
	public function getAdmins(){
		$criteria = new CDbCriteria;
		
		$criteria -> join = 'INNER JOIN lcatalog__Shop_User AS su ON su.userId=t.Id';
		$criteria -> compare('su.shopId',$this -> Id);		                  
		
		return new CActiveDataProvider('User', array(
			'criteria'   => $criteria,
			'pagination' => false,
		));
	}
	
	/**
	 * получить список пользователей, не являющихся администраторами данного магазина
	 * @return array
	 */
	public function getPossibleAdminsList(){
		$criteria = new CDbCriteria();
		
		$criteria -> select .= ',CONCAT(t.firstname," ",t.lastname) AS lastname';
		$criteria -> join = 'INNER JOIN {{users}} AS u ON u.id=t.user_id'
		                  . ' LEFT JOIN lcatalog__Shop_User AS su ON su.userId=u.id AND su.shopId=:shopid' 
		;
		$criteria -> compare('u.status',1);
		$criteria -> params[':shopid'] = $this -> Id;
		$criteria -> addCondition('su.userId IS NULL');
		
		return CHtml::listData(Profile::model() -> findAll($criteria),'user_id','lastname');
	}
	
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return Shop the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'lcatalog__Shop';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Description', 'required'),
			array('Name', 'length', 'max'=>255),
			array('Type', 'length', 'max'=>6),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, Name, Description, Type', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'Profiles' => array(self::MANY_MANY,'Profile','lcatalog__Shop_User(shopId,userId)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id' => 'ID',
			'Name' => 'Имя',
			'Description' => 'Описание',
			'Type' => 'Тип',
			'displayType' => 'Тип',
		);
	}
	
	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('Id',$this->Id);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('Type',$this->Type,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
}