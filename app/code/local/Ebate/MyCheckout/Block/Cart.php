<?php
 
class Ebate_MYCheckout_Block_Cart extends Mage_Checkout_Block_Cart
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * get billing phone
     * @param int $defaultBilling
     * @return string 
     */
    public function getBillingPhone()
    {
        $customer = $this->getQuote()->getCustomer();
        $counter = 0;
        foreach ($customer->getAddresses() as $address) :
            if ($counter < 1) {
              $addr = $address->toArray();
            }
            $counter++;
        endforeach;
        if (!empty($addr['telephone'])) {
            return $addr['telephone'];
        }
    }
    
    /**
     * alias => check conditions
     * @param int $defaultBilling
     * @return string 
     */
    public function getBillingSuffix()
    {
        $customer = $this->getQuote()->getCustomer();
        $counter = 0;
        foreach ($customer->getAddresses() as $address) :
            //$addr = $address->toArray();
            if (!empty($address['suffix'])) {
                return $address['suffix'];
            }
        endforeach;
    }
    
   /**
    * check for isset customer data
    * @return boolean 
    */ 
   public function issetCustomerData()
   {
       $firstname = $this->getQuote()->getCustomer()->getFirstname();
       $phone = $this->getBillingPhone();
       $conditions = $this->getBillingSuffix();
       if(!empty($firstname) && $conditions == "true") {
           return true;
       }
       
       return false;
   }
   
   public function prepareStreetInfo($full_addr, $type)
   {
      $addr = explode(',', $full_addr);
      switch($type) {
          case 'street': $data = trim($addr[0]); break;
          case 'house': $data = trim($addr[1]); break;
          case 'flat': $data = trim($addr[2]); break;
      }
      
      return $data;
   }
}
