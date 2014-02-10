<?php

class ImageHelper extends FileHelper
{
	// check if file type is gif/jpeg/png
	public function checkType($type = null)
	{
		if (is_null($type)) {
			if (is_null($this->theType)) {
				$this->error = 'no file type defined.  File is probably too large';
				return false;
			} else {
				$type = $this->theType;
			}
		}
		
		// check mime type		
		$okTypes  = array(
			'image/pjpeg'   => 'jpg', 
			'image/jpeg'    => 'jpg',
			'image/jpeg'    => 'jpeg',
			'image/gif'     => 'gif',
			'image/JPG'     => 'jpg',
			'image/GIF'     => 'gif',
			'image/png'     => 'png',
			'image/PNG'     => 'png'
		);
		
		if (!array_key_exists($type, $okTypes)) {
			$this->error = 'file must be JPG, GIF or PNG format';
			return false;
		} else {
			return true;
		}
		
		return true;
	}
	
	// resize gif/jpeg/png image and crop if not square if not to passed dimensions
	public function resizeCrop($destWidth, $destHeight)
	{	
		if (!$this->tempName) {
			$this->error = 'no temp image';
			return false;
		}
		
		// check image size
		$srcDims = getImageSize($this->tempName);
		
		if ($srcDims[0] == 0 || $srcDims[1] == 0) {
			$this->error = 'image has zero height or width';
			return false;
		}
		
		// check proportions
		$srcProp  = $srcDims[0] / $srcDims[1];
		$destProp = $destWidth / $destHeight;
		
		// crop? this will only work for a square image!
		if ($srcProp != $destProp) {
			if ($srcDims[0] < $srcDims[1]) {
				$srcDims[1] = $srcDims[0];
			} else {
				$srcDims[0] = $srcDims[1];
			}
		}
		
		// resize image?
		if ($srcDims[0] != $destWidth || $srcDims[1] != $destHeight) {
		
			$srcImage  = null;
			$destImage = null;
			
			// create a new image from temp file
			switch ($srcDims[2]) {
				case 1: $srcImage = imagecreatefromgif($this->tempName); // GIF
						break;
				case 2: $srcImage = imageCreateFromJpeg($this->tempName); // JPEG
						break;
				case 3: $srcImage = imagecreatefrompng($this->tempName); //PNG
						break;
				default: $this->error = 'image must be JPG, GIF or PNG format';
						return false;
						break;
			}
			
			if (!$srcImage) {
				$this->error = 'failed to create new image from file';
				return false;
			}
			
			// create a new true color image
			$destImage = imageCreateTrueColor($destWidth, $destHeight);
			
			if (!$destImage) {
				$this->error = 'failed to create new true color image';
				return false;
			}
			
			// copy and resize part of an image with resampling
			if (!imageCopyResampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcDims[0], $srcDims[1])) {
				$this->error = 'failed to copy and resize the image with resampling';
				return false;
			}
			
			// output the image back to the temp file
			switch ($srcDims[2]) {
				case 1: $isGood = imageGIF($destImage, $this->tempName); // GIF
						break;
				case 2: $isGood = imageJpeg($destImage, $this->tempName, 75); // JPEG
						break;
				case 3: $isGood = imagePNG($destImage, $this->tempName); //PNG
						break;
				default: return false;
						break;
			}
			
			if (!$isGood) {
				$this->error = 'failed to output the image back to the temp file';
				return false;
			}
			
			// delete temp images
			if (!imagedestroy($srcImage) || !imagedestroy($destImage)) {
				$this->error = 'failed to detroy temporary images';
				return false;
			}
			
			return true;
		}
		
		return true;
	}
	
	// resize gif/jpeg/png image if not to passed dimensions - modifies tempName property
	public function resize($destWidth, $destHeight)
	{	
		if (!$this->tempName) {
			$this->error = 'no temp image';
			return false;
		}
		
		// check image size
		$srcDims   = getImageSize($this->tempName);
		
		// resize image?
		if ($srcDims[0] != $destWidth || $srcDims[1] != $destHeight) {
		
			$srcImage  = null;
			$destImage = null;
			
			// create a new image from temp file
			switch ($srcDims[2]) {
				case 1: $srcImage = imagecreatefromgif($this->tempName); // GIF
						break;
				case 2: $srcImage = imageCreateFromJpeg($this->tempName); // JPEG
						break;
				case 3: $srcImage = imagecreatefrompng($this->tempName); //PNG
						break;
				default: $this->error = 'image must be JPG, GIF or PNG format';
						return false;
						break;
			}
			
			if (!$srcImage) {
				$this->error = 'failed to create new image from file';
				return false;
			}
			
			// create a new true color image
			$destImage = imageCreateTrueColor($destWidth, $destHeight);
			
			if (!$destImage) {
				$this->error = 'failed to create new true color image';
				return false;
			}
			
			// copy and resize part of an image with resampling
			if (!imageCopyResampled($destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcDims[0], $srcDims[1])) {
				$this->error = 'failed to copy and resize the image with resampling';
				return false;
			}
			
			// output the image back to the temp file
			switch ($srcDims[2]) {
				case 1: $isGood = imageGIF($destImage, $this->tempName); // GIF
						break;
				case 2: $isGood = imageJpeg($destImage, $this->tempName, 75); // JPEG
						break;
				case 3: $isGood = imagePNG($destImage, $this->tempName); //PNG
						break;
				default: return false;
						break;
			}
			
			if (!$isGood) {
				$this->error = 'failed to output the image back to the temp file';
				return false;
			}
			
			// delete temp images
			if (!imagedestroy($srcImage) || !imagedestroy($destImage)) {
				$this->error = 'failed to detroy temporary images';
				return false;
			}
			
			return true;
		}
		
		return true;
	}
}