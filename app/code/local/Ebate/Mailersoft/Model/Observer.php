<?php

class Ebate_Mailersoft_Model_Observer {

    public function updateSubscriber(Varien_Event_Observer $observer) 
    {
        try {
            if($observer->getEvent()->getCustomer()->getConfirmation()) return;
            $customerId = $observer->getEvent()->getCustomer()->getId();            
            $collection = Mage::getModel('customer/customer')->getCollection();            
            $collection->getSelect()->where('entity_id = ?', $customerId)->joinLeft(array('affiliatecode' => 'affiliatecode'), 'e.affiliate_code_id = affiliatecode.id', array('code' => 'affiliatecode.code'));                        
            foreach($collection as $customer){
               $store = Mage::app()->getStore($customer->getStoreId());
               $userData = array('validate' =>$customer->getConfirmation() ,'id' => $customer->getId(), 'name' => $customer->getFirstname(), 'email' => $customer->getEmail(), 'city' => $store->getCode(), 'affiliate' => $customer->getCode(), 'city_id' => $customer->getStoreId());
            }
            $mailer = Mage::getModel('mailer/mailersoft');
            $res = $mailer->observeSubscriber($userData);           
            Mage::log($res);
        } catch (Exception $e) {
            Mage::logException($e);
        }        
    }

}

?>
