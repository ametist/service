<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliatecode_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
    {
     
        public function __construct()
        {
            parent::__construct();
            $this->setId('affiliatecode_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('affiliate')->__('News Information'));
        }
     
        protected function _beforeToHtml()
        {
            $this->addTab('form_section', array(
                'label'     => Mage::helper('affiliate')->__('Item Information'),
                'title'     => Mage::helper('affiliate')->__('Item Information'),
                'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode_edit_tab_form')->toHtml(),
            ));
            return parent::_beforeToHtml();
        }
    }