<?php

class Ebate_MYCheckout_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * get bundle options
     * @param type $productId 
     */
    public function getBundleOptions($productId) 
    {
	$child = Mage::getModel('bundle/product_type')->getOrderOptions($productId);

	$data = array();
	foreach($child['bundle_options'] as $item) {
	  $data['title'] = $item['value'][0]['title'];
	  $data['qty'] = $item['value'][0]['qty'];
	  $data['price'] = $item['value'][0]['price'];
	}
	
	if (!empty($data)) {
	  return $data;
	}
    }
    
    /**
     * Sort methods by conversion orders
     * 
     * @param array $methods
     * @param string $action
     * @return array 
     */
    public function sortPaymentMethods($methods) 
    {
        $_methodData = array();
        foreach ($methods as $_method):
            $_methodData['code'][$_method->getCode()] = $_method->getCode();
            $_methodData['title'][$_method->getCode()] = $this->htmlEscape($_method->getTitle());
        endforeach;

        $prepareMethodsData = array(
            $_methodData['title']['upc_standart'] => $_methodData['code']['upc_standart'],
           # $_methodData['title']['ik_interkassaprivat'] => $_methodData['code']['ik_interkassaprivat'],
            $_methodData['title']['privat24'] => $_methodData['code']['privat24'],
            $_methodData['title']['osmp_standart'] => $_methodData['code']['osmp_standart'],
            $_methodData['title']['bank'] => $_methodData['code']['bank'],
            $_methodData['title']['bonuspay'] => $_methodData['code']['bonuspay'],
            $_methodData['title']['ik_interkassaterm'] => $_methodData['code']['ik_interkassaterm'],
            $_methodData['title']['ik_interkassawebmoney'] => $_methodData['code']['ik_interkassawebmoney'],
            $_methodData['title']['nonstop_standart'] => $_methodData['code']['nonstop_standart'],
            $_methodData['title']['sms'] => $_methodData['code']['sms'],
            $_methodData['title']['ik_interkassaliqpay'] => $_methodData['code']['ik_interkassaliqpay'],
            $_methodData['title']['rewardpay'] => $_methodData['code']['rewardpay'],
            $_methodData['title']['upc_visa'] => $_methodData['code']['upc_visa'],
            $_methodData['title']['ibox_standart'] => $_methodData['code']['ibox_standart'],
            $_methodData['title']['alfacredit'] => $_methodData['code']['alfacredit'],
            $_methodData['title']['sdfree'] => $_methodData['code']['sdfree'],
            $_methodData['title']['cash'] => $_methodData['code']['cash'],
        );
        
                
        $prepareMethodsData = $this->exclusionReorderMethods($prepareMethodsData, $_methodData);
        
        return $prepareMethodsData;
    }
    
   /**
    * exclusion some reorder methods
    * 
    * @param array $prepareMethodsData
    * @param array $_methodData
    * @return array 
    */ 
   public function exclusionReorderMethods($prepareMethodsData, $_methodData)
   {
            if (Mage::app()->getRequest()->getActionName() == 'reorder') {
            $orderId = Mage::app()->getRequest()->getParam('order_id');

            if (!empty($orderId)) {
                $_order = Mage::getModel('sales/order')->load($orderId);
                $paymentMethod = $_order->getPayment()->getMethod();

                if ($paymentMethod == "upc_standart") {
                    unset($prepareMethodsData[$_methodData['title']['upc_standart']]);
                }
                if ($paymentMethod == "privat24") {
                    unset($prepareMethodsData[$_methodData['title']['privat24']]);
                }
                if ($paymentMethod == "ik_interkassaprivat") {
                    unset($prepareMethodsData[$_methodData['title']['ik_interkassaprivat']]);
                }
            }
        }

        return $prepareMethodsData;
    }
    
    /**
     * check if active static block
     * 
     * @param type $attribute
     * @param type $value
     * @return boolean 
     */
    public function isActive($attribute, $value) {

        $col = Mage::getModel('cms/block')->getCollection();
        $col->addFieldToFilter($attribute, $value);
        $item = $col->getFirstItem();
        $id = $item->getData('is_active');

        if ($id == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getServicePrice($product)
    {
        $_type = $product->getTypeId(); 
        if($_type == 'bundle') {
            $service_price = $this->getBundleServicePrice($product->getId());
        } else {
            $service_price = $product->getServicePrice();
        }
        
        return $service_price;
    }
    
    /**
     * get bundle options
     * @param type $productId 
     */
    public function getBundleServicePrice($productId) 
    {
	    $bundled_product = new Mage_Catalog_Model_Product();
	    $bundled_product->load($productId);
	    
	    $selectionCollection = $bundled_product->getTypeInstance(true)->getSelectionsCollection(
		$bundled_product->getTypeInstance(true)->getOptionsIds($bundled_product), $bundled_product
	    );

	    $bundled_items = array();
	    foreach($selectionCollection as $option)
	    {
		$bundled_items['product_id'][] = $option->product_id;
	    }
	    
	    $virtual = Mage::getModel('catalog/product')->load($bundled_items['product_id'][0]);
	    return $virtual->getServicePrice();	
    } 
   

}
