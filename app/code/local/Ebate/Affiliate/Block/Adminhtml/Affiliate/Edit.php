<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();
                   
            $this->_objectId = 'id';
            $this->_blockGroup = 'affiliate';
            $this->_controller = 'adminhtml_affiliate';
     
            $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Item'));
            $this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Item'));
        }
     
        public function getHeaderText()
        {
            if( Mage::registry('affiliate_data') && Mage::registry('affiliate_data')->getId() ) {
                return Mage::helper('affiliate')->__("Edit Affiliate Partner '%s'", $this->htmlEscape(Mage::registry('affiliate_data')->getTitle()));
            } else {
                return Mage::helper('affiliate')->__('Add Item');
            }
        }
    }