<?php
require_once(Mage::getBaseDir("lib") . '/nusoap/nusoap_client.php');
class Ebate_Mailersoft_Model_Mailersoft extends Mage_Core_Model_Abstract
{
    private $_client;
    private $_wsdl;
    private $_api_key;

    static public $responses = array(
        self::SUCCESS => 'Subscriber was added.',
        self::INVALID_EMAIL => 'Invalid customer email.',
        self::INVALID_API_KEY => 'Invalid api key.',
        self::INVALID_GROUP_ID => 'Invalid group id.',
        self::IS_UNSUBSCRIBED => 'Customer is insubscribed.',
        self::IS_BOUNCED => 'Customer is bounced'
    );

    const SUCCESS = 0;

    const INVALID_EMAIL = 1;
    const INVALID_API_KEY = 100;
    const INVALID_GROUP_ID = 101;

    const IS_UNSUBSCRIBED = 201;
    const IS_BOUNCED = 202;


    public function _construct()
    {
        parent::_construct();
        $this->_init('mailer/mailersoft');
        $this->_wsdl = self::getConfig('wsdl');
        $this->_api_key = self::getConfig('api_key');
        $this->_client = new nusoap_client($this->_wsdl);
        return $this;
    }

    static public function getConfig($param)
    {
        return Mage::getStoreConfig('mailer/mailer_general/' . $param);
    }

    static public function getCityGroup($city_id)
    {
        return Mage::getStoreConfig('mailer/mailer_groups/group_' . $city_id);
    }

    function getDataByEmail(){}

    function addSubscriber(){}

    function sendContent(){}

    public function addSubscriberWithCustomFields(Mage_Customer_Model_Customer $customer, $group_id)
    {
        $params['api_key']  = $this->_api_key;
        $params['group_id'] = $group_id;//возваращается ли номер города в котором зарегистрирован пользователь
        $params['email']    = $customer->getName();
        $params['name']     = $customer->getEmail();//уточнить
        $params['fields'][] = array("name" => 'city', "value" => $customer->getGroupName());//уточнить
        $params['fields'][] = array("name" => 'affilate', "value" => $customer->getData('affiliate'));//уточнить
        $params['fields'][] = array("name" => 'validate', "value" => $customer->getData('validate'));//уточнить
        $response = $this->_client->call('addSubscriberWithCustomFields', $params);
        return $response;
    }
    
    public function observeSubscriber(array $data)
    {
        $params['api_key']  = $this->_api_key;
        $params['group_id'] = $this->getCityGroup($data['city_id']);//возваращается ли номер города в котором зарегистрирован пользователь
        $params['email']    = $data['email'];
        $params['name']     = $data['name'];//уточнить
        $params['fields'][] = array("name" => 'city', "value" => $data['city']);//уточнить
        $params['fields'][] = array("name" => 'affilate', "value" => $data['affiliate']);//уточнить
        $params['fields'][] = array("name" => 'validate', "value" => $data['validate']);//уточнить
         if($this->preSend($params)){
            $result = $this->_client->call('addSubscriberWithCustomFields', $params);
         }        
        return $result;
    }
    
    protected function preSend(array $params)
    {
        $array = array($params['api_key'], $params['email']);
        $group_id = $this->_client->call('getDataByEmail', $array);
        if(!empty($group_id['Subscriber'])){
            $groups = $group_id['Subscriber']['Groups'];
            #die(var_export($groups,1));
            if(count($groups) == 1){//&& $groups[0]['id'] == VisaPaymentModel::MAILERSOFT_GROUP
                return false;
            } else {
                array_walk($groups, array($this, 'removeFromGroup'), $params);
            }
        }
        return true;
    }
    
    protected function removeFromGroup(array $group, $key, $params)
    {
        $vals = array($params['api_key'], $group['id'], $params['email']);
        $current_group = $this->getCityGroup($params['city_id']);
        if($current_group == $group['id']) return true;
        $response = $this->_client->call('removeSubscriberFromGroup', $vals);
        if(empty($response['error']['code']) || $response['error']['code'] != 0) return false;
        return true;
    }

    public function parseResult(array $response)
    {
        return self::$responses[$response['code']];
    }

    function getStatistics(){}

    function hasGroup(){}

    function getMailLinks(){}

    public function getReport($mailId){
        $params['api_key']  = $this->_api_key;
        $params['mail_id']  = $mailId;
        
         if($this->preSend($params)){
            $result = $this->_client->call('getReport', $params);
         }        
        return $result;        
    }

    public function getReports(){
        $params['api_key']  = $this->_api_key;
       
         if($this->preSend($params)){
            $result = $this->_client->call('getReports', $params);
         }        
        return $result;
    }

    function getActiveSubscribers(){}

    function getChangedSubscribers(){}

    function getActiveSubscribersByGroup(){}

    function removeSubscriberFromGroup(Mage_Customer_Model_Customer $customer, $group_id)
    {
        return $this->client->call('removeSubscriberFromGroup',
                                   array('api_key' => $this->_api_key,
                                        'group_id' => $group_id,
                                        'email' => $customer->getEmail()
                                   ));
    }

    function getSubscribersByReport(){}

    function getGroupIdByName(){}

    function addGroup(){}

    function getGroup(){}

    function getGroups(){}

    function unsubscribeSubscriber(){}
    
    public function getCities() 
    {
	$collection = Mage::getModel('core/store_group')->getCollection();

	$collection->getSelect()->order('group_id ASC');

	return $collection;
    }
    
//    public function savePosition($data = array(), $cityId = 0)
//    {//echo "<pre>";
//        foreach($data as $productId => $productData)
//        {
//            //print_r($productData);
//            //var_dump($productData) . "<br>";
//            $product = Mage::getModel('mailer/mailersoft')
//                        ->getCollection()
//                        ->addFieldToFilter('product_id', $productId)
//                        ->addFieldToFilter('product_city', $cityId)
//                        ->getFirstItem(); 
//
//            $mailerId = $product->getId();
//
//            if(!empty($mailerId) AND $productData['position'] == 0)
//            {
//                $model = Mage::getModel('mailer/mailersoft')->load($mailerId);
//                $model->delete();
//            }
//            elseif(!empty($mailerId) AND $productData['position'] != 0)
//            {
//                $model = Mage::getModel('mailer/mailersoft')->load($mailerId);
//                $model->setProductPosition($productData['position']);
//                $model->setProductTitle($productData['title']);
//                $model->save();
//            }
//            else
//            {
//                if($productData['position'] != 0)
//                {
//                    $model = Mage::getModel('mailer/mailersoft');
//                    $model->setProductId($productId);
//                    $model->setProductPosition($productData['position']);
//                    $model->setProductTitle($productData['title']);
//                    $model->setProductCity($cityId);
//                    $model->save();
//                }
//            }
//        }
//    }
    
    public function savePosition($data = array(), $cityId = 0)
    {
        Mage::getModel('mailer/mailersoft')->getCollection()->addFieldToFilter('product_city', $cityId)->walk('delete');
        
        $iteration = 1;
        
        foreach($data as $productId => $productData)
        {
            $model = Mage::getModel('mailer/mailersoft');
            $model->setProductId($productId);
            $model->setProductPosition($iteration);
            $model->setProductTitle($productData['title']);
            $model->setProductCity($cityId);
            $model->save();
            
            $iteration++;
        }
    }
    
    public function prepareData($city)
    {
	$todayDate = Zend_Date::now()->setTimezone('Europe/Kiev')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	$tommorow12HourDate = Zend_Date::now()->setTimezone('Europe/Kiev')->addDay(1)->setHour(12)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
	
        $categoryId = Mage::helper('xmlexport_mailersoft')->getCategoryIdByCity($city);
        $todayDate = $tommorow12HourDate;
	
        $category = Mage::getModel('catalog/category')->load($categoryId);
	$data = array();
	$model = Mage::getModel('catalog/product');
        $renewAttribute = $model->getRenewAttributes();
	$collection = $model->getCollection();
        
//        $collection->getSelect()->joinLeft( array('superdeal_mailersoft' => 'superdeal_mailersoft'), 
//                                                  "superdeal_mailersoft.product_id = e.entity_id AND 
//                                                   superdeal_mailersoft.product_city = {$city}", 
//                                            array('product_id' => 'superdeal_mailersoft.product_id',
//                                                  'product_position' => 'superdeal_mailersoft.product_position',
//                                                  'product_title' => 'superdeal_mailersoft.product_title',   
//                                                  'product_city' => 'superdeal_mailersoft.product_city'));
        
	$collection->addCategoryFilter($category)
	           ->addAttributeToFilter('deal_start_time', array('or' => array(
				0 => array('datetime' => true, 'to' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
				), 'left')
		   ->addAttributeToFilter('deal_end_time', array('or' => array(
				0 => array('datetime' => true, 'from' => $todayDate),
				1 => array('is' => new Zend_Db_Expr('null')))
				), 'left')
		   ->addAttributeToFilter(
				array(
				    array('attribute' => 'deal_start_time', 'is' => new Zend_Db_Expr('not null')),
				    array('attribute' => 'deal_end_time', 'is' => new Zend_Db_Expr('not null'))
				)
			)
		   ->addAttributeToFilter('status', 1)
                   ->addAttributeToFilter('auto_renew', array('or' => array(
                                            0 => array('eq' => $renewAttribute[0]), 
                                            1 => array('is' => new Zend_Db_Expr('null'))
                                        )), 'left')
		   ->addAttributeToFilter('type_id', array('neq' => 'virtual'))
                   ->addAttributeToFilter('visibility', array('neq' => 1))
                   ->addAttributeToFilter('visibility', array('neq' => 3))
		   ->addAttributeToSort('product_position', 'desc');
//        $collection->printLogQuery(true); 
//        die();
    
        $renewCollection = Mage::getModel('catalog/product')->getCollection();
        
//        $renewCollection->getSelect()->joinLeft( array('superdeal_mailersoft' => 'superdeal_mailersoft'), 
//                                                       "superdeal_mailersoft.product_id = e.entity_id AND 
//                                                        superdeal_mailersoft.product_city = {$city}", 
//                                                 array('product_id' => 'superdeal_mailersoft.product_id', 
//                                                       'product_position' => 'superdeal_mailersoft.product_position',
//                                                       'product_title' => 'superdeal_mailersoft.product_title', 
//                                                       'product_city' => 'superdeal_mailersoft.product_city'));
        
        $renewCollection->addCategoryFilter($category)
                        ->addAttributeToFilter('status', array('eq'   => '1'))
                        ->addAttributeToFilter('auto_renew', array('and' => array(
                                                    0 => array('neq' => $renewAttribute[0]), 
                                                    1 => array('is not' => new Zend_Db_Expr('null'))
                                                )), 'left')
                        ->addAttributeToFilter('type_id', array('neq' => 'virtual'))
                        ->addAttributeToFilter('visibility', array('neq' => 1))
                        ->addAttributeToFilter('visibility', array('neq' => 3))
                        ->addAttributeToSort('product_position', 'desc')
                        ->addAttributeToSelect('deal_end_time');

        foreach($renewCollection as $item)
        { 
            $autoRenewDays  = $item->getAutoRenew();
            $renewDateStart = $item->getDealStartTime();
            $renewDateEnd   = $item->getDealEndTime();

            $clearDays = array_search($autoRenewDays, $renewAttribute);

            $autoRenewDateEnd = strtotime($renewDateEnd) + $clearDays * 24 * 60 * 60;

            if(strtotime($todayDate) >= strtotime($renewDateStart) AND strtotime($todayDate) <= $autoRenewDateEnd)
            {
                $collection->addItem($item);
            }
        }   
        
        $collItems = $collection->getItems();
        
        usort($collItems, array('SD_Mailersoft_Model_Mailersoft' ,'sortDateStart'));
        
        $main_counter = 0;
	foreach ($collItems as $offer) {
	    $id = $offer->getId(); 
	    
	    $model->load($id);
	    
	    $name = $model->getShortDescription();
	    
	    try {
	    	$picture = (string)Mage::helper('catalog/image')->init($model, 'image')->resize(440, 260); 
	    } catch(Exception $e) {
		$picture = (string)Mage::helper('catalog/image')->placeholder('Logo-go.png');
	    }
	    
            $main = ($main_counter >= 1) ? 0 : (int) $model->getIsMainDeal();
	    if($main == 1) {
		$main_counter++;
	    }
		
	    $type = $model->getTypeId(); 
	    $category = $model->getCategory();
	    
	    if($type == "bundle") {
	        
	       $bundleOptions = Mage::helper('xmlexport_mailersoft')->getBundleOptions($model->getId());
	       
	       $price = (int) $bundleOptions['price'][0];
	       $value = (int) $bundleOptions['value'][0]; 
               
               if($value == 0) {
                 $discount = (int) $bundleOptions['percent_discount'][0]; 
               } else {
	         $discount = (int) Mage::helper('xmlexport_mailersoft')->getMaxDiscountBundleNominal($model->getId());
               }
	       
	    } elseif($type == "downloadable") {
               
               $value = (int) $model->getServicePrice(); 
	       $price = (int) $model->getPrice();	
               
               if($value == 0) {
                  $discount = (int) $model->getPercentDiscount(); 
               } else {
                  $discount = (int) round((($value - $price) / $value)*100);
               }
	    }
	    
            $urlProduct = Mage::helper('xmlexport_mailersoft')->getProductLink($id, $city); 
           
	    $categories = $model->getCategoryIds();
            $manager = $model->getData('manager');
            $agreement_num = $model->getData('agreement_num');
            $annex_num = $model->getData('annex_num');
	    	    
	    $data[] = array('title' => $name, 'imglink' => $picture, 
			    'price' => $price, 'value' => $value,   
			    'percent' => $discount, 'link' => Mage::helper('xmlexport_mailersoft')->buildProductLink($type, $urlProduct, $categories[0], $city), 
			    'main' => $main,  
		            'all_count' => $collection->count(),
                            'manager' => $manager, 'agreement_num' => $agreement_num, 'annex_num' => $annex_num, 
                            'id' => $id, 'position' => ($offer->getProductPosition()) ? $offer->getProductPosition() : 0,
                            //'tempTitle' => ($offer->getProductTitle()) ? $offer->getProductTitle() : $name
			    );
	}

	return $data;
    }
    
    static public function sortDateStart($a, $b)
    {
        if(strtotime($a->getData('deal_start_time')) ==  strtotime($b->getData('deal_start_time'))){ return 0 ; }
        return (strtotime($a->getData('deal_start_time')) < strtotime($b->getData('deal_start_time'))) ? 1 : -1;
    }
    
    public function clearLog()
    {
        $db = Mage::getSingleton('core/resource')->getConnection('core_read');
        $db->query("TRUNCATE superdeal_mailersoft");
    }
}
    