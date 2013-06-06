<?php

require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'OnepageController.php';

class Ebate_MYCheckout_Checkout_OnepageController extends Mage_Checkout_OnepageController {

    const SALT = 'flowers';

    public function saveOrderAction() {
                
        try {
            $checkout = Mage::getSingleton('checkout/type_onepage');
            $payment = $this->getRequest()->getPost('payment');
            $payment['method'] = "sdfree";

            $cart = Mage::getSingleton('checkout/cart');
            $cartData = $this->getRequest()->getParam('cart');
                        
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter($data['qty']);
                    }
                }
                #$cart = Mage::getSingleton('checkout/cart');

                if (!$cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }

                $cartData = $cart->suggestItemsQty($cartData);
                $cart->updateItems($cartData)
                        ->save();
            }

            $checkout->savePayment(array('method' => $payment['method']));

            $billingDefault = array(
                'address_id' => '',
                'company' => '',
                'street' => array('Address part 1', 'Address part 2'),
                'postcode' => '31000',
                'country_id' => 'UA',
                'fax' => '',
                'customer_password' => '',
                'save_in_address_book' => ''
            );
            $shippingDefault = array(
                'address_id' => '',
                'company' => '',
                'street' => array('Address part 1', 'Address part 2'),
                'postcode' => '31000',
                'country_id' => 'UA',
                'fax' => '',
                'customer_password' => '',
                'save_in_address_book' => ''
            );


            $billingAddress = $this->getRequest()->getParam('billing');
            $billingAddress = array_merge($billingAddress, $billingDefault);
            $billingAddress['telephone'] = $billingAddress['telephone'] ? $billingAddress['telephone'] : '0123456789';
            $billingAddress['use_for_shipping'] = 0;
            $shippingAddress = $this->getRequest()->getParam('shipping') ? $this->getRequest()->getParam('shipping') : array();
            $shippingAddress = array_merge($shippingAddress, $shippingDefault);
            #$checkoutMethod = $this->getRequest()->getParam('checkout_method', false);
            $checkoutMethod = "sdfree";
            $checkout->saveCheckoutMethod($checkoutMethod);
            //STEP(1)

            if (!$billingAddress['firstname'] || !$billingAddress['lastname']) {
                Mage::throwException($this->__('Пожалуйста, заполните поля "Имя" и "Фамилия"'));
            } else {
                $customerId = Mage::getSingleton('customer/session')->getCustomerId();
                $customer = Mage::getModel('customer/customer')->load($customerId);
                if (!$customer->getFirstname()) {
                    $customer->setFirstname($billingAddress['firstname']);
                }
                if (!$customer->getLastname()) {
                    $customer->setLastname($billingAddress['lastname']);
                }
                $customer->save();
            }
            
            // customer billing phone
            if (!empty($billingAddress['telephone']) && empty($shippingAddress['method'])) {

                $customerId = Mage::getSingleton('customer/session')->getCustomerId();
                // customer address data	    
                $_custom_address = array(
                    'telephone' => $billingAddress['telephone'],
                    'suffix' => 'true'
                );

                // get address id
                $customAddress = Mage::getModel('customer/address')->getCollection();
                $customAddress->getSelect()
                        ->where('parent_id = ?', $customerId);

                $addrId = '';
                foreach ($customAddress as $item) {
                    $addrId = $item->getId();
                }

                if (!empty($addrId)) {
                    $_address_model = Mage::getModel('customer/address')->load($addrId);

                    $_address_model->addData($_custom_address)
                            ->save();
                } else {
                    $_address_model = Mage::getModel('customer/address');
                    $_address_model->setData($_custom_address)
                            ->setCustomerId($customerId)
                            ->save();
                }
            }
            
            // billing save
            $billing = $checkout->saveBilling($billingAddress, false);
            
            // save shipping method
            $checkout->saveShippingMethod('freeshipping_freeshipping');
               
            // save order
            $checkout->saveOrder();
        
            $storeId = Mage::app()->getStore()->getId();
            $paymentHelper = Mage::helper("payment");
            $zeroSubTotalPaymentAction = $paymentHelper->getZeroSubTotalPaymentAutomaticInvoice($storeId);
            if ($paymentHelper->isZeroSubTotal($storeId)
                    && $this->_getOrder()->getGrandTotal() == 0
                    && $zeroSubTotalPaymentAction == Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE
                    && $paymentHelper->getZeroSubTotalOrderStatus($storeId) == 'pending') {
                $invoice = $this->_initInvoice();
                $invoice->getOrder()->setIsInProcess(true);
                $invoice->save();

            }

            // redirect to partner area
            $partner_url = $this->getProductIdByOrder($this->_getOrder()->getId()); 
            $this->getResponse()->setRedirect($partner_url);
            Mage::app()->getResponse()->sendResponse();
            exit();

        } catch (Mage_Payment_Model_Info_Exception $e) {
            $message = $e->getMessage();
            Mage::logException($e);
            Mage::getSingleton('checkout/session')->addError($this->__($message));
            $this->_redirect('checkout/cart/', array('_secure' => true));
            return;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $message = $e->getMessage();
            Mage::getSingleton('checkout/session')->addError($this->__($message));
            $this->_redirect('checkout/cart/', array('_secure' => true));
            return;
        } catch (Exception $e) {
            Mage::logException($e);
            $message = $this->__('There was an error processing your order. Please contact us or try again later.');
            Mage::getSingleton('checkout/session')->addError($this->__($message));
            $this->_redirect('checkout/cart/', array('_secure' => true));
            return;
        }

        $redirectUrl = $this->getOnepage()->getCheckout()->getRedirectUrl();
        $checkout->getQuote()->save();

        
        /**
         * when there is redirect to third party, we don't want to save order yet.
         * we will save the order in return action.
         */
        if (isset($redirectUrl)) {
            $this->_redirectUrl($redirectUrl);
        }
    }

    public function getProductIdByOrder($order_id)
    {
        $order = Mage::getModel('sales/order')->load($order_id);
        $items = $order->getAllItems();
        #return array_shift($items);

        foreach ($items as $itemId => $item) {
            $product_id = $item->getProductId(); 
        }

        if ($product_id) {
            $real_url = Mage::getModel('catalog/product')->load($product_id)->getData('real_url');
            $hash = Mage::helper('core')->encrypt($order_id . self::SALT);
            return $real_url . '?oid=' . $hash;
        }
    }
}
