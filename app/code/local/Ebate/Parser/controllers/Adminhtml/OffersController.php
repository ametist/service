<?php

class Ebate_Parser_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action {
    

    public function indexAction() {
        $this->loadLayout();
        $this->renderLayout();
    }

    //'http://www.superdeal.com.ua/output_xml/skidochnik.xml';
    
    public function parseAction() { 
	    
        $data = $this->_request->getParam('parse_url'); 
        $xml = new Zend_Config_Xml($data);
        $offers = $xml->toArray();
        $url = $offers['operator']['url'];

        foreach ($offers['offers']['offer'] as $offer) {
            $this->createOffer($offer, $url);
        }
    }
    
    public function createOffer($data, $url)
    {
        $newproduct = new Mage_Catalog_Model_Product();

        $newproduct->setTypeId('simple');
        $newproduct->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); 
        $newproduct->setStatus(2); // disabled
        #$newproduct->setSKU($SKU);
        $newproduct->setTaxClassId(0);
        $newproduct->setWebsiteIDs(array(0)); 
        $newproduct->setStoreIDs(array(1)); 
        $newproduct->setStockData(array( 
            'is_in_stock' => 1, 
            'qty' => 100000,
            'manage_stock' => 0
        )); 

        $newproduct->setAttributeSetId(9);
        $newproduct->setName($data['name']);
        $newproduct->setCategoryIds(array(3)); // !!! CATEGORIES

        $newproduct->setShortDescription($data['name']);
        $newproduct->setDescription($data['description']);
        $newproduct->setConditions($data['conditions']);
        $newproduct->setPrice($data['price']);
        $newproduct->setCashback($data['cashback']);
        $newproduct->setRealUrl($url);

        $this->addImage($newproduct, $data['picture']);

        try {
            if (is_array($errors = $newproduct->validate())) {
                $strErrors = array();
                foreach($errors as $code=>$error) {
                    $strErrors[] = ($error === true)? Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code) : $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            $newproduct->save();

            echo 'Product saved successfull';
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }
  }

  public function addImage($product, $productPath)
  {
    $images = array($productPath);
    for($j=0;$j<count($images);$j++){
        $image_url = $images[$j]; //get external image url from csv
                
        $image_url = str_replace("https://", "http://", $image_url); // repalce https tp http
        
        $image_type = substr(strrchr($image_url,"."),1); //find the image extension
        $filename   = $sku.$j.'.'.$image_type; //give a new name, you can modify as per your requirement
        $filepath   = Mage::getBaseDir('media') . DS . 'import'. DS . $filename; //path for temp storage folder: ./media/import/
        file_put_contents($filepath, file_get_contents(trim($image_url))); //store the image from external url to the temp storage folder
        
        $filepath_to_image=$filepath;
        $mediaAttribute = array (
                'thumbnail',
                'small_image',
                'image'
        );

        $product->addImageToMediaGallery($filepath_to_image, $mediaAttribute, true, false);
    }
  }

}