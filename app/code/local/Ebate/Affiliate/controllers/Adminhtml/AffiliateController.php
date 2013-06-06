<?php
     
    class Ebate_Affiliate_Adminhtml_AffiliateController extends Mage_Adminhtml_Controller_Action
    {
     
        protected function _initAction()
        {
            $this->loadLayout()
                ->_setActiveMenu('affiliate/items')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            return $this;
        }   
       
        public function indexAction() {
            $this->_initAction();       
            $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliate'));
            $this->renderLayout();
        }
     
        public function editAction()
        {
            $affiliateId     = $this->getRequest()->getParam('id');
            $affiliateModel  = Mage::getModel('affiliate/affiliate')->load($affiliateId);
     
            if ($affiliateModel->getId() || $affiliateId == 0) {
     
                Mage::register('affiliate_data', $affiliateModel);
     
                $this->loadLayout();
                $this->_setActiveMenu('affiliate/items');
               
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
               
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
               
                $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit'))
                     ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit_tabs'));
                   
                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist'));
                $this->_redirect('*/*/');
            }
        }
       
        public function newAction()
        {
            $affiliateId     = $this->getRequest()->getParam('id');
            $affiliateModel  = Mage::getModel('affiliate/affiliate')->load($affiliateId);
     
            if ($affiliateModel->getId() || $affiliateId == 0) {
     
                Mage::register('affiliate_data', $affiliateModel);
     
                $this->loadLayout();
                $this->_setActiveMenu('affiliate/items');
               
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));
               
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
               
                $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit'))
                     ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit_tabsnew'));
                   
                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Item does not exist'));
                $this->_redirect('*/*/');
            }
        }
       
        public function saveAction()
        {
            if ( $this->getRequest()->getPost() ) {
                try {
                    $postData = $this->getRequest()->getPost();
                    $affiliateModel = Mage::getModel('affiliate/affiliate');

		    //check new or edit mode
                    if(!empty($postData['created_time'])) {
                        $affiliateModel->setId($this->getRequest()->getParam('id'))
                            ->setTitle($postData['title'])
                            ->setContent($postData['content'])
                            ->setStatus($postData['status'])	
                            ->setUpdate_time(date('Y-m-d H:i:s')  )
                            ->save();
                    } else {
                        $affiliateModel->setId($this->getRequest()->getParam('id'))
                            ->setTitle($postData['title'])
                            ->setContent($postData['content'])
                            ->setStatus($postData['status'])	
                            ->setCreated_time(date('Y-m-d H:i:s'))
                            ->setUpdate_time(date('Y-m-d H:i:s'))
                            ->save();
                    }
		    
		    // custom sql for update partner account
		     if(isset($postData['partner_account'])) {
		       $write = Mage::getSingleton('core/resource')->getConnection('core_write');
		       $write->query("UPDATE affiliate SET partner_account = '".$postData['partner_account']."' WHERE affiliate_id = '".$affiliateModel->getId()."'");
		     }
		    
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setAffiliateData(false);
     
                    $this->_redirect('*/*/');
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setAffiliateData($this->getRequest()->getPost());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
                }
            }
            $this->_redirect('*/*/');
        }
       
        public function deleteAction()
        {
            if( $this->getRequest()->getParam('id') > 0 ) {
                try {
                    $affiliateModel = Mage::getModel('affiliate/affiliate');
                   
                    $affiliateModel->setId($this->getRequest()->getParam('id'))
                        ->delete();
                       
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                    $this->_redirect('*/*/');
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                }
            }
            $this->_redirect('*/*/');
        }

        public function deletecodeAction()
        {
            die("DELETE Action");
        }
        public function gridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                   $this->getLayout()->createBlock('importedit/adminhtml_affiliate_grid')->toHtml()
            );
        }
    }