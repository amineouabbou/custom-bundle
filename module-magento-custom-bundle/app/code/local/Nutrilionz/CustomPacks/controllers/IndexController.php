<?php

class Nutrilionz_CustomPacks_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        
        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
        
        if(!$product->getId()){
            return $this->_forward('noRoute');
        }
        
        
        // Register current data and dispatch final events
        Mage::register('current_product', $product);
        Mage::register('product', $product);
        
        
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle($product->getName());
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('product-bundle-view');
        }
        $this->renderLayout();

    }
}
?>