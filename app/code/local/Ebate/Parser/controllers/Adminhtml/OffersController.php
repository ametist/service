<?php

class Ebate_Parser_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action {
    
    protected $dir;
    
    public function indexAction() { 
	    
        $data = 'http://www.superdeal.com.ua/output_xml/skidochnik.xml';
        $xml = new Zend_Config_Xml($data);
        $offers = $xml->toArray();
        $url = $offers['operator']['url'];

        foreach ($offers['offers']['offer'] as $offer) {
            $this->createOffer($offer, $url); die();
        }
    }
    
    public function createOffer($data, $url)
    {

        $newproduct = new Mage_Catalog_Model_Product();

        $newproduct->setTypeId('simple');
        #$newproduct->setWeight($product->UnitWeight);       
        $newproduct->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH); 
        $newproduct->setStatus(0);
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
        $newproduct->setCategoryIds(array(3)); // array of categories it will relate to

        $newproduct->setShortDescription($data['name']);
        $newproduct->setDescription($data['description']);
        $newproduct->setConditions($data['conditions']);
        $newproduct->setPrice($data['price']);
        $newproduct->setData('real_url', $url);

        #$this->addImage($newproduct, $data['picture']);

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

  public function addImage($product, $putPathHere)
  {
   $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
   $product->addImageToMediaGallery ($putPathHere, array ('image', 'small_image', 'thumbnail'), false, false); 
  }

}