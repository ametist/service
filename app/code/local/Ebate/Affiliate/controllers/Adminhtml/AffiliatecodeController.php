<?php
     
    class Ebate_Affiliate_Adminhtml_AffiliatecodeController extends Mage_Adminhtml_Controller_Action
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
            $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode'));
            $this->renderLayout();
        }
     
        public function editAction()
        {
            $affiliateCodeId     = $this->getRequest()->getParam('id');
            Mage::register('affiliateId', $this->getRequest()->getParam('affiliate_id') );
            $affiliateCodeModel  = Mage::getModel('affiliate/affiliatecode')->load($affiliateCodeId);
     
            if ($affiliateCodeModel->getId() || $affiliateCodeModel == 0) {
     
                Mage::register('affiliatecode_data', $affiliateCodeModel);
     
                $this->loadLayout();
                $this->_setActiveMenu('affiliatecode/items');
               
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Code'), Mage::helper('adminhtml')->__('Affiliate Code'));
               
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
               
                $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode_edit'))
                     ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode_edit_tabs'));
                   
                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Affiliate code does not exist'));
                $this->_redirect('*/*/');
            }
        }
       
        public function newAction()
        {
            $affiliateCodeId = $this->getRequest()->getParam('id');
            Mage::register('affiliateId', $this->getRequest()->getParam('affiliate_id') );
            $affiliateCodeModel  = Mage::getModel('affiliate/affiliatecode')->load($affiliateCodeId);
     
            if ($affiliateCodeModel->getId() || $affiliateCodeId == 0) {
     
                Mage::register('affiliatecode_data', $affiliateCodeModel);
     
                $this->loadLayout();
                $this->_setActiveMenu('affiliate/items');
               
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
                $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Affiliate Code'), Mage::helper('adminhtml')->__('Affiliate Code'));
               
                $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
               
                $this->_addContent($this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode_edit'))
                     ->_addLeft($this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode_edit_tabs'));
                   
                $this->renderLayout();
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('affiliate')->__('Affiliate code does not exist'));
                $this->_redirect('*/*/');
            }
        }
       
        public function saveAction()
        {
            if ( $this->getRequest()->getPost() ) {
                try {
                    $postData = $this->getRequest()->getPost();
//                    prepeare date format
                    if (strlen($postData['created_time']) > 19) {
                        $postData['created_time'] = substr($postData['created_time'], 0, 17);
                    }

                    if (strlen($postData['end_time']) > 19) {
                        $postData['end_time'] = substr($postData['end_time'], 0, 17);
                    }
                    
                    $affiliateCodeModel = Mage::getModel('affiliate/affiliatecode');
                    $affiliateCodeModel->setId($this->getRequest()->getParam('id'))
                        ->setAffiliate_id($postData['affiliate_id'])
                        ->setCode($postData['code'])
                        ->setDescription($postData['description'])
                        ->setStatus($postData['status'])
                        ->setCreated_time( date('Y-m-d H:i:s', strtotime($postData['created_time'])))
                        ->setEnd_time(date('Y-m-d H:i:s', strtotime($postData['end_time'])))
                        ->setAmount_add( intval($postData['amount_add']) )
                        ->save();
                   
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully saved'));
                    Mage::getSingleton('adminhtml/session')->setAffiliatecodeData(false);

                    $this->_redirect('*/adminhtml_affiliate/edit/id/' . $postData['affiliate_id']);
                    return;
                } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setAffiliatecodeData($this->getRequest()->getPost());
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
                    $affiliateCodeModel = Mage::getModel('affiliate/affiliatecode');
                   
                    $affiliateCodeModel->setId($this->getRequest()->getParam('id'))
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

        public function gridAction()
        {
            $this->loadLayout();
            $this->getResponse()->setBody(
                   $this->getLayout()->createBlock('importedit/adminhtml_affiliatecode_grid')->toHtml()
            );
        }
    }