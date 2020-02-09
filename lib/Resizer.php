<?php

	namespace Resizer\lib;
	use Exception;

	class Resizer {
		private $generateThumbnail;
		private $thumbnailSize;
		private $thumbnailName;
		private $destinationFolder;
		private $maxSize;
		private $allowedSize;
		private $quality;
		private $saveDir;
		private $type;
		private $fileCount;
		private $imageData;
		private $uploadErrorNo;
		private $tmpName;
		private $sizeInfo;
		private $imageSize;
		private $originalWidth;
		private $originalHeight;
		private $newWidth;
		private $newHeight;
		private $imageType;
		private $fileName;
		private $imageRes;
		private $imageScale;
		private $canvas;
		private $xOffset;
		private $yOffset;
		private $response = array();
		
		/**
		 * Set default values from passed imageData
		 *
		 * @param array $imageData
		 */
		public function __construct(array $imageData) {	
			$this->generateThumbnail = $imageData['generateThumbnail'];
			$this->thumbnailSize = $imageData['thumbnailSize'];
			$this->thumbnailName = $imageData['thumbnailName'];
			$this->destinationFolder = $imageData['destinationFolder'];
			$this->maxSize = $imageData['maxSize'];
			$this->allowedSize = $imageData['allowedSize'];
			$this->quality = $imageData['quality'];
			$this->imageData = $imageData['imageData'];
			$this->type = $imageData['type'];
			$this->fileCount = !empty($this->imageData['name']) ? count($this->imageData['name']) : 0;
		}
		
		/**
		 * Exposed to client for resizing 
		 *
		 * @return array
		 */
		public function resize() {
			return $this->type == 'local' ? $this->resizeFromLocal() : $this->resizeFromUrl();
		}

		/**
		 * Resize image from local
		 *
		 * @return array
		 */
		private function resizeFromLocal() {
			if(empty($this->fileCount)) {
				throw new Exception('HTML file input field must be in array format!');
			}
			for ($x = 0; $x < $this->fileCount; $x++) {
				if ($this->imageData['error'][$x] > 0) {
					$this->uploadErrorNo = $this->imageData['error'][$x];
					throw new Exception($this->getUploadError()); 
				}

				if(is_uploaded_file($this->imageData['tmp_name'][$x])) {
					$this->xOffset = 0;
					$this->yOffset = 0;
					$this->tmpName = $this->imageData['tmp_name'][$x];
					$this->getImageInfo($this->type);
					$this->imageRes = $this->getImageResource();
					$this->fileName = $this->imageData['name'][$x];
					$this->resizeImage($x);
					if($this->generateThumbnail) {
						if($this->originalWidth > $this->originalHeight) {
							$this->xOffset = ($this->originalWidth - $this->originalHeight) / 2;
							$this->originalWidth = $this->originalHeight  = $this->originalWidth - ($this->xOffset * 2);
						} else {
							$this->yOffset = ($this->originalHeight - $this->originalWidth) / 2;
							$this->originalWidth = $this->originalHeight  = $this->originalHeight - ($this->yOffset * 2);
						}
						$this->generateThumbnail($x);
					}

				} else {
					throw new Exception('Possible file upload attack.'); 
				}
			}
			return $this->response;
		}

		/**
		 * Resize image from URL
		 *
		 * @return array
		 */
		private function resizeFromUrl() {
			$this->xOffset = 0;
			$this->yOffset = 0;
			$this->tmpName = file_get_contents($this->imageData);
			$this->getImageInfo($this->type);
			$this->imageRes = imagecreatefromstring(file_get_contents($this->imageData));
			$this->fileName =  substr(md5(uniqid(rand().md5(rand()))), 0, 16).$this->getImageExtension();
			$this->resizeImage(0);
			if($this->generateThumbnail) {
				if($this->originalWidth > $this->originalHeight) {
					$this->xOffset = ($this->originalWidth - $this->originalHeight) / 2;
					$this->originalWidth = $this->originalHeight  = $this->originalWidth - ($this->xOffset * 2);
				} else {
					$this->yOffset = ($this->originalHeight - $this->originalWidth) / 2;
					$this->originalWidth = $this->originalHeight  = $this->originalHeight - ($this->yOffset * 2);
				}
				$this->generateThumbnail(0);
			}
			return $this->response;
		}

		/**
		 * Resize image based on max size
		 *
		 * @param integer $current
		 * @return void
		 */
		private function resizeImage(int $current) {
			$this->saveDir = __DIR__.'/'.$this->destinationFolder.'/original/';

			//do not resize if image is smaller than max size
			if($this->originalWidth <= $this->maxSize || $this->originalHeight <= $this->maxSize) {	
				$this->newWidth = $this->originalWidth;
				$this->newHeight =  $this->originalHeight;						
			}
			else {					
				$this->imageScale = min($this->maxSize/$this->originalWidth, $this->maxSize/$this->originalHeight);
				$this->newWidth = ceil($this->imageScale * $this->originalWidth);
				$this->newHeight = ceil($this->imageScale * $this->originalHeight);	
			}

			if($this->resampleImage()) {
				$this->response[$current]['normal'] = $this->saveImage();
			}
		}

		/**
		 * Generate thumbnail
		 *
		 * @param integer $current
		 * @return void
		 */
		private function generateThumbnail(int $current) {
			for ($i = 0; $i < count($this->thumbnailSize); $i++) { 
				$this->newWidth = $this->thumbnailSize[$i];
				$this->newHeight = $this->thumbnailSize[$i];
				$this->saveDir = __DIR__.'/'.$this->destinationFolder.'/'.$this->thumbnailName[$i].'/';	
				
				if($this->resampleImage()) {
					$this->response[$current][$this->thumbnailName[$i]] = $this->saveImage();
				}
			}
		}

		/**
		 * Get image resource of current image
		 *
		 * @return any
		 */
		private function getImageResource() {
			switch($this->imageType) {
				case 'image/png':
					return imagecreatefrompng($this->tmpName);
					break;
				case 'image/gif': 
					return imagecreatefromgif($this->tmpName);
					break;			
				case 'image/jpeg': case 'image/pjpeg':
					return imagecreatefromjpeg($this->tmpName); 
					break;
				default:
					return false;
			}
		}

		/**
		 * Get image info of current image
		 *
		 * @param string $imageSource
		 * @return void
		 */
		private function getImageInfo(string $imageSource) {
			$this->sizeInfo = ($imageSource == 'local') ? getimagesize($this->tmpName) : getimagesizefromstring($this->tmpName);
			$this->imageSize = ($imageSource == 'local') ? filesize($this->tmpName) : strlen($this->tmpName);
			if($this->sizeInfo && $this->imageSize < $this->allowedSize * 10096) {
				$this->originalWidth 	= $this->sizeInfo[0]; 
				$this->originalHeight = $this->sizeInfo[1]; 
				$this->imageType = $this->sizeInfo['mime'];
			} else {
				throw new Exception("Make sure file is valid image!");
			}
		}

		/**
		 * Resample current image
		 *
		 * @return any
		 */
		private function resampleImage() {
			$this->canvas = imagecreatetruecolor($this->newWidth, $this->newHeight);
			if(imagecopyresampled($this->canvas, $this->imageRes, 0, 0, $this->xOffset, 
			$this->yOffset, $this->newWidth, $this->newHeight, $this->originalWidth, $this->originalHeight)) {
				return true;
			}	
		}
		
		/**
		 * Save current image
		 *
		 * @return any
		 */
		private function saveImage() {
			if(!is_dir($this->saveDir)) {
				if(!mkdir($this->saveDir, 0755, true)) {
					throw new Exception($this->saveDir . ' - directory doesn\'t exist!');
				}
			}
			
			switch($this->imageType) {
				case 'image/png': 
					imagepng($this->canvas, $this->saveDir.$this->fileName); 
					imagedestroy($this->canvas); 
					return $this->fileName; 
					break;
				case 'image/gif': 
					imagegif($this->canvas, $this->saveDir.$this->fileName); 
					imagedestroy($this->canvas); 
					return $this->fileName; 
					break;          
				case 'image/jpeg': case 'image/pjpeg':
					imagejpeg($this->canvas, $this->saveDir.$this->fileName, $this->quality);
					imagedestroy($this->canvas);
					return $this->fileName; 
					break;
				default: 
					imagedestroy($this->canvas);
					return false;
			}
		}	
		
		/**
		 * Get image extension of current image
		 *
		 * @return any
		 */
		public function getImageExtension() {
			if(empty($this->imageType)) {
				return false;   
			}
			switch($this->imageType) {
				case 'image/gif': 
					return '.gif';
				case 'image/jpeg': 
					return '.jpg';
				case 'image/png': 
					return '.png';
				default: 
					return false;
			}
		}
		
		/**
		 * Get possible upload error
		 *
		 * @return string
		 */
		private function getUploadError() {
			switch($this->upload_error_no) {
				case 1 : return 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
				case 2 : return 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
				case 3 : return 'The uploaded file was only partially uploaded.';
				case 4 : return 'No file was uploaded.';
				case 5 : return 'Missing a temporary folder.';
				case 6 : return 'Failed to write file to disk.';
			}
		}
	}

?>