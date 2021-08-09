<?php

class Nutrilionz_CustomPacks_ListController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $category_id = 7;
        $products = Mage::getModel('catalog/category')->load($category_id)
         ->getProductCollection()
         ->addAttributeToSelect('*') // add all attributes - optional
         ->addAttributeToFilter('status', 1) // enabled
         ->addAttributeToFilter('visibility', 4) //visibility in catalog,search
         ->setOrder('price', 'ASC'); //sets the order by price
        
        foreach($products as $product){
            print_r($product->getData());
        }
        
        die("ss");
        
        
        
        $this->loadLayout();
        //$this->getLayout()->getBlock('head')->setTitle($product->getName());
        $this->renderLayout();

    }
}
?>