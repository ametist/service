<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliatecode extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
        public function __construct()
        {
            $this->_controller = 'adminhtml_affiliatecode';
            $this->_blockGroup = 'affiliate';
            $this->_headerText = Mage::helper('affiliate')->__('Affiliate code list');
            $this->_addButtonLabel = Mage::helper('affiliate')->__('Add affiliate code');
            parent::__construct();
        }
    }