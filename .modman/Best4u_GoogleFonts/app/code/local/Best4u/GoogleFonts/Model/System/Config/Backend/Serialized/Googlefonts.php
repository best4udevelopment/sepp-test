<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Model_System_Config_Backend_Serialized_Googlefonts extends
    Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{

    /**
     * @inheritDoc
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (is_array($value)) {
            $unique = [];

            foreach ($value as $key => &$font) {
                if (!is_array($font) || empty($font['font'])) {
                    unset($value[$key]);
                }

                if ('__empty' === $key || !is_array($font) || empty($font['font'])) {
                    continue;
                }

                $unique[] = $font['font'];

                $font['status'] = (int) $font['status'] ?: 1;

                unset($key, $font);
            }

            if (count($unique) !== count(array_unique($unique))) {
                Mage::throwException(
                    Mage::helper('googlefonts')->__('Fonts must not be duplicated')
                );
            }

            $this->setValue($value);

            array_walk($value, function ($font) {
                Mage::getSingleton('googlefonts/googlefonts')->download($font['font']);
            });

            unset($unique);
        }

        unset($value);

        parent::_beforeSave();
    }
}
