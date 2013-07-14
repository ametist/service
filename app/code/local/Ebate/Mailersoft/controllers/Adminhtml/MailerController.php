<?php
class Ebate_Mailersoft_Adminhtml_MailerController extends Mage_Adminhtml_Controller_Action
{
    public function settingsAction()
    {
        $this->loadLayout();
            #->_setActiveMenu('mailer/settings');
        $this->renderLayout();

    }

    public function postAction()
    {
        $post = $this->getRequest()->getPost();
        try {
            if (empty($post)) {
                Mage::throwException($this->__('Invalid form data.'));
            }

            foreach($post['mailer'] as $group => $value){
                Mage::getConfig()->saveConfig('mailer/mailer_groups/' . $group, $value);
            }

            $message = $this->__('Your form has been submitted successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirect('*/*');
    }

    public function exportAction()
    {
        $this->loadLayout();
            #->_setActiveMenu('mailer/export');
        $this->renderLayout();
    }
    
    public function reportsAction()
    {
        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template', 'mailer_reports', array('template' => 'mailer/reports.phtml')
        );

        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    public function sortAction()
    {
        $cityId = $this->getRequest()->getParam('city');
        
        if(!isset($cityId)) $cityId = 0;
        
        if($this->getRequest()->getPost('clearLog'))
        {
                Mage::getModel('mailer/mailersoft')->clearLog();
                Mage::register('clear', true);
        }
        
        if($this->getRequest()->getPost('savePosition'))
        {
            Mage::getModel('mailer/mailersoft')->savePosition($this->getRequest()->getPost('mailer'), $this->getRequest()->getPost('cityId'));
        }
        
        $this->loadLayout();
        $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template', 'mailer_sort', array('template' => 'mailer/sort.phtml')
        );  

        $this->getLayout()->getBlock('content')->append($block);
        $this->renderLayout();
    }
    
    
}