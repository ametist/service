<?php
                                                                       
    class Ebate_Affiliate_Block_Adminhtml_Affiliate_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
    {
	
	/**
	 * get partner role id
	 * @return int
	 */
	public function getPartnerRole()
	{
	   $collection = Mage::getModel('admin/role')->getCollection();
	   $collection->getSelect()
		      ->where('role_name = ?', 'Affiliate Partners');
	   $partner = '';   
	   foreach ($collection as $item)
	   {
	     $partner = $item->getRoleId();
	   }
	   
	   return $partner;
	}
	
	/**
	 * get admin accounts
	 * @return type 
	 */
	public function getAccounts() 
	{
	    $collection = Mage::getModel('admin/role')->getCollection();
	    $collection->getSelect()
		       ->where('parent_id = ?', $this->getPartnerRole())
		       ->join(
			    array('admin_user' => 'admin_user'),
			    'main_table.user_id = admin_user.user_id',
			    array('username' => 'admin_user.username')
			);
            
            $collection->setOrder('admin_user.username', 'ASC');


	    $dataItems = array();
	    $data = array();
	    foreach($collection as $item) {
		$dataItems['value'] = $item->getUserId();
		$dataItems['label'] = (string) $item['username'];
		$data[] = $dataItems;
	    }

	    return $data;
	}
	
        protected function _prepareForm()
        {
            $form = new Varien_Data_Form();
            $this->setForm($form);
            $fieldset = $form->addFieldset('affiliate_form', array('legend'=>Mage::helper('affiliate')->__('Item information')));
           
            $fieldset->addField('title', 'text', array(
                'label'     => Mage::helper('affiliate')->__('Title'),
                'class'     => 'required-entry',
                'required'  => true,
                'name'      => 'title',
            ));
	    
	    $accouns = $this->getAccounts();
	    $partner_account = $fieldset->addField('partner_account', 'select', array(
		'label' => Mage::helper('affiliate')->__('UserName'),
		'name' => 'partner_account',
	    ));
	    $partner_account->setData('values', $accouns);
	    
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
           
            $fieldset->addField('content', 'editor', array(
                'name'      => 'content',
                'label'     => Mage::helper('affiliate')->__('Content'),
                'title'     => Mage::helper('affiliate')->__('Content'),
                'style'     => 'width:98%; height:400px;',
                'wysiwyg'   => false,
                'required'  => true,
            ));

            $fieldset->addField('created_time', 'hidden', array(
                'label'     => Mage::helper('affiliate')->__('created_time'),
                'required'  => false,
                'name'      => 'created_time',
            ));

           
            if ( Mage::getSingleton('adminhtml/session')->getAffiliateData() )
            {
                $form->setValues(Mage::getSingleton('adminhtml/session')->getAffiliateData());
                Mage::getSingleton('adminhtml/session')->setAffiliateData(null);
            } elseif ( Mage::registry('affiliate_data') ) {
                $form->setValues(Mage::registry('affiliate_data')->getData());
            }
            return parent::_prepareForm();
        }
    }
    
    
    
