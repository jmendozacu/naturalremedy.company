<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Walmart_Template_SellingFormat_Edit
    extends Ess_M2ePro_Block_Adminhtml_Walmart_Template_Edit
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('walmartTemplateSellingFormatEdit');
        $this->_blockGroup = 'M2ePro';
        $this->_controller = 'adminhtml_walmart_template_sellingFormat';
        $this->_mode = 'edit';
        // ---------------------------------------

        // Set header text
        // ---------------------------------------
        if (!Mage::helper('M2ePro/Component')->isSingleActiveComponent()) {
            $componentName = Mage::helper('M2ePro/Component_Walmart')->getTitle();

            if ($this->isEditMode()) {
                $this->_headerText = Mage::helper('M2ePro')->__(
                    'Edit %component_name% Selling Policy "%template_title%"', $componentName,
                    $this->escapeHtml(Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getTitle())
                );
            } else {
                $this->_headerText = Mage::helper('M2ePro')->__("Add %component_name% Selling Policy", $componentName);
            }
        } else {
            if ($this->isEditMode()) {
                $this->_headerText = Mage::helper('M2ePro')->__(
                    'Edit Selling Policy "%template_title%"',
                    $this->escapeHtml(Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getTitle())
                );
            } else {
                $this->_headerText = Mage::helper('M2ePro')->__("Add Selling Policy");
            }
        }

        // ---------------------------------------

        // Set buttons actions
        // ---------------------------------------
        $this->removeButton('back');
        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('add');
        $this->removeButton('save');
        $this->removeButton('edit');
        // ---------------------------------------

        // ---------------------------------------
        $url = Mage::helper('M2ePro')->getBackUrl('list');
        $this->_addButton(
            'back', array(
            'label'     => Mage::helper('M2ePro')->__('Back'),
            'onclick'   => 'WalmartTemplateSellingFormatHandlerObj.back_click(\'' . $url . '\')',
            'class'     => 'back'
            )
        );
        // ---------------------------------------

        if (Mage::helper('M2ePro/Data_Global')->getValue('temp_data')
            && Mage::helper('M2ePro/Data_Global')->getValue('temp_data')->getId()
        ) {
            // ---------------------------------------
            $this->_addButton(
                'duplicate', array(
                'label'   => Mage::helper('M2ePro')->__('Duplicate'),
                'onclick' => 'WalmartTemplateSellingFormatHandlerObj.duplicate_click'
                    .'(\'walmart-template-sellingFormat\')',
                'class'   => 'add M2ePro_duplicate_button'
                )
            );
            // ---------------------------------------

            // ---------------------------------------
            $this->_addButton(
                'delete', array(
                'label'     => Mage::helper('M2ePro')->__('Delete'),
                'onclick'   => 'WalmartTemplateSellingFormatHandlerObj.delete_click()',
                'class'     => 'delete M2ePro_delete_button'
                )
            );
            // ---------------------------------------
        }

        // ---------------------------------------
        $this->_addButton(
            'save', array(
            'label'     => Mage::helper('M2ePro')->__('Save'),
            'onclick'   => 'WalmartTemplateSellingFormatHandlerObj.save_click('
                . '\'\','
                . '\'' . $this->getSaveConfirmationText() . '\','
                . '\'' . Ess_M2ePro_Block_Adminhtml_Walmart_Template_Grid::TEMPLATE_SELLING_FORMAT . '\''
            . ')',
            'class'     => 'save'
            )
        );
        // ---------------------------------------

        // ---------------------------------------
        $this->_addButton(
            'save_and_continue', array(
            'label'     => Mage::helper('M2ePro')->__('Save And Continue Edit'),
            'onclick'   => 'WalmartTemplateSellingFormatHandlerObj.save_and_edit_click('
                . '\'\','
                . 'undefined,'
                . '\'' . $this->getSaveConfirmationText() . '\','
                . '\'' . Ess_M2ePro_Block_Adminhtml_Walmart_Template_Grid::TEMPLATE_SELLING_FORMAT . '\''
            . ')',
            'class'     => 'save'
            )
        );
        // ---------------------------------------
    }

    //########################################

    protected function isEditMode()
    {
        $templateModel = Mage::helper('M2ePro/Data_Global')->getValue('temp_data');
        return $templateModel && $templateModel->getId();
    }

    //########################################
}
