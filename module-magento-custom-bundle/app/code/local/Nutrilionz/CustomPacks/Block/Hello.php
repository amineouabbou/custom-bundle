<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer login block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Nutrilionz_CustomPacks_Block_Hello extends Mage_Core_Block_Template
{

    public function getProduct(){
        return Mage::registry('product');
    }

    public function getBundleOptions(){
        $product = $this->getProduct();
        $optionCollection = $product->getTypeInstance()->getOptionsCollection();
        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
        $options = $optionCollection->appendSelections($selectionCollection);
        return $options;
    }

    public function getProductOptions(){
        $productIds = array();
        $options_items = array();
        foreach( $this->getBundleOptions() as $option )
        {
            
            $options_items[] = $option;
            
            $_selections = $option->getSelections();
            $i = 0;
            foreach( $_selections as $selection )
            {
                if($i === 0){
                    $productIds[] = $selection->getId();
                }
                
                $i++;
            }
            
            $this->setData('ids_products', $productIds);
        }
        
        return $options_items;
    }
    
    public function getPrice($product){
        return Mage::helper('core')->currency($product->getFinalPrice(), true, false);
    }


    public function getChoices($product_id){
        $product = Mage::getModel('catalog/product')->load($product_id);
        if($product->isSalable()){
            if($product->getAttributeText('general_arome')){
                return $product->getAttributeText('general_arome');
            }
            
            if($product->getAttributeText('color')){
                return $product->getAttributeText('color');
            }
            
        }
    }

    
    public function getParentProducts(){
        $ids = $this->getData('ids_products');
        $products = [];
        
        foreach($ids as $id){
            $parents = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
            
            if(isset($parents[0])){
                $products[] = Mage::getModel('catalog/product')->load($parents[0]);
            }else{
                $products[] = Mage::getModel('catalog/product')->load($id);
            }
            
        }
        
        
        
        return $products;
        
    }
    
    
    public function formatPrice($price){
        return Mage::helper('core')->currency($price, true, false);
    }
    
    
    public function youSave($newprice) {
        $num =  round($this->getPackTotal() - $newprice, 2);
        return $this->formatPrice($num);
    }
    
    
    public function getPackTotal(){
        $total = [];
        
        foreach($this->getParentProducts() as $product){
            $total[] = $product->getFinalPrice();
        }
        
        $num = array_sum($total);
        
        return $num;
    }
    
    
    
    public function getRelatedProducts(){
        $bundle_names = array();
        
        $allRelatedProductIds = $this->getProduct()->getRelatedProductIds();
        foreach ($allRelatedProductIds as $id) {
            $product = Mage::getModel('catalog/product')->load($id);
            $bundle_names[] = $product;
        }
        
        return $bundle_names;
    }
    
    public function getRelatedChilds($id){
        $associated_products = array();
        $productIds = array();
        
        
        //Get product Options
        $product = Mage::getModel('catalog/product')->load($id);
        $optionCollection = $product->getTypeInstance()->getOptionsCollection();
        $selectionCollection = $product->getTypeInstance()->getSelectionsCollection($product->getTypeInstance()->getOptionsIds());
        $options = $optionCollection->appendSelections($selectionCollection);

        foreach( $options as $option ){
            $_selections = $option->getSelections();
            $i = 0;
            foreach( $_selections as $selection )
            {
                if($i === 0){
                    $productIds[] = $selection->getId();
                }

                $i++;
            }
        }
        
        
        //Get Parents
        foreach($productIds as $id){
            $parents = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
            
            if(isset($parents[0])){
                $associated_products[] = Mage::getModel('catalog/product')->load($parents[0]);
            }else{
                $associated_products[] = Mage::getModel('catalog/product')->load($id);
            }
            
        }
        
        
        return $associated_products;
    }
    
    
    public function getCustomUrl($id){
        return Mage::getUrl("custom-packs/index/index/id/{$id}");
    }
    
    
    public function getReviews($productId){
        /**
         * Getting reviews collection object
         */
        $reviews = Mage::getModel('review/review')
                        ->getResourceCollection()
                        ->addStoreFilter(Mage::app()->getStore()->getId())
                        ->addEntityFilter('product', $productId)
                        ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                        ->setDateOrder()
                        ->addRateVotes();
        
        return $reviews;
    }
}
