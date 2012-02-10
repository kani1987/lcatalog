<?php

/**
 * Отвечает за работу с медиафайлами
 * @author yura
 */
class MediaFile extends CActiveRecord{
	// ## поля Number и Priority имеют смысл только,
	// ## если речь идет о "goods_thousands" методе хранения медиафайлов
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function tableName(){
		return 'lcatalog__mediafiles';
	}

	public function rules(){
		return array(
		array('Priority, TypeId', 'numerical', 'integerOnly'=>true),
		array('Name', 'length', 'max'=>255),
		array('Description', 'safe'),
		);
	}
	
	public function attributeLabels(){
		return array(
			'Name' => 'Имя',
			'Description' => 'Описание',
		);
	}

	public function relations(){
		return array(
			'Type' => array(self::BELONGS_TO,'MediaType' ,'TypeId'),
			'GoodBinding' => array(self::HAS_ONE   ,'GoodsMedia','mediafilesId'),
		);
	}

	/**
	 * устаревшая функция, сохраненная для обратной совместимости
	 */
	public function createMediaUrl($previewNum='0'){
		return $this -> getUrl($previewNum);
	}

	/**
	 * Формирует относительный URL медиафайла
	 *
	 * В зависимости от метода хранения данного типа медиафайлов
	 *
	 * Если "all_together", то возвращаемый url:
	 * 		$typeUrl/$mediaId_$typePostfix_$previewNum.$typeExt
	 * Если "goods_thousands", то url:
	 * 		$typeUrl/$goodsIdThousand/$goodsId_$mediaNumber_$previewNum.$typeExt
	 *
	 * @param int $previewNum номер превьюшки файла
	 * @param string $mode - генерация абсолютного урла или относительного
	 * @return string url
	 */
	public function getUrl($previewNum='0',$mode='relative'){
		$previewNum = intval($previewNum);
		$url = $this -> Type -> Url;

		if ($this -> Type -> Method == 'all_together'){
			$url .= $this->Id;
		}elseif( $this -> Type -> Method == 'goods_thousands'){
			$obj = GoodsMedia::model()->findByAttributes(array('mediafilesId' => $this->Id));

			if($obj == NULL) return false;
			$goodId = $obj -> goodsId;
			$goodIdThousand = intval($goodId / 1000);

			$url .= $goodIdThousand.'/'.$goodId.'_'.$this->Number;
		}

		$url .= '_'.$previewNum.'.'.$this->Type->Extension;

		if($mode == 'relative') return $url;

		return Yii::app() -> request -> hostInfo . '/media/'.$url;
	}

	/**
	 * Возвращает физический путь к медиафайлу
	 * Внутреннее устройство аналогично устройству $this->getUrl()
	 * @param $previewNum номер превьюшки файла
	 * @return string destination path
	 */
	public function getDestination($previewNum='0',$goodId=NULL){
		$mediaDir = Yii::app()->getModule('lcatalog')->mediaDir;

		$dir = $mediaDir . DIRECTORY_SEPARATOR . $this -> Type -> Url;

		if ($this -> Type -> Method == 'all_together'){
			$dir .= '/'.$this->Id;
		}elseif($this -> Type -> Method == 'goods_thousands'){
			if(empty($goodId)) throw new CException('goodId needed', 7);

			$goodId = intval($goodId);
			$goodIdThousand = intval($goodId / 1000);

			if(!is_writeable($dir)) throw new CException('Не хватает прав на запись в директорию '.$dir, 14);

			$dir .= $goodIdThousand;
			if(!file_exists($dir)) mkdir($dir);
			$dir .= DIRECTORY_SEPARATOR . $goodId . '_' . $this->Number;
		}
		$dir .= '_'.$previewNum.$this->Type->Postfix.'.'.$this->Type->Extension;

		return $dir;
	}


	/**
	 * Считает количество товаров, связанных с медиафайлом
	 */
	public function countLinks(){
		$linkings = GoodsMedia::model()->findAllByAttributes(array('mediafilesId'=>$this -> Id));
		return count($linkings);
	}

	/**
	 * Получить модель типа медиафайла по имени (не путать с $this -> Name !!!) медиафайла
	 * @param string $name
	 * @return MediaType model
	 */
	static private function getTypeByName($name){
		$ext = array_pop(explode('.',$name));

		return MediaType::model()->findByAttributes(array('Extension' => $ext));
	}

	/**
	 * Создать новый медиафайл (создать все нужные записи в БД + создать все нужные файлы в файловой системе)
	 * на основе загруженного файла
	 * @param $goodIds    - массив Id товаров, к которым следует привязать указанный медиафайл
	 * @param int $typeId - идентификатор типа медиафайла
	 * @return bool
	 *
	 * TODO: починить транзакции
	 */
	public static function upload($goodIds,$typeId=NULL){
		$uploadedFile = CUploadedFile::getInstanceByName('mediafile');
		if(empty($uploadedFile))      throw new CException('Не удалось загрузить файл!', 1);
		if($uploadedFile -> hasError) throw new CException('Ошибка при загрузке файла: '.$uploadedFile->getError(),1);

		$type = empty($typeId) ? MediaFile::getTypeByName($uploadedFile -> name) : MediaType::model()->findByPk($typeId);

		if(empty($type)){
			throw new CException('Тип указанного файла неизвестен системе: '.$uploadedFile -> name, 2);
		}
		
			foreach($goodIds as $goodId){

				$media = new MediaFile();
				$media -> TypeId = $type -> Id;

				switch($type->Method){
					case 'goods_thousands':
						// ## здесь идентифицируем сколько у соотв. товара медиафайлов
						$good   = Good::model()->findByPk($goodId);
						$number = $good -> countMediaFiles();

						$media -> Number   = $number + 1;
						$media -> Priority = $number + 1;

						if(!$media -> save()) throw new CException('Не удалось сохранить данные о новом медиафайле типа goods_thou', 2);

						$goodsMedia = new GoodsMedia();
						$goodsMedia -> mediafilesId = $media->Id;
						$goodsMedia -> goodsId = $goodId;

						if(!$goodsMedia -> save()) throw new CException('Не удалось сохранить связку товара с медиафайлом', 3);
						break;
					case 'all_together':
						if(!$media -> save()) throw new CException('Не удалось сохранить данные о новом медиафайле типа all_together', 2);
						$cat = Category::model()->findByPk($goodId);
							
						if(!empty($cat -> ImagesId)){
							$temp = MediaFile::model()->findByPk($cat -> ImagesId);
							if ($temp -> countLinks() == 0) $temp -> destroyCompletely();
						}
							
						$cat -> ImagesId = $media -> Id;
						if(!$cat -> save()) throw new CException('Не удалось сохранить связку иконки и категории');
						break;
					default:
						throw new CException('Неверный вызов функции сохранения медиафайла',15);
				}

				$tempfile = $uploadedFile -> tempName;

				if($type -> Extension == 'jpg' && $mode == 'good'){
					$size = getimagesize($tempfile);
					$previewSizes = array(150,300,600);

					$scale = max(array($size[0],$size[1]));
					foreach($previewSizes as $i=>$rightSize){
						$dest = $media->getDestination($i,$goodId);
						
						if($scale > $rightSize){
							if(!$media->resizeImage($tempfile, $dest, $rightSize, $rightSize)){
								throw new CException('Ошибка при сохранении измененного превью файла', 56);
							};
						}else{
							if(! $uploadedFile->saveAs($dest,false)){
								throw new CException('Ошибка при сохранении превью файла'.$dest, 57);
							}
						}
					}
				}else{
					$dest = $media->getDestination('0',$goodId);
					if(!$uploadedFile->saveAs($dest,false)){
						throw new CException('Ошибка при сохранении файла', 58);
					}
				}
			}

			unlink($tempfile);
			return true;
	}
	
	
	/**
	 * Сохранение превьюшки флеша
	 * @param int $projectId
	 * @return MediaFile | false
	 */
	public static function uploadFlashImage($projectId){
		if(!isset($GLOBALS["HTTP_RAW_POST_DATA"])) return false;
		
		$project = Good::model() -> findByPk($projectId);
		if(empty($project)) return false;
		
		$media = new Media();
		$media -> Name = 'preview for flash project';
		$media -> save();
		
		$dest = $media -> getDestination('0',$project -> Id);
		if(false === file_put_contents($dest,$jpg)) return false;
		
		if($media -> createLink($project -> Id)) return $media->getUrl('0','absolute');
		else return false;
	}

	/**
	 * Создает связь между существующим товаром и существующим медиафайлом
	 * при условии что тип медиафайла допускает это
	 *
	 * @param $originalId
	 * @return bool
	 */
	public function createLink($originalId){
		if($this -> Type -> Method != 'all_together') return false;

		$link = new GoodsMedia();
		$link -> mediafilesId = $this -> Id;
		$link -> goodsId = $originalId;

		return $link -> save();
	}



	/**
	 * Очистить базу и файловую систему от всех следов медиафайла
	 * @return bool
	 */
	public function destroyCompletely($goodId=NULL){
		GoodsMedia::model()->deleteAllByAttributes(array('mediafilesId'=>$this->Id));
		$this->delete();
		
		for($i=2;$i>=0;$i--){
			$path = $this->getDestination($i,$goodId);
			$this->deleteFileOrDir($path);
		}

		return true;
	}

	/**
	 * Функция resizeImage(): генерация thumbnails
	 * успешно содрано отсюда http://www.php5.ru/articles/image
	 * генерирует jpg-preview на основе png, gif, jpg или wbmp файла
	 * Параметры:
	 * $src             - имя исходного файла
	 * $dest            - имя генерируемого файла
	 * $width, $height  - ширина и высота генерируемого изображения, в пикселях
	 * Необязательные параметры:
	 * $rgb             - цвет фона, по умолчанию - белый
	 * $quality         - качество генерируемого JPEG, по умолчанию - максимальное (100)
	 */
	private function resizeImage($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=100)
	{
		if (!file_exists($src)) return false;

		$size = getimagesize($src);

		if ($size === false) return false;

		// Определяем исходный формат по MIME-информации, предоставленной
		// функцией getimagesize, и выбираем соответствующую формату
		// imagecreatefrom-функцию.
		$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
		$icfunc = "imagecreatefrom" . $format;
		if (!function_exists($icfunc)) return false;

		$x_ratio = $width / $size[0];
		$y_ratio = $height / $size[1];

		$ratio       = min($x_ratio, $y_ratio);
		$use_x_ratio = ($x_ratio == $ratio);

		$new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
		$new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
		$new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
		$new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

		$isrc = $icfunc($src);

		//$idest = imagecreatetruecolor($width, $height);
		//imagefill($idest, 0, 0, $rgb);
		//imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);

		// чтобы не добавлять белые поля у картинки
		$idest = imagecreatetruecolor($new_width, $new_height);
		imagecopyresampled($idest, $isrc, 0, 0, 0, 0, $new_width, $new_height, $size[0], $size[1]);

		imagejpeg($idest, $dest, $quality);

		imagedestroy($isrc);
		imagedestroy($idest);

		return true;
	}

	/**
	 * Delete a file or recursively delete a directory
	 * Успешно содрано из комментов отсюда: http://ru2.php.net/manual/en/function.unlink.php
	 *
	 * @param string $str Path to file or directory
	 */
	private function deleteFileOrDir($str){
		if(is_file($str)){
			return @unlink($str);
		}
		elseif(is_dir($str)){
			$scan = glob(rtrim($str,'/').'/*');
			foreach($scan as $index=>$path){
				$this->recursiveDelete($path);
			}
			return @rmdir($str);
		}
	}
}