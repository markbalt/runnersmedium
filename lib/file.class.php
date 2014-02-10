<?php

class FileHelper extends Component
{
	protected $tempName   = null;
	protected $uploadName = null;
	protected $fileName   = null;
	protected $theType  = null;
	
	function __construct()
	{
		
	}
	
	// set temporary name property
	public function setTempName($name = null)
	{
		if ($name) {
			$this->tempName = $name;
		} else {
			return false;
		}
	}

	// set user's provided file name
	public function setUploadName($name = null)
	{
		if ($name) {
			$this->uploadName = $name;
		} else {
			return false;
		}
	}
	
	// set file type
	public function setFileType($type = null)
	{
		if ($type) {
			$this->theType = $type;
		} else {
			return false;
		}
	}

	// get filename
	public function get()
	{
		if ($this->fileName) {
			return $this->fileName;
		} else { 
			return false;
		}
	}
	
	// rename and move file to dest
	public function moveFile($dest)
	{
		// get new image name
		if ($this->uploadName) {
			$this->fileName = $this->getFilename($this->uploadName, $dest);
		} else {
			$this->error = 'no upload filename';
			return false;
		}
		
		// make sure we have the image name
		if (!$this->fileName) {
			$this->error = 'no file name';
			return false;
		}
		
		// make sure we have the temp image name
		if (!$this->tempName) {
			$this->error = 'no temp image';
			return false;
		}
		
		// set the path
		$path = $dest.$this->fileName;
		
		// move the uploaded file to the new location
		if (!move_uploaded_file($this->tempName, $path)) {
			$this->error = 'error moving uploaded image to '.$path;
		} else {
			return $this->fileName;
		}
		echo filesize($dest.$this->fileName);
		return true;
	}
	
	// remove/unlink file
	public function removeFile($file)
	{
		if (is_file($file)) {
			unlink($file);
			return true;
		} else {
			return false;
		}
	}
	
	// return a filename that is not taken, appending count if neccessary
	private function getFilename($filename, $dest)
	{
		static $count = 0;
		
		$new = str_replace(" ", "_", $filename);
		$new = substr($new, 0, strrpos($new, '.')).$count++.substr($new, strrpos($new, '.'));
		
	    if (is_file($dest.$new)) {
	        $new = $this->getFilename($filename, $dest);
	    }
	    
		return $new;
	}
}

?>