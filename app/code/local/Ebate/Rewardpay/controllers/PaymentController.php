<?php

class Ebate_Rewardpay_PaymentController extends Mage_Core_Controller_Front_Action
{
    public function processAction()
    {        
        try{            
            $redirect = Mage::getSingleton('core/session')->getRewardpayStatus();
            $text = Mage::getSingleton('core/session')->getRewardpayStatusText();
            if($redirect){
                $this->_redirect("*/*/{$redirect}", array('_secure' => true));
            }else{
                $this->_redirect('*/*/error', array('_secure' => true));
            }
        }catch(Exception $e){
            Mage::getSingleton('core/session')->setRewardpayStatusText($e->getMessage());
            $this->_redirect('*/*/error', array('_secure' => true));
        }        
    }
    
    public function errorAction()
    {
        #print Mage::getSingleton('core/session')->getBonuspayStatusText();
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function successAction()
    {
        #print Mage::getSingleton('core/session')->getBonuspayStatusText();
        $this->loadLayout();
        $this->renderLayout();
    }
    
//    public function addAction()
//    {
//        $bonus = Mage::getModel('bonuspay/bonuspay');
//        $bonus->addBonus(Mage::getSingleton('customer/session')->getCustomerId(), 133, Mage::app()->getStore()->getWebsiteId());
//    }
}
