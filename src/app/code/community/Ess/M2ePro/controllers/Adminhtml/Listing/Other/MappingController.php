<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Adminhtml_Listing_Other_MappingController
    extends Ess_M2ePro_Controller_Adminhtml_BaseController
{
    //########################################

    public function mapGridAction()
    {
        $block = $this->loadLayout()->getLayout()->createBlock('M2ePro/adminhtml_listing_other_mapping_grid');
        $this->getResponse()->setBody($block->toHtml());
    }

    //########################################

    public function mapAction()
    {
        $componentMode = $this->getRequest()->getParam('componentMode');
        $productId = $this->getRequest()->getPost('productId');
        $sku = $this->getRequest()->getPost('sku');
        $productOtherId = $this->getRequest()->getPost('otherProductId');

        if ((!$productId && !$sku) || !$productOtherId || !$componentMode) {
            return;
        }

        /** @var $collection Ess_M2ePro_Model_Resource_Magento_Product_Collection */
        $collection = Mage::getConfig()->getModelInstance(
            'Ess_M2ePro_Model_Resource_Magento_Product_Collection',
            Mage::getModel('catalog/product')->getResource()
        );

        $productId && $collection->addFieldToFilter('entity_id', $productId);
        $sku       && $collection->addFieldToFilter('sku', $sku);

        $tempData = $collection->getSelect()->query()->fetch();
        if (!$tempData) {
            return $this->getResponse()->setBody('1');
        }

        $productId || $productId = $tempData['entity_id'];

        $productOtherInstance = Mage::helper('M2ePro/Component')->getComponentObject(
            $componentMode, 'Listing_Other', $productOtherId
        );

        $productOtherInstance->mapProduct($productId, Ess_M2ePro_Helper_Data::INITIATOR_USER);

        return $this->getResponse()->setBody('0');
    }

    public function autoMapAction()
    {
        $componentMode = $this->getRequest()->getParam('componentMode');
        $productIds = $this->getRequest()->getParam('product_ids');

        if (empty($productIds)) {
            return $this->getResponse()->setBody('You should select one or more Products');
        }

        if (empty($componentMode)) {
            return $this->getResponse()->setBody('Component is not defined.');
        }

        $productIds = explode(',', $productIds);

        $productsForMapping = array();
        foreach ($productIds as $productId) {

            /** @var $listingOther Ess_M2ePro_Model_Listing_Other */
            $listingOther = Mage::helper('M2ePro/Component')
                ->getComponentObject($componentMode, 'Listing_Other', $productId);

            if ($listingOther->getProductId()) {
                continue;
            }

            $productsForMapping[] = $listingOther;
        }

        $componentMode = ucfirst(strtolower($componentMode));
        $mappingModel = Mage::getModel('M2ePro/'.$componentMode.'_Listing_Other_Mapping');
        $mappingModel->initialize();

        if (!$mappingModel->autoMapOtherListingsProducts($productsForMapping)) {
            return $this->getResponse()->setBody('1');
        }
    }

    public function unmappingAction()
    {
        $componentMode = $this->getRequest()->getParam('componentMode');
        $productIds = $this->getRequest()->getParam('product_ids');

        if (!$productIds || !$componentMode) {
            return $this->getResponse()->setBody('0');
        }

        $productArray = explode(',', $productIds);

        if (empty($productArray)) {
            return $this->getResponse()->setBody('0');
        }

        foreach ($productArray as $productId) {
            $listingOtherProductInstance = Mage::getModel('M2ePro/Listing_Other')->load($productId);

            if (!$listingOtherProductInstance->getId() ||
                $listingOtherProductInstance->getData('product_id') === null) {
                continue;
            }

            $listingOtherProductInstance->unmapProduct(Ess_M2ePro_Helper_Data::INITIATOR_USER);
        }

        return $this->getResponse()->setBody('1');
    }

    //########################################
}