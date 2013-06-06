<?php
     
    class Ebate_Affiliate_Block_Adminhtml_Affiliate_Edit_Tab_Form2 extends Mage_Core_Block_Template
    {
        public $affiliateCodeCollection = false;
        
        private function setAffiliateCodeCollection() {
            if(!$this->affiliateCodeCollection) {
                $affiliateId = $this->getRequest()->getParam('id');

                foreach(Mage::getModel('affiliate/affiliatecode')->getCollection() as $code) {
                    if($code->affiliate_id == $affiliateId) {
                        $affilateCodeCollection[] = array('id'           => $code->id,  
                                                          'affiliate_id' => $affiliateId, 
                                                          'code'         => $code->code, 
                                                          'description'  => $code->description, 
                                                          'status'       => $code->status, 
                                                          'created_time' => $code->created_time, 
                                                          'end_time'     => $code->end_time, 
                                                          'amount_add'   => $code->amount_add, 
                                                         );
                    }
                }
                $this->affiliateCodeCollection = $affilateCodeCollection;
            }

            return $this->affiliateCodeCollection;
        }
        
        public function __construct() {
            $this->setAffiliateCodeCollection();           
            parent::__construct();
            $this->setTemplate('affiliate/index.phtml');
        }    
    
    }