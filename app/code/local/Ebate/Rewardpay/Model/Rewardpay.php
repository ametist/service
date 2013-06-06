<?php

class Ebate_Rewardpay_Model_Rewardpay extends Mage_Payment_Model_Method_Abstract
{
    
    protected $_code = 'rewardpay';
 
    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     *
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */
     
    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = false;
 
    /**
     * Can authorize online?
     */
    protected $_canAuthorize            = false;
 
    /**
     * Can capture funds online?
     */
    protected $_canCapture              = false;
 
    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;
 
    /**
     * Can refund online?
     */
    protected $_canRefund               = false;
 
    /**
     * Can void transactions online?
     */
    protected $_canVoid                 = false;
 
    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;
 
    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;
 
    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = false;
 
    /**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;
 
    /**
     * Here you will need to implement authorize, capture and void public methods
     *
     * @see examples of transaction specific public methods such as
     * authorize, capture and void in Mage_Paygate_Model_Authorizenet
     */
    
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('rewardpay/payment/process/', array('_secure' => true));
    }
    
    public function isAvailable($quote = null) {
        parent::isAvailable($quote);        
        $websiteId = Mage::app()->getStore()->getWebsiteId();
        $customer_id = $quote->getCustomerId();
        if(!$customer_id) return true;
        $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($customer_id)
                ->setWebsiteId($websiteId)
                ->loadByCustomer()
                ->getPointsBalance();
         if($quote->hasProductId(362) || $quote->hasProductId(5323)){
            return false;
        }elseif($reward < $quote->getGrandTotal()){
            return false;
        }    
        return true;
    }

}
?>
