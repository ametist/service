<?php

class SuperDeal_Parser_Adminhtml_OffersController extends Mage_Adminhtml_Controller_Action {
    
    public function indexAction() { 
	
        $xml = new Zend_Config_Xml($data);
        $res = $xml->toArray();
    }
    
}