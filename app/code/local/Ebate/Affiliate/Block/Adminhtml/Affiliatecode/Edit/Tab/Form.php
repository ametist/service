<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliatecode_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
	
	/**
	 * prepare city data
	 * @return type 
	 */
	public function getCityCount() {
	    $collection = Mage::getModel('core/store_group')->getCollection();
	    $collection->getSelect()
		    ->where('group_id > ?', 0)
		    ->order('group_id ASC');

	    return $collection->count();
	}
	
        private function generateNewAffiliateCode() {
            // 2 digits number
            $code = rand(1,99);
            if($code < 10) { 
		 $code = "0" . $code; 
	    }
            $code .= "-";
            
	    // City ID            
            $code .= rand(1, $this->getCityCount());
            $code .= "-";
            
	    // 7 letters
            for( $i=0; $i<7; $i++ ) {
                $code .= chr( rand( 65,90 ) );
            }            
            
            return $code;
        }
        
        private function checkGeneratedCode() {
                $code = $this->generateNewAffiliateCode();
                $affiliateCodeModel = Mage::getModel('affiliate/affiliatecode')->load($code ,'code');
            while( $affiliateCodeModel->getId()) {
                $code = $this->generateNewAffiliateCode();
                $affiliateCodeModel = Mage::getModel('affiliate/affiliatecode')->load($code ,'code');
            }
            return $code;
        }        
        
        
        
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Affiliate code information')));
           
            $fieldset->addField('code', 'text', array(
                'label'     => Mage::helper('affiliate')->__('Code'),
                'class'     => 'required-entry',
                'required'  => true,
                'readonly'  => true,
                'name'      => 'code',
            ));

            $fieldset->addField('affiliate_id', 'hidden', array(
                'label'     => Mage::helper('affiliate')->__('affiliate_id'),
                'required'  => false,
                'name'      => 'affiliate_id',
            ));
     
            $fieldset->addField('status', 'select', array(
                'label'     => Mage::helper('affiliate')->__('Status'),
                'name'      => 'status',
                'values'    => array(
                    array(
                        'value'     => 1,
                        'label'     => Mage::helper('affiliate')->__('Active'),
                    ),
     
                    array(
                        'value'     => 0,
                        'label'     => Mage::helper('affiliate')->__('Inactive'),
                    ),
                ),
            ));
            
            $fieldset->addField('description', 'editor', array(
                'name'      => 'description',
                'label'     => Mage::helper('affiliate')->__('description'),
                'title'     => Mage::helper('affiliate')->__('description'),
                'style'     => 'width:98%; height:100px;',
                'wysiwyg'   => false,
                'required'  => false,
            ));


            $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

            $fieldset->addField('created_time', 'date', array(
                            'name'      => 'created_time',
                            'label'     => Mage::helper('affiliate')->__('start_time'),
                            'title'     => Mage::helper('affiliate')->__('created_time'),
                            'required'  => false,
                            'image' => $this->getSkinUrl('images/grid-cal.gif'),
                            'format' => $outputFormat,
                            'time' => true,
                            'style' => 'width: 140px;'
            ));             

            $fieldset->addField('end_time', 'date', array(
                            'name'      => 'end_time',
                            'label'     => Mage::helper('affiliate')->__('end_time'),
                            'title'     => Mage::helper('affiliate')->__('end_time'),
                            'required'  => false,
                            'image' => $this->getSkinUrl('images/grid-cal.gif'),
                            'format' => $outputFormat,
                            'time' => true,
                            'style' => 'width: 140px;'
            ));             

            $fieldset->addField('amount_add', 'text', array(
                'label'     => Mage::helper('affiliate')->__('amount_add'),
                'class'     => 'required-entry',
                'required'  => false,
                'name'      => 'amount_add',
            ));

           
           
            if ( Mage::getSingleton('adminhtml/session')->getAffiliatecodeData() )
            {
                $affiliatecodeData = Mage::getSingleton('adminhtml/session')->getAffiliatecodeData();
                $affiliatecodeData['affiliate_id'] = Mage::registry('affiliateId');
                $form->setValues($affiliatecodeData);
                Mage::getSingleton('adminhtml/session')->setAffiliatecodeData(null);
            } elseif ( Mage::registry('affiliatecode_data')) {
                $affiliatecodeData = Mage::registry('affiliatecode_data')->getData();
                $affiliatecodeData['affiliate_id'] = Mage::registry('affiliateId');
                if(!isset($affiliatecodeData['code'])) {
                    $affiliatecodeData['code'] = $this->checkGeneratedCode(); 
                }
                $form->setValues($affiliatecodeData);
            }
            return parent::_prepareForm();
        }
    }