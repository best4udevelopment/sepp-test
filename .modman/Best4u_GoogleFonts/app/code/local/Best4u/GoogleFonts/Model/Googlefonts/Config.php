<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Model_Googlefonts_Config implements Mage_Media_Model_Image_Config_Interface
{

    /**
     * Module name
     *
     * @var string|null
     */
    protected $_moduleName;

    /**
     * Base Media URL
     *
     * @var string|null
     */
    protected $_baseMediaUrl;

    /**
     * Base Media Path
     *
     * @var string|null
     */
    protected $_baseMediaPath;

    /**
     * Fonts media directory
     *
     * @var string|null
     */
    protected $_mediaDir = 'googlefonts';

    /**
     * Retrive base url for media files
     *
     * @return string
     */
    public function getBaseMediaUrl()
    {
        if (null === $this->_baseMediaUrl) {
            $this->_baseMediaUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $this->_mediaDir;
        }

        return $this->_baseMediaUrl;
    }

    /**
     * Retrive base path for media files
     *
     * @return string
     */
    public function getBaseMediaPath()
    {
        if (null === $this->_baseMediaPath) {
            $this->_baseMediaPath = Mage::getConfig()->getOptions()->getMediaDir() . DS . $this->_mediaDir;
        }

        return $this->_baseMediaPath;
    }

    /**
     * Retrive url for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->getBaseMediaUrl() . '/' . $this->_prepareFileForUrl($file);
    }

    /**
     * Retrive file system path for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaPath($file)
    {
        return $this->getBaseMediaPath() . DS . $this->_prepareFileForPath($file);
    }

    /**
     * Prepare filename for URL
     *
     * @param string $file
     * @return string
     */
    protected function _prepareFileForUrl($file)
    {
        return trim(str_replace(DS, '/', $file), ' /');
    }
    
    /**
     * Prepare filename for path
     *
     * @param string $file
     * @return string
     */
    protected function _prepareFileForPath($file)
    {
        return trim(str_replace('/', DS, $file), ' ' . DS);
    }
}
