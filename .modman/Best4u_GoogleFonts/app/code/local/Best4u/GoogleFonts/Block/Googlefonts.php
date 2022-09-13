<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Block_Googlefonts extends Mage_Core_Block_Template
{

    /**
     * Fonts cache
     *
     * @var null|array
     */
    protected $fonts;

    /**
     * Retrieve font's urls
     *
     * @var null|array
     */
    public function getFonts()
    {
        if (null === $this->fonts) {
            $this->fonts = [];

            foreach ($this->helper('googlefonts')->getFonts() as $font) {
                if (array_key_exists('status', $font) && !$font['status']) {
                    continue;
                }

                $this->fonts[$font['font']] = Mage::getSingleton('googlefonts/googlefonts')
                    ->getUrls($font['font']);
            }
        }

        return $this->fonts;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if (! $this->helper('googlefonts')->isEnabled()) {
            return '';
        }

        return parent::_toHtml();
    }
}
