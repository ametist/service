<?php


class Ebate_Affiliate_Model_Observer
{
    /**
     * add affiliate to customer
     * 
     * @param type $observer 
     */
    public function addAffiliateToCustomer($observer) 
    {
        $customer = $observer->getCustomer();
        $customerData = $customer->getData();

        // check if isset affiliate in this customer
        $checkIsSetAffiliate = $this->checkIsSetAffiliate($customer->getId());

        if (empty($checkIsSetAffiliate)) {

            $affiliate = Mage::getSingleton('core/session')->getData('affiliate');

            if (!empty($affiliate)) {
                $aff = $this->getAffId($affiliate);

                if (!empty($aff['id'])) {

                    // assign affiliate code to customer
                    $write = Mage::getSingleton('core/resource')->getConnection('core_write');
                    $write->query("UPDATE customer_entity SET affiliate_code_id = '" . $aff['id'] . "' WHERE entity_id = '" . $customer->getId() . "'");

                    // add balance to customer
                    $this->addCustomerBalance($customer->getId(), $aff['amount'], $aff['description']);
                }
            }
        } 
    }
    
    
   /**
    *
    * check for isset affiliate
    * @param type $customerId
    * @return type 
    */ 
   public function checkIsSetAffiliate($customerId)
   {
      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $query = "SELECT affiliate_code_id FROM customer_entity WHERE entity_id = '".$customerId."'";
      $results = $readConnection->fetchAll($query);
      
      return $results[0]['affiliate_code_id'];
   }
    
   /**
    * get affiliate id
    * 
    * @return array 
    */ 
   public function getAffId($affiliate) 
   {
	$collection = Mage::getModel('affiliate/affiliatecode')->getCollection();
	$collection->getSelect()
		   ->where('code = ?', $affiliate);
	
	$affiliate = array();
	foreach ($collection as $item) {
	    $affiliate['id'] = $item->getId(); 
	    $affiliate['amount'] = $item->getAmountAdd();
	    $affiliate['description'] = $item->getDescription();
	}

	return $affiliate;
    }
   
   /**
    * add customer balance
    * 
    * @param type $customer 
    */ 
   public function addCustomerBalance($customer, $amount, $description)
   {
       $store = Mage::app()->getStore();
       
       $model = Mage::getModel('enterprise_customerbalance/balance');
       $model->setCustomerId($customer)
	     ->setWebsiteId($store->getWebsiteId())  
	     ->save();
      
      // set amount 
      $model = Mage::getModel('enterprise_customerbalance/balance')->load($model->getId());
      $model
	    ->setAmountDelta((float) $amount)  
	    ->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_UPDATED)
            ->setUpdatedActionAdditionalInfo('От аффилейта.' .$description)  
            ->save(); 
      
   }
 
}