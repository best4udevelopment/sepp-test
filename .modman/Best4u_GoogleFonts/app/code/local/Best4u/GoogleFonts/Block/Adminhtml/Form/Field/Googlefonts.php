<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    /**
     * Font renderer
     *
     * @var null|Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Fonts
     */
    protected $_fontRenderer;

    /**
     * Font status renderer
     *
     * @var null|Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Status
     */
    protected $_statusRenderer;

    /**
     * Retrieve font column renderer
     *
     * @return Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Fonts
     */
    protected function _getFontRenderer()
    {
        if (!$this->_fontRenderer) {
            $this->_fontRenderer = $this->getLayout()->createBlock(
                'googlefonts/adminhtml_form_field_googlefonts_fonts',
                '',
                array('is_render_to_js_template' => true)
            );
            $this->_fontRenderer->setClass('font-select');
            $this->_fontRenderer->setExtraParams('style="width:120px"');
        }
        return $this->_fontRenderer;
    }

    /**
     * Retrieve status column renderer
     *
     * @return Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Status
     */
    protected function _getStatusRenderer()
    {
        if (!$this->_statusRenderer) {
            $this->_statusRenderer = $this->getLayout()->createBlock(
                'googlefonts/adminhtml_form_field_googlefonts_status',
                '',
                array('is_render_to_js_template' => true)
            );
            $this->_statusRenderer->setClass('status-select');
        }
        return $this->_statusRenderer;
    }

    /**
     * Prepare to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'font',
            [
                'label' => Mage::helper('googlefonts')->__('Font'),
                'renderer' => $this->_getFontRenderer()
            ]
        );
        $this->addColumn(
            'status',
            [
                'label' => Mage::helper('googlefonts')->__('Active'),
                'renderer' => $this->_getStatusRenderer()
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('googlefonts')->__('Add font');
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object $row
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        foreach (array(
            'font' => $this->_getFontRenderer(),
            'status' => $this->_getStatusRenderer()
        ) as $field => $renderer) {
            $row->setData(
                'option_extra_attr_' . $renderer->calcOptionHash($row->getData($field)),
                'selected="selected"'
            );
            unset($field, $renderer);
        }
    }
}
