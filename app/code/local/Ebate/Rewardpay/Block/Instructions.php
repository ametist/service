<?php

class Ebate_Rewardpay_Block_Instructions extends Mage_Core_Block_Template
{
    public function getCustomerBalance()
    {
        $customerId = Mage::getSingleton('customer/session')->getId();
        $balance = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($customerId)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByCustomer()
                ->getAmount();
        return $balance;        
    }
    
    public function getAmountDelta()
    {
        return Mage::getSingleton('core/session')->getRewardpayDelta();
    }
    
    public function showBonusBlock()
    {
	if (Mage::getSingleton('customer/session')->isLoggedIn()) {
	    return true;
	}
    }
}