<?php
/**
 * Copyright © Best4u. All rights reserved.
 */
 
class Best4u_GoogleFonts_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * XML path to Google Font active flag
     */
    const XML_PATH_GOOGLE_FONTS_ACTIVE = 'design/googlefonts/active';

    /**
     * XML path to Google Fonts API key setting
     */
    const XML_PATH_GOOGLE_FONTS_API_KEY = 'design/googlefonts/api_key';

    /**
     * XML path to Google Fonts settings
     */
    const XML_PATH_GOOGLE_FONTS = 'design/googlefonts/fonts';

    /**
     * Fonts cache
     *
     * @var array
     */
    protected $fonts = [];

    /**
     * Check whether GoogleFonts is enabled
     *
     * @param mixed $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->getConfigFlag(self::XML_PATH_GOOGLE_FONTS_ACTIVE, $store)
            && $this->isModuleOutputEnabled();
    }

    /**
     * Retrieve foogle fonts api key
     *
     * @param mixed $store
     * @return null|string
     */
    public function getApiKey($store = null)
    {
        $apiKey = $this->getConfigData(self::XML_PATH_GOOGLE_FONTS_API_KEY, $store);

        if ($apiKey) {
            $apiKey = Mage::helper('core')->decrypt($apiKey);
        }

        return $apiKey;
    }

    /**
     * Retrieve foogle fonts
     *
     * @param mixed $store
     * @return array
     */
    public function getFonts($store = null)
    {
        $store = Mage::app()->getStore($store);

        if (! array_key_exists($store->getCode(), $this->fonts)) {
            $fonts = $this->getConfigData(self::XML_PATH_GOOGLE_FONTS, $store);

            $this->fonts[$store->getCode()] = [];

            if (! empty($fonts)) {
                try {
                    $this->fonts[$store->getCode()] = array_values(
                        Mage::helper('core/unserializeArray')->unserialize((string) $fonts)
                    );
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }

            unset($fonts);
        }

        return $this->fonts[$store->getCode()];
    }

    /**
     * Get module store configuration
     *
     * @param string $node
     * @param mixed $store
     * @return string|array|null
     */
    public function getConfigData($node = null, $store = null)
    {
        if (1 < substr_count($node, '/')) {
            return Mage::getStoreConfig($node, $store);
        }

        if (null !== $node) {
            return Mage::getStoreConfig(sprintf('design/googlefonts/%s', $node), $store);
        }

        return Mage::getStoreConfig('design/googlefonts', $store);
    }

    /**
     * Get store config flag
     *
     * @param string $node
     * @param mixed $store
     * @return bool
     */
    public function getConfigFlag($node, $store = null)
    {
        if (1 < substr_count($node, '/')) {
            return Mage::getStoreConfigFlag($node, $store);
        }

        return Mage::getStoreConfigFlag(sprintf('design/googlefonts/%s', $node), $store);
    }
}
