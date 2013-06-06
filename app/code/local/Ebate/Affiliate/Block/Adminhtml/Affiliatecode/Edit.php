<?php
                            
    class Ebate_Affiliate_Block_Adminhtml_Affiliatecode_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();
                   
            $this->_objectId = 'id';
            $this->_blockGroup = 'affiliate';
            $this->_controller = 'adminhtml_affiliatecode';
     
            $this->_updateButton('save', 'label', Mage::helper('affiliate')->__('Save Item'));
            $this->_updateButton('delete', 'label', Mage::helper('affiliate')->__('Delete Item'));
        }
     
        public function getHeaderText()
        {
            if( Mage::registry('affiliatecode_data') && Mage::registry('affiliatecode_data')->getId() ) {
                return Mage::helper('affiliate')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('affiliatecode_data')->getTitle()));
            } else {
                return Mage::helper('affiliate')->__('Add Item');
            }
        }
    }