<?php
require_once Mage::getModuleDir('Model', 'Mage_Checkout') . DS . 'Model' . DS . 'Type' . DS . 'Onepage.php';

class Ebate_MYCheckout_Model_Onepage extends Mage_Checkout_Model_Type_Onepage
{
  
    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        $service = Mage::getModel('sales/service_quote', $this->getQuote());
        $service->submitAll();

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData();

        $order = $service->getOrder();
        if ($order) {
            Mage::dispatchEvent('checkout_type_onepage_save_order_after', array('order'=>$order, 'quote'=>$this->getQuote()));

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());

            // as well a billing agreement can be created
            $agreement = $order->getPayment()->getBillingAgreement();
            if ($agreement) {
                $this->_checkoutSession->setLastBillingAgreementId($agreement->getId());
            }
        }

        // add recurring profiles information to the session
        $profiles = $service->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = array();
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        #if (!Mage::helper('catalog/product')->checkIfWare($this->getQuote(), 'quote')) {
            Mage::dispatchEvent(
                    'checkout_submit_all_after', array('order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles)
            );
        #}
        
        // save partner location
        $orderId = $order->getId();
        $partnerLocation = Mage::getSingleton('core/app')->getRequest()->getParam('partner_location');
        if (!empty($orderId) && !empty($partnerLocation)) {
            $location = Mage::getSingleton('core/resource')->getConnection('core_write');
            $location->query("UPDATE sales_flat_order SET partner_location = '" . $partnerLocation . "' WHERE entity_id = '" . $orderId . "'");
        }

        // assign affiliate
        if (!empty($orderId)) {
            $this->assignAffiliate($orderId);
        }
        
        return $this;
    } 
    
    
   /**
    * assign affiliate to order
    * 
    * @param int $orderId 
    */ 
   public function assignAffiliate($orderId) 
   {
       try{
            if (Mage::getSingleton('core/cookie')->get('eca')) {
                $affiliate = Mage::getModel('core/cookie')->get('eca');
            } else {
                $affiliate = Mage::getSingleton('core/session')->getData('affiliate');
            }
        } catch(Exception $e){
            Mage::logException($e);
        }
        
        if (!empty($affiliate)) {
            $collection = Mage::getModel('affiliatecode/affiliatecode')->getCollection();
            $collection->getSelect()
                       ->where('code = ?', $affiliate);

            $affiliateId = $collection->getFirstItem()->getData('id');
            
            if (!empty($affiliateId)) {
                $resource = Mage::getSingleton('core/resource')->getConnection('core_write');
                $affiliateId = $resource->quote($affiliateId);
                $resource->query("UPDATE sales_flat_order SET affiliate_id = {$affiliateId} WHERE entity_id = {$orderId}");
            }
        }
    }
    
    /**
     * get merchant value
     * 
     * @param int $attr_code
     * @return int 
     */
    public function getMerchantValue($attr_code)
    {
        $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter('merchant')->getFirstItem();
        $attributeOptions = $attributeInfo->getSource()->getAllOptions(false);
        
        $mAttribute = '';
        foreach($attributeOptions as $value) {
            if ($value['value'] == $attr_code) {
                $mAttribute = $value['label'];
            }
        }
        
        return $mAttribute;
    }
    
    /**
     * get all shipping methods
     * @return type 
     */
    public function getAllShippingMethods() 
    {
        $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
        $options = array();

        foreach ($methods as $_ccode => $_carrier) {
           
            $_methodOptions = array();
            if ($_methods = $_carrier->getAllowedMethods()) {
                $_code = $_ccode . '_' . $_ccode;

                if (!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
                    $_title = $_ccode;

                $options[] = array('value' => $_code, 'label' => $_title);
            }
        }

        return $options;
    }
   
}