<?php
/**
 * @category	BlueVisionTec
 * @package     BlueVisionTec_GoogleShoppingApi
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @copyright   Copyright (c) 2015 BlueVisionTec UG (haftungsbeschränkt) (http://www.bluevisiontec.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Data Api account types Source
 *
 * @category	BlueVisionTec
 * @package    BlueVisionTec_GoogleShoppingApi
 * @author     Magento Core Team <core@magentocommerce.com>
 * @author      BlueVisionTec UG (haftungsbeschränkt) <magedev@bluevisiontec.eu>
 */
class BlueVisionTec_GoogleShoppingApi_Model_Source_Accounttype
{
    /**
     * Retrieve option array with account types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'HOSTED_OR_GOOGLE', 'label' => Mage::helper('googleshoppingapi')->__('Hosted or Google')),
            array('value' => 'GOOGLE', 'label' => Mage::helper('googleshoppingapi')->__('Google')),
            array('value' => 'HOSTED', 'label' => Mage::helper('googleshoppingapi')->__('Hosted'))
        );
    }
}
