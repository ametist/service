<?php

class Ebate_Rewardpay_Model_Observer 
{
    //    public function processOrderPlace(Varien_Event_Observer $observer)
//    {
//        if (!Mage::helper('enterprise_customerbalance')->isEnabled()) {
//            return;
//        }
//
//        $order = $observer->getEvent()->getOrder();
//        if ($order->getBaseCustomerBalanceAmount() > 0) {
//            $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
//            $balance = Mage::getModel('enterprise_customerbalance/balance')
//                ->setCustomerId($order->getCustomerId())
//                ->setWebsiteId($websiteId)
//                ->setAmountDelta(-$order->getBaseCustomerBalanceAmount())
//                ->setHistoryAction(Enterprise_CustomerBalance_Model_Balance_History::ACTION_USED)
//                ->setOrder($order)
//                ->save();
//        }
//    }
    
    public function processOrderPlace(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('enterprise_reward')->isEnabled() || 'rewardpay' != $observer->getEvent()->getOrder()->getPayment()->getMethodInstance()->getCode()) {
            return;
        }

        $order = $observer->getEvent()->getOrder();
        $amount = Mage::app()->getStore()->roundPrice($order->getGrandTotal());
        $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();
        $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->loadByCustomer()
                ->getPointsBalance();
        $delta = $reward - $amount;
        if ($delta >= 0) {                        
             $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId(Mage::app()->getStore($order->getStoreId())->getWebsiteId())
                ->setPointsDelta(-$amount)
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ORDER)
                ->setActionEntity($order)
                ->updateRewardPoints();
            if (!$order->canInvoice()) {
                $order->addStatusToHistory(
                        $order->getStatus(), Mage::helper('osmp')->__('Error during creation of invoice.', true), $notified = true
                );
            } else {
                // Need to save transaction id
                $order->getPayment()->setTransactionId($reward->getId());                
                // Need to convert from order into invoice
                $invoice = $order->prepareInvoice();
                $invoice->register()->pay();
                Mage::getModel('core/resource_transaction')
                        ->addObject($invoice)
                        ->addObject($invoice->getOrder())
                        ->save();
                $order->addStatusToHistory(
                        $order->getStatus(), Mage::helper('bonuspay')->__('Order paid by reward points.'), $notified = true
                );                
            }  
           Mage::getSingleton('core/session')->setRewardpayStatus("success");
           Mage::getSingleton('core/session')->setRewardpayStatusText(Mage::helper('rewardpay')->__('Order complete'));
           Mage::getSingleton('core/session')->setRewardpayDelta($amount);
        }else{
           Mage::getSingleton('core/session')->setRewardpayStatus("error");
           Mage::getSingleton('core/session')->setRewardpayStatusText(Mage::helper('rewardpay')->__('Not enough Reward Points to complete this Order.'));
        }
    }
    
    public function processOrderPlaceBefore(Varien_Event_Observer $observer)
    {
        #die(var_export($observer));
         if (!Mage::helper('enterprise_reward')->isEnabled() || 'rewardpay' != $observer->getEvent()->getOrder()->getPayment()->getMethodInstance()->getCode()) {
            return;
        }
        $order = $observer->getEvent()->getOrder();
        $websiteId = Mage::app()->getStore($order->getStoreId())->getWebsiteId();        
        $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomerId($order->getCustomerId())
                ->setWebsiteId($websiteId)
                ->loadByCustomer()
                ->getPointsBalance();
        $amount = Mage::app()->getStore()->roundPrice($order->getGrandTotal());
        $delta = $reward - $amount;
        if ($reward <=  0 || $delta < 0) {            
            Mage::throwException(Mage::helper('rewardpay')->__('Not enough Reward Points to complete this Order.'));
        }        
    }    
}
?>
