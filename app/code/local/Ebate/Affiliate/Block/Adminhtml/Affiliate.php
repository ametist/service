<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliate extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
        public function __construct()
        {
            $this->_controller = 'adminhtml_affiliate';
            $this->_blockGroup = 'affiliate';
            $this->_headerText = Mage::helper('affiliate')->__('Item Manager');
            $this->_addButtonLabel = Mage::helper('affiliate')->__('Add Item');
            parent::__construct();
        }
    }