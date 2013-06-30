<?php

class Ebate_Parser_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action {
    
    protected $dir;

    public function __construct() {
        parent::__construct();
        
        $this->_dir = Mage::getBaseDir('var') . DS . 'partners_offers';
    }
    
    public function indexAction() { 
	
        $xml = new Zend_Config_Xml($data);
        $res = $xml->toArray();
    }
    
}