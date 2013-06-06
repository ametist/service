<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliatecode_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('affiliatecodeGrid');
            // This is the primary key of the database
            $this->setDefaultSort('id');
            $this->setDefaultDir('ASC');
            $this->setSaveParametersInSession(true);
        }
     
        protected function _prepareCollection()
        {
            $collection = Mage::getModel('affiliate/affiliatecode')->getCollection();
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
     
        protected function _prepareColumns()
        {
            $this->addColumn('id', array(
                'header'    => Mage::helper('affiliate')->__('ID'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'id',
            ));
     
            $this->addColumn('code', array(
                'header'    => Mage::helper('affiliate')->__('Code'),
                'align'     =>'left',
                'index'     => 'code',
            ));
     
            /*
            $this->addColumn('content', array(
                'header'    => Mage::helper('affiliate')->__('Item Content'),
                'width'     => '150px',
                'index'     => 'content',
            ));
            */
     
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

            $this->addColumn('created_time', array(
                'header'    => Mage::helper('affiliate')->__('Creation Time'),
                'align'     => 'left',
                'width'     => '120px',
                'type'      => 'date',
                'default'   => '--',
                'index'     => 'created_time',
            ));
     
     
     
     
            return parent::_prepareColumns();
        }
     
        public function getRowUrl($row)
        {
            return $this->getUrl('*/*/edit', array('id' => $row->getId()));
        }
     
     
    }