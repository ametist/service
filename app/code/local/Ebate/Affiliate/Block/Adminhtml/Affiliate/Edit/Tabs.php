<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliate_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
    {
     
        public function __construct()
        {
            parent::__construct();
            $this->setId('affiliate_tabs');
            $this->setDestElementId('edit_form');
            $this->setTitle(Mage::helper('affiliate')->__('News Information'));
        }
     
        protected function _beforeToHtml()
        {
            $this->addTab('form_section', array(
                'label'     => Mage::helper('affiliate')->__('Item Information'),
                'title'     => Mage::helper('affiliate')->__('Item Information'),
                'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit_tab_form')->toHtml(),
            ));

            $this->addTab('form_section2', array(
                'label'     => Mage::helper('affiliate')->__('affilate codes'),
                'title'     => Mage::helper('affiliate')->__('affilate codes'),
                'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliate_edit_tab_form2')->toHtml(),
//                'content'   => $this->getLayout()->createBlock('affiliate/adminhtml_affiliatecode')->toHtml(),

            ));
           
            return parent::_beforeToHtml();
        }
    }