<?php

/****************************************
Example of how to use this uploader class...
You can uncomment the following lines (minus the require) to use these as your defaults.

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 10 * 1000 * 1000;
//the input name set in the javascript
$inputName = 'qqfile'

require('fileuploader.php');
$uploader = new FileUploader($allowedExtensions, $sizeLimit, $inputName);

// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
$result = $uploader->handleUpload('uploads/');

// to pass data through iframe you will need to encode all html tags
header("Content-Type: text/plain");
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);

/******************************************/



/**
 * Handle file uploads via XMLHttpRequest
 */
class UploadedFileXhr {
    private $inputName;
    
    /**
     * @param string $inputName; defaults to the javascript default: 'qqfile'
     */
    public function __construct($inputName = 'qqfile'){
        $this->inputName = $inputName;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {    
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
        if ($realSize != $this->getSize()){            
            return false;
        }
        
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
    
    /**
     * Get the original filename
     * @return string filename
     */
    public function getName() {
        return $_GET[$this->inputName];
    }
    
    /**
     * Get the file size
     * @return integer file-size in byte
     */
    public function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class UploadedFileForm {
    private $inputName;

    /**
     * @param string $inputName; defaults to the javascript default: 'qqfile'
     */
    public function __construct($inputName = 'qqfile'){
        $this->inputName = $inputName;
    }
    
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    public function save($path) {
        return move_uploaded_file($_FILES[$this->inputName]['tmp_name'], $path);
    }
    
    /**
     * Get the original filename
     * @return string filename
     */
    public function getName() {
        return $_FILES[$this->inputName]['name'];
    }
    
    /**
     * Get the file size
     * @return integer file-size in byte
     */
    public function getSize() {
        return $_FILES[$this->inputName]['size'];
    }
}

/**
 * Class that encapsulates the file-upload internals
 */
class FileUploader {
    private $allowedExtensions;
    private $sizeLimit;
    private $file;
    private $uploadName;
    private $additionalCheck;

    /**
     * @param array $allowedExtensions; defaults to an empty array
     * @param int $sizeLimit; defaults to the server's upload_max_filesize setting
     * @param string $inputName; defaults to the javascript default: 'qqfile'
     */
    function __construct(array $allowedExtensions = null, $sizeLimit = null, $inputName = 'qqfile', $additionalCheck = null){
        if($allowedExtensions===null) {
            $allowedExtensions = array();
        }
    	if($sizeLimit===null) {
    	    $sizeLimit = $this->toBytes(ini_get('upload_max_filesize'));
    	}
    	        
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
            
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;
        $this->additionalCheck = $additionalCheck;
        
        $this->checkServerSettings();       

        if(!isset($_SERVER['CONTENT_TYPE'])) {
            $this->file = false;	
        } else if (strpos(strtolower($_SERVER['CONTENT_TYPE']), 'multipart/') === 0) {
            $this->file = new UploadedFileForm($inputName);
        } else {
            $this->file = new UploadedFileXhr($inputName);
        }
    }
    
    /**
     * Get the name of the uploaded file
     * @return string
     */
    public function getUploadName(){
        if( isset( $this->uploadName ) )
            return $this->uploadName;
    }

    /**
     * Get the original filename
     * @return string filename
     */
    public function getName(){
        if ($this->file)
            return $this->file->getName();
    }
    
    /**
     * Internal function that checks if server's may sizes match the
     * object's maximum size for uploads
     */
    private function checkServerSettings(){        
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1000 / 1000) . 'M';             
            die(json_encode(array('error'=>'請增加 post_max_size 和 upload_max_filesize 到 ' . $size)));    
        }        
    }
    
    /**
     * Convert a given size with units to bytes
     * @param string $str
     */
    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1000;
            case 'm': $val *= 1000;
            case 'k': $val *= 1000;        
        }
        return $val;
    }
    
    /**
     * Handle the uploaded file
     * @param string $uploadDirectory
     * @param string $replaceOldFile=true
     * @returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
        if (!is_writable($uploadDirectory)){
            return array('error' => "伺服器錯誤！上傳資料夾不可寫入。");
        }
        
        if (!$this->file){
            return array('error' => '沒有檔案。');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => '檔案是空的。');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => '檔案大小超出限制！');
        }
        
        if($this->additionalCheck){
        	try {
        		call_user_func($this->additionalCheck,$this);
        	}
        	catch (Exception $e){
        		return array('error' => $e->getMessage());
        	}
        }
        
        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = @$pathinfo['extension'];		// hide notices if extension is empty

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => '檔案格式錯誤。只支援以下格式：'. $these . '。');
        }
        
        $ext = ($ext == '') ? $ext : '.' . $ext;
        
        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . DIRECTORY_SEPARATOR . $filename . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        $this->uploadName = $filename . $ext;

        if ($this->file->save($uploadDirectory . DIRECTORY_SEPARATOR . $filename . $ext)){
            return array('success'=>true);
        } else {
            return array('error'=> '上載失敗！' .
                '上傳而被取消，或是伺服器錯誤。');
        }
        
    }    
}