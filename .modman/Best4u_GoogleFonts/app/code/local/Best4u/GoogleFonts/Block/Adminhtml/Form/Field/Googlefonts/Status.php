<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Status extends Mage_Core_Block_Html_Select
{

    /**
     * Set name
     *
     * @param string $value
     * @return Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Status
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (! $this->getOptions()) {
            foreach (Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray() as $value => $label) {
                $this->addOption($value, addslashes($label));
            }
        }

        return parent::_toHtml();
    }
}
