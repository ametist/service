<?php

 class Ebate_Feedback_Helper_Data extends Mage_Core_Helper_Abstract {
    
     // email config
     protected $_ssl = 'tls';
     protected $_port = '587';
     protected $_auth = 'login';
     protected $_username = 'noreply@mycash.in.ua';
     protected $_password = 'CELoYGTOwza';
     protected $_from = 'noreply@mycash.in.ua';

     
     /**
      * upload images
      *
      * @param array $files
      * @param string $fname
      * @param string $path
      * @param string $iName
      * @param int $width
      * @param int $height 
      */
    public function uploadImages($files, $fname, $path, $iName, $width, $height) {
	
	if (isset($files[$fname]['name']) and (file_exists($files[$fname]['tmp_name']))) {
	    
	    try {
		
		$uploader = new Varien_File_Uploader($fname);
		
		$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png')); 

		$uploader->setAllowRenameFiles(true);

		$uploader->setFilesDispersion(false);
		
		unlink($path . DS . $iName);
		
		$uploader->save($path, $iName);
 
		//$resizePath = $this->resizeImage($iName, $width, $height, $path);
		
		$data[$fname] = $files[$fname]['name'];
	    
	    } catch (Exception $e) {
		
		Mage::logException($e);
		
	    }
	} 
    }
    
    /**
     * Method for clear dir
     * @param string $dir 
     */
    public function clearDir($dir)
    {
        if ($objs = glob($dir . "/*")) {
            foreach ($objs as $obj) {
                is_dir($obj) ? $this->clearDir($obj) : unlink($obj);
            }
        }
    }
    
    /**
     * Resize Image proportionally and return the resized image url
     *
     * @param string $imageName         name of the image file
     * @param integer|null $width       resize width
     * @param integer|null $height      resize height
     * @param string|null $imagePath    directory path of the image present inside media directory
     * @return string                   full url path of the image
     */
    public function resizeImage($imageName, $width = NULL, $height = NULL, $imagePath = NULL) 
    {
	$imagePath = str_replace("/", DS, $imagePath);
	$imagePathFull = $imagePath . DS . $imageName;
	
	if ($width == NULL && $height == NULL) {
	    $width = 600;
	    $height = 360;
	}
	
	if (file_exists($imagePathFull)) {
	    
	    $imageObj = new Varien_Image($imagePathFull);
	    $imageObj->backgroundColor(array(255, 255, 255));
	    $imageObj->constrainOnly(TRUE);
	    $imageObj->keepAspectRatio(TRUE);
	    $imageObj->keepFrame(FALSE);
            $imageObj->quality(100);
	    $imageObj->resize($width, $height);
	    $imageObj->save($imagePathFull);
	}

    }
    
    /**
     * method for trim text if text > 100 symbols
     * 
     * @param type $text
     * @param type $cSymb
     * @return type 
     */
    public function trimText($text, $cSymb = 100, $dot = true) {

	if (mb_strlen($text, "UTF-8") > $cSymb) {
	    
	    $newText = $this->myutf8_substr2($text, 0, $cSymb - 1);
            
            if($dot == true) $newText = $newText . '...';
            
	    return $newText;
	    
	} else {
	    
	    return $text;
	    
	}
    }
    
    /**
     * utf substr
     * 
     * @param type $str
     * @param type $from
     * @param type $len
     * @return type 
     */
    public function myutf8_substr2($str, $from, $len) {

	return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
			'((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
    }

    /**
    * Send mail
    * 
    * @param string $to
    * @param string $body
    * @param string $type 
    */ 
   public function sendLetter($to, $body, $type = 'text', $subject = 'РџРѕРґС‚РІРµСЂР¶РґРµРЅРёРµ СЂРµРіРёСЃС‚СЂР°С†РёРё РЅР° SuperDeal.ua', $attachment = null, $id = null, $fromMail = 'noreply@superdeal.com.ua', $from = 'SuperDeal') {

	#$config = array('ssl' => $this->_ssl, 'port' => $this->_port, 'auth' => $this->_auth, 'username' => $this->_username, 'password' => $this->_password);

	#$transport = new Zend_Mail_Transport_Smtp('smtp.superdeal.com.ua', $config);

	$mail = new Zend_Mail('utf-8');

	if (strtolower($type == 'html')) {
	    $mail->setBodyHtml($body);
	} else {
	    $mail->setBodyText($body);
	}

	$mail
		->setBodyText($body)
		->setFrom($fromMail, $from)
		->addTo($to)
		->setSubject($subject);
                                if($attachment && $id){
                                    $mail->createAttachment(file_get_contents($attachment),
                                            'application/octet-stream',
                                            Zend_Mime::DISPOSITION_INLINE,
                                            Zend_Mime::ENCODING_BASE64, "superdeal-invoice-{$id}.pdf");
                                }

	try {
	    
	    $mail->send();
	    
	} catch (Exception $e) {
	    
	    Mage::logException($e);
	    
	}
    }
    
    /**
     * google optimazer code 
     * 
     * @return string 
     */
    public function goptimazerCode() {
        $landingID = Mage::app()->getRequest()->getParam('id');
        if ($landingID) {
            $landing = Mage::getModel('landing/landing')->load($landingID);
            if ($landing) {
                return $landing->getData('goptimazer');
            }
        }
    }

}

