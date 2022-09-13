<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Fonts extends Mage_Core_Block_Html_Select
{

    /**
     * Set name
     *
     * @param string $value
     * @return Best4u_GoogleFonts_Block_Adminhtml_Form_Field_Googlefonts_Fonts
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
            foreach (Mage::getSingleton('googlefonts/googlefonts')->toArray() as $fontId => $fontLabel) {
                $this->addOption($fontId, addslashes($fontLabel));
            }
        }

        return parent::_toHtml();
    }
}
