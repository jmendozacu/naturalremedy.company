<?php

/*
 * @author     M2E Pro Developers Team
 * @copyright  M2E LTD
 * @license    Commercial use is forbidden
 */

class Ess_M2ePro_Block_Adminhtml_Development_Tabs_Database_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    //########################################

    public function __construct()
    {
        parent::__construct();

        // Initialization block
        // ---------------------------------------
        $this->setId('developmentDatabaseGrid');
        // ---------------------------------------

        // Set default values
        // ---------------------------------------
        $this->setDefaultSort('component');
        $this->setDefaultDir('ASC');
        $this->setDefaultLimit(50);

        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        // ---------------------------------------
    }

   //########################################

    protected function _prepareCollection()
    {
        $magentoHelper   = Mage::helper('M2ePro/Magento');
        $structureHelper = Mage::helper('M2ePro/Module_Database_Structure');

        $tablesList = $magentoHelper->getMySqlTables();
        foreach ($tablesList as &$tableName) {
            $tableName = $structureHelper->getTableNameWithoutPrefix($tableName);
        }

        $tablesList = array_unique(array_merge($tablesList, $structureHelper->getMySqlTables()));

        $collection = new Ess_M2ePro_Model_Collection_Custom();
        foreach ($tablesList as $tableName) {
            if (!$structureHelper->isModuleTable($tableName)) {
                continue;
            }

            $tableRow = array(
                'table_name' => $tableName,
                'component'  => '',
                'group'      => '',
                'is_exist'   => $isExists = $structureHelper->isTableExists($tableName),
                'is_crashed' => $isExists ? !$structureHelper->isTableStatusOk($tableName) : false,
                'records'    => 0,
                'size'       => 0,
                'model'      => $structureHelper->getTableModel($tableName)
            );

            if ($tableRow['is_exist'] && !$tableRow['is_crashed']) {
                $tableRow['component'] = $structureHelper->getTableComponent($tableName);
                $tableRow['group']     = $structureHelper->getTableGroup($tableName);
                $tableRow['size']      = $structureHelper->getDataLength($tableName);
                $tableRow['records']   = $structureHelper->getCountOfRecords($tableName);
            }

            $collection->addItem(new Varien_Object($tableRow));
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'table_name', array(
            'header'    => Mage::helper('M2ePro')->__('Table Name'),
            'align'     => 'left',
            'index'     => 'table_name',
            'filter_index' => 'table_name',
            'frame_callback' => array($this, 'callbackColumnTableName'),
            'filter_condition_callback' => array($this, 'callbackFilterTitle'),
            )
        );

        // ---------------------------------------
        $options['general'] = 'General';
        $options = array_merge($options, Mage::helper('M2ePro/Component')->getComponentsTitles());

        $this->addColumn(
            'component', array(
            'header'    => Mage::helper('M2ePro')->__('Component'),
            'align'     => 'right',
            'width'     => '120px',
            'index'     => 'component',
            'type'      => 'options',
            'options'   => $options,
            'filter_index' => 'component',
            'filter_condition_callback' => array($this, 'callbackFilterMatch'),
            )
        );
        // ---------------------------------------

        // ---------------------------------------
        $options = array(
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_CONFIGS           => 'Configs',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_ACCOUNTS          => 'Accounts',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_MARKETPLACES      => 'Marketplaces',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_LISTINGS          => 'Listings',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_LISTINGS          => 'Listings',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_LISTINGS_PRODUCTS => 'Listings Products',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_LISTINGS_OTHER    => 'Listings Other',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_LOGS              => 'Logs',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_ITEMS             => 'Items',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_PROCESSING        => 'Processing',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_CONNECTORS        => 'Connectors',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_DICTIONARY        => 'Dictionary',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_ORDERS            => 'Orders',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_TEMPLATES         => 'Templates',
            Ess_M2ePro_Helper_Module_Database_Structure::TABLE_GROUP_OTHER             => 'Other'
        );

        $this->addColumn(
            'group', array(
            'header'    => Mage::helper('M2ePro')->__('Group'),
            'align'     => 'right',
            'width'     => '100px',
            'index'     => 'group',
            'type'      => 'options',
            'options'   => $options,
            'filter_index' => 'group',
            'filter_condition_callback' => array($this, 'callbackFilterMatch'),
            )
        );
        // ---------------------------------------

        $this->addColumn(
            'records', array(
            'header'    => Mage::helper('M2ePro')->__('Records'),
            'align'     => 'right',
            'width'     => '100px',
            'index'     => 'records',
            'type'      => 'number',
            'filter'    => false,
            )
        );

        $this->addColumn(
            'size', array(
            'header'    => Mage::helper('M2ePro')->__('Size (Mb)'),
            'align'     => 'right',
            'width'     => '100px',
            'index'     => 'size',
            'filter'    => false,
            )
        );

        return parent::_prepareColumns();
    }

    //########################################

    public function callbackColumnTableName($value, $row, $column, $isExport)
    {
        if (!$row->getData('is_exist')) {
            return "<p style=\"color: red; font-weight: bold;\">{$value} [table is not exists]</p>";
        }

        if ($row->getData('is_crashed')) {
            return "<p style=\"color: orange; font-weight: bold;\">{$value} [table is crashed]</p>";
        }

        if (!$row->getData('model')) {
            return "<p style=\"color: #878787;\">{$value}</p>";
        }

        return "<p>{$value}</p>";
    }

    //########################################

    public function getMassactionBlockName()
    {
        return 'M2ePro/adminhtml_grid_massaction';
    }

    protected function _prepareMassaction()
    {
        // Set massaction identifiers
        // ---------------------------------------
        $this->setMassactionIdField('table_name');
        $this->getMassactionBlock()->setFormFieldName('tables');
        $this->getMassactionBlock()->setUseSelectAll(false);
        // ---------------------------------------

        // Set edit action
        // ---------------------------------------
        $this->getMassactionBlock()->addItem(
            'edit', array(
            'label'    => Mage::helper('M2ePro')->__('Edit Table(s)'),
            'url'      => $this->getUrl('*/adminhtml_development_database/manageTables')
            )
        );
        // ---------------------------------------

        // Set delete action
        // ---------------------------------------
        $this->getMassactionBlock()->addItem(
            'delete', array(
            'label'    => Mage::helper('M2ePro')->__('Delete Table(s) Rows'),
            'url'      => $this->getUrl('*/adminhtml_development_database/deleteTablesRows'),
            'confirm'  => Mage::helper('M2ePro')->__('Are you sure?')
            )
        );
        // ---------------------------------------

        // Set truncate action
        // ---------------------------------------
        $this->getMassactionBlock()->addItem(
            'truncate', array(
            'label'    => Mage::helper('M2ePro')->__('Truncate Table(s)'),
            'url'      => $this->getUrl('*/adminhtml_development_database/truncateTables'),
            'confirm'  => Mage::helper('M2ePro')->__('Are you sure?')
            )
        );
        // ---------------------------------------

        return parent::_prepareMassaction();
    }

    //########################################

    protected function _toHtml()
    {
        $gridJsObj = $this->getId().'JsObject';

        $javascript = <<<HTML
<script>

    $$('#developmentDatabaseGrid_table select[name="component"]',
       '#developmentDatabaseGrid_table select[name="status"]',
       '#developmentDatabaseGrid_table select[name="group"]').each(function(el)
        {
            el.observe('change', function() { $gridJsObj.doFilter(); });
        });

</script>
HTML;

        return parent::_toHtml().$javascript;
    }

    //########################################

    public function getGridUrl()
    {
        return $this->getUrl('*/adminhtml_development_database/databaseGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        if (!$row->getData('is_exist') || $row->getData('is_crashed') || !$row->getData('model')) {
            return false;
        }

        return $this->getUrl(
            '*/adminhtml_development_database/manageTable',
            array('table' => $row->getData('table_name'))
        );
    }

    //########################################

    protected function callbackFilterTitle($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        if ($value == null) {
            return;
        }

        $this->getCollection()->addFilter(
            'table_name', $value, Ess_M2ePro_Model_Collection_Custom::CONDITION_LIKE
        );
    }

    protected function callbackFilterMatch($collection, $column)
    {
        $field = $column->getFilterIndex() ? $column->getFilterIndex()
                                           : $column->getIndex();

        $value = $column->getFilter()->getValue();
        if ($value == null || empty($field)) {
            return;
        }

        $this->getCollection()->addFilter(
            $field, $value, Ess_M2ePro_Model_Collection_Custom::CONDITION_MATCH
        );
    }

    //########################################
}
