<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliate_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('affiliateGrid');
            // This is the primary key of the database
            $this->setDefaultSort('affiliate_id');
            $this->setDefaultDir('ASC');
            $this->setSaveParametersInSession(true);
        }
     
        protected function _prepareCollection()
        {
            $collection = Mage::getModel('affiliate/affiliate')->getCollection();
	    $collection->getSelect()
		       ->joinLeft(
			    array('admin_user' => 'admin_user'),
			    'admin_user.user_id = main_table.partner_account',
			    array('username' => 'admin_user.username')
			);
	    
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
     
        protected function _prepareColumns()
        {
            $this->addColumn('affiliate_id', array(
                'header'    => Mage::helper('affiliate')->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'affiliate_id',
            ));
     
            $this->addColumn('title', array(
                'header'    => Mage::helper('affiliate')->__('Title'),
                'align'     =>'left',
                'index'     => 'title',
            ));
	    
	    $this->addColumn('username', array(
                'header'    => Mage::helper('affiliate')->__('Account'),
                'align'     =>'left',
                'index'     => 'username',
            ));
     
            $this->addColumn('created_time', array(
                'header'    => Mage::helper('affiliate')->__('Creation Time'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'datetime',
                'default'   => '--',
                'index'     => 'created_time',
            ));
     
            $this->addColumn('update_time', array(
                'header'    => Mage::helper('affiliate')->__('Update Time'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'datetime',
                'default'   => '--',
                'index'     => 'update_time',
            ));   
     
     
            $this->addColumn('status', array(
     
                'header'    => Mage::helper('affiliate')->__('Status'),
                'align'     => 'left',
                'width'     => '80px',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => array(
                    1 => 'Active',
                    0 => 'Inactive',
                ),
            ));
     
            return parent::_prepareColumns();
        }
     
        public function getRowUrl($row)
        {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
     
     
    }