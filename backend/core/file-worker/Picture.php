<?php

namespace app\core\file_worker;

class Picture{
	
	public static $instance;
	public $dir;  //Директория для сохранения изображения
	public $imgTypes; //Допустимые расширения
	public $imgResize; ////Размеры изображения на выходе array(width, height)
	public $imgMaxSize = array(1000, 1000); //Максимально допустимые размеры изображения
	public $imgFile; // Массив $_FILES[inputname], хранящий необходимое нам изображение
	
	public static function getInstance()
    {
		
		if (self::$instance===null){
			
			self::$instance = new self;
			
		}
		
		return self::$instance;
	}
###function end###



	public function __construct($directory = 'articles/preview/', $imgResize = array(280,210)){
		$this->dir = ROOT_DIR.'/images/'.$directory;
		$this->imgTypes = array('jpg', 'jpeg', 'gif', 'png');
		
		//Если необходимая ширина и высота выше максимально возможных
		if($imgResize[0]>$this->imgMaxSize[0]){
			$imgResize[1] = ($this->imgMaxSize[0]/$imgResize[0])*$imgResize[1];
			$imgResize[0] = $this->imgMaxSize[0];
		}
		$this->imgResize = $imgResize;
	}
###function end###



//Изменение пропорций изображения, пока оно не уместится в указанные границы
	public function changeSize($width, $height, $needed = array(280,210)){
		
		
		//Если ширина больше необходимой
		if($width>$needed[0]){
			//Определяем пропорцию
			if($width!=$needed[0]){
				$proportion = $width/$needed[0];
			}elseif($height!=$needed[1]){
				$proportion = $height/$needed[1];
			} else {
				return $needed;
			}
			//Задаем высоте необходимое разрешение
			$width = $needed[0];
			if($proportion>1){
				$height = $height/$proportion;
			} else{
				$height = $height*$proportion;
			}
		}
		
		//Если высота больше необходимой
		if($height>$needed[1]){
			
			//Определяем пропорцию
			if($width!=$needed[0]){
				$proportion = $width/$needed[0];
			}elseif($height!=$needed[1]){
				$proportion = $height/$needed[1];
			} else {
				return $needed;
			}
			
			//Задаем ширине необходимое разрешение
			$height = $needed[1];
			if($proportion>1){
				$width = $width/$proportion;
			} else{
				$width = $width*$proportion;
			}
		}

		
		
		return array($width,$height);
		
	}
###function end###


	//Создание и загрузка превьюшки для статьи
	public function uploadImage($file,$isPreview=true, $oldName=null){
		
		$this->imgFile = $file;
		$type = pathinfo($this->imgFile['name'], PATHINFO_EXTENSION); //Расширение картинки
		
		
		if ( empty($type) OR !in_array($type, $this->imgTypes) ){ //Если расширение не определилось или недопустимое расширение
			
			return false;
			
		} else {
			
			//Имя файла
			if ($oldName === null){
				$imgName = md5(microtime()).'.'.$type;
				$destination = $this->dir.$imgName; //Путь для сохранения файла
			} else{
				$imgName = $oldName;
				
				if($isPreview){
					$destination = $this->dir.$imgName;
				}else{
					$destination = ROOT_DIR.$imgName; //Путь для сохранения файла
				}
			}
			
			
			$src_x = 0;
			$src_y = 0;
			
			list($width, $height) = getimagesize($this->imgFile['tmp_name']);
			
			//Если файл должен стать превьюшкой
			if ($isPreview){
				
				list($new_width, $new_height) = $this->changeSize($width, $height);
				
			} else { //Если это полноценное изображение
				
				//Если ширина изображения выше максимально-допустимой
				if($width>$this->imgMaxSize[0]){
					list($new_width, $new_height) = $this->changeSize($width, $height,$this->imgResize);
				} else{
					list($new_width, $new_height) = $this->changeSize($width, $height, array($width, $height));
				}
				
			}
			
			if ($new_width<$this->imgResize[0]){
					$src_x = ($this->imgResize[0]-$new_width)/2;
				}
				
				if ($new_height<$this->imgResize[1]){
					$src_y = ($this->imgResize[1]-$new_height)/2;
				}
			
			
			$image_p = imagecreatetruecolor($this->imgResize[0], $this->imgResize[1]);
			$bg_color = imagecolorallocate($image_p, 255, 255, 255);
			imagefill($image_p, 0, 0, $bg_color);
			
			// ресэмплирование
			if (strtolower($type)=='jpeg'||strtolower($type)=='jpg') {
				$image = imagecreatefromjpeg($file['tmp_name']);
				imagecopyresampled($image_p, $image, $src_x, $src_y, 0, 0,  $new_width, $new_height, $width, $height);
				imagejpeg($image_p, $destination, 80); 
			}
			if (strtolower($type)=='png') {
				$image = imagecreatefrompng($file['tmp_name']);
				imagecopyresampled($image_p, $image, 0, 0, $src_x, $src_y, $new_width, $new_height, $width, $height);
				imagepng($image_p, $destination, 8); 
			}
			if (strtolower($type)=='gif') {
				$image = imagecreatefromgif($file['tmp_name']);
				imagecopyresampled($image_p, $image, 0, 0, $src_x, $src_y, $new_width, $new_height, $width, $height);
				imagegif($image_p, $destination); 
			}
			imagedestroy($image);
		return $imgName;
		}
	}
###function end###

	
}
