<?php

    namespace Resizer\lib;
    require_once __DIR__. "/../constant.php";

    class Constant {

        private $generateThumbnail;
        private $thumbnailSize;
        private $thumbnailName;
        private $destinationFolder;
        private $maxSize;
        private $allowedSize;
        private $quality;

		/**
		 * Set default values from constant file
		 */
        public function __construct() {	
            $this->generateThumbnail = GENERATE_THUMBNAIL;
            $this->thumbnailSize = explode(',',THUMBNAIL_SIZE);
            $this->thumbnailName = explode(',',THUMBNAIL_NAME);
            $this->destinationFolder = DESTINATION_FOLDER;
            $this->maxSize = MAX_SIZE;
            $this->allowedSize = ALLOWED_SIZE;
            $this->quality = QUALITY;
        }
 
		/**
		 * Get the value of generateThumbnail
		 *
		 * @return boolean
		 */
        public function getGenerateThumbnail() {
            return $this->generateThumbnail;
        }

        /**
         * Set the value of generateThumbnail
         *
         * @param boolean $generateThumbnail
         * @return self
         */
        public function setGenerateThumbnail(bool $generateThumbnail) {
            $this->generateThumbnail = $generateThumbnail;
            return $this;
        }

		/**
		 * Get the value of thumbnailSize
		 *
		 * @return array
		 */
        public function getThumbnailSize() {
            return $this->thumbnailSize;
        }

		/**
		 * Set the value of thumbnailSize
		 *
		 * @param array $thumbnailSize
		 * @return self
		 */
        public function setThumbnailSize(array $thumbnailSize) {
            $this->thumbnailSize = $thumbnailSize;
            return $this;
        }

		/**
		 * Get the value of thumbnailName
		 *
		 * @return array
		 */
        public function getthumbnailName() {
            return $this->thumbnailName;
        }

		/**
		 * Set the value of thumbnailName
		 *
		 * @param array $thumbnailName
		 * @return self
		 */
        public function setthumbnailName(array $thumbnailName) {
            $this->thumbnailName = $thumbnailName;
            return $this;
        }

		/**
		 * Get the value of destinationFolder
		 *
		 * @return string
		 */
        public function getDestinationFolder() {
            return $this->destinationFolder;
        }

		/**
		 * Set the value of destinationFolder
		 *
		 * @param string $destinationFolder
		 * @return self
		 */
        public function setDestinationFolder(string $destinationFolder) {
            $this->destinationFolder = $destinationFolder;
            return $this;
        }

		/**
		 * Get the value of maxSize
		 *
		 * @return integer
		 */
        public function getMaxSize() {
            return $this->maxSize;
        }
 
		/**
		 * Set the value of maxSize
		 *
		 * @param integer $maxSize
		 * @return self
		 */
        public function setMaxSize(int $maxSize) {
            $this->maxSize = $maxSize;
            return $this;
        }

		/**
		 * Get the value of allowedSize
		 *
		 * @return integer
		 */
        public function getAllowedSize() {
            return $this->allowedSize;
        }
 
		/**
		 * Set the value of allowedSize
		 *
		 * @param integer $allowedSize
		 * @return self
		 */
        public function setAllowedSize(int $allowedSize) {
            $this->allowedSize = $allowedSize;
            return $this;
        }
 
		/**
		 * Get the value of quality
		 *
		 * @return integer
		 */
        public function getQuality() {
            return $this->quality;
        }

		/**
		 * Set the value of quality
		 *
		 * @param integer $quality
		 * @return self
		 */
        public function setQuality(int $quality) {
            $this->quality = $quality;
            return $this;
        }
    }
?>