<?php

class Ebate_Free_PaymentController extends Mage_Core_Controller_Front_Action
{
    
    public function processAction()
    {        
        try{
            $reorderId = Mage::helper('free')->getReorderId();
            if (!empty($reorderId))
                $lastOrderId = $reorderId;
            else
                $lastOrderId = Mage::getSingleton('checkout/session')->getLastOrderId();
            $freeModel = Mage::getModel('free/free');
            $freeModel->createInvoice($lastOrderId);
            $this->_redirect('*/*/success/', array('_secure'=>true));
        }catch(Exception $e){
            #die($e->getMessage());
            $this->_redirect('*/*/error/', array('_secure'=>true));
        }        
    }
    
    public function errorAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function successAction()
    {
        #$path = Mage::getBaseDir('var') . DS .'invoices' . DS;
        #$reorderId = Mage::helper('alfacredit')->getReorderId();
        
        #if (!empty($reorderId)) {
        #  $invoiceId = Mage::getSingleton('core/session')->getInvoiceId() ? Mage::getSingleton('core/session')->getInvoiceId(): $reorderId;
        #} else {
        #  $invoiceId = Mage::getSingleton('core/session')->getInvoiceId() ? Mage::getSingleton('core/session')->getInvoiceId(): Mage::getSingleton('checkout/session')->getLastOrderId();
        #}
        
        #if(file_exists($path . $invoiceId . '.pdf')) {
           #$email = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        #if($email){            
               
            /*$body = "Мы благодарим вас за заказ.

В приложении вы можете найти счет на оплату!

Для завершения заказа вам необходимо совершить оплату в любом банке, если вы еще не сделали этого. Напоминаем, что при оплате в Приватбанке комиссия банка отсутствует.
Как только мы получим оплату, ваш SuperDeal  купон будет доставлен на ваш е-меил, а также появится в вашем профиле на сайте.

Попасть в ваш профиль можно нажав кнопку “профиль” в верхнем правом углу сайта. 

С наилучшими пожеланиями,
Команда портала SuperDeal

--------------------------------------
Команда SuperDeal,
support@superdeal.com.ua
Тел.: 044 5922228, 050 1502844
Skype: superdeal_support
ICQ: 611-788-655
                ";*/
            
            #$body = Mage::getModel('core/email_template')
            #      ->loadDefault('customer_order_email_template')
            #      ->getProcessedTemplate(array('url' => Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB)));
            
            #Mage::helper('landing')->sendLetter($email, $body, 'html', 'Уведомление о заказе!', $path . $invoiceId . '.pdf', $invoiceId);
        #}
        
        #}
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function createAction()
    {
        $model = Mage::getModel('free/free');
        $model->createInvoice();
    }
}
