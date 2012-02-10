<?php

/**
 * Модель спецпредложения
 * This is the model class for table "{{offers}}".
 *
 * The followings are the available columns in table '{{offers}}':
 * @property integer $Id
 * @property string $Name
 * @property string $Description
 * @property string $FullDescription
 * @property string $Start
 * @property string $Finish
 * @property string $Icon
 */
class Offer extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Offer the static model class
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
		return 'lcatalog__offers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Name, Description, FullDescription, shopId', 'required'),
			array('shopId', 'numerical', 'integerOnly'=>true),
			array('Name', 'length', 'max'=>256),
			array('Description, Icon', 'length', 'max'=>2000),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('Id, Name, Description, FullDescription, Start, Finish, Icon', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'Id'              => 'ID',
			'Name'            => 'Наименование',
			'Description'     => 'Краткое описание',
			'FullDescription' => 'Полное описание',
			'Start'           => 'Время начала',
			'Finish'          => 'Время завершения',
			'Icon'            => 'Иконка',
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
		$criteria->compare('FullDescription',$this->FullDescription,true);
		$criteria->compare('Start',$this->Start,true);
		$criteria->compare('Finish',$this->Finish,true);
		$criteria->compare('Icon',$this->Icon,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 * Найти все спецпредложения по магазину
	 * @param int $shopId
	 * @return array of offers
	 */
	public static function findAllByShop($shopId){
		return self::model()->findAllByAttributes(array('shopId' => $shopId));
	}
	
	public static function getDataProviderByShop($shopId){
		$criteria = new CDbCriteria();
		$criteria -> compare('shopId',$shopId);
		
		return new CActiveDataProvider('Offer', array(
			'criteria' => $criteria,
		));
	}
	
	public function getIconFile(){
		return !empty($this->Icon) ? CHtml::image('/media/images/offerIcons/'.$this->Id.'.'.$this->Icon) : 'пусто';
	}
	
	public function save(){
		if(!parent::save()) return false;
		
		$file  = CUploadedFile::getInstance($this, 'IconFile');
		
		$path  = YiiBase::getPathOfAlias('webroot.media.images.offerIcons');
		$path .= '/'.$this -> Id .'.'. $file->extensionName;
		if(!empty($file) && $file -> saveAs($path)){
			$this -> Icon = $file->extensionName;
			$this -> save();
		}
		
		return true;
	}
	
	public function isChecked($priceId){
		return OfferPrice::model() -> countByAttributes(array('priceId'=>$priceId,'offerId'=>$this->Id)) > 0;
	}
}