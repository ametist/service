<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';

class Ebate_MYCheckout_Checkout_CartController extends Mage_Checkout_CartController {

    public function preDispatch() {
	parent::preDispatch();
	$cart = Mage::getSingleton('checkout/cart');
	$quote = $cart->getQuote();
	if (count($quote->getAllItems()) == 0) {
	    $this->_redirect('/');
	}
    }

    /**
     * Overloaded indexAction 
     */
    public function indexAction() {
	parent::indexAction();
    }

}
