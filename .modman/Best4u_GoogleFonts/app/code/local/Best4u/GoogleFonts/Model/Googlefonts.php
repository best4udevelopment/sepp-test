<?php
/**
 * Copyright © Best4u. All rights reserved.
 */

class Best4u_GoogleFonts_Model_Googlefonts
{

    /**
     * Cache key
     */
    const CACHE_KEY = 'best4u_googlefonts';

    /**
     * Cache lifetime
     */
    const CACHE_TIMEOUT = 7 * 2 * 60 * 60;

    /**
     * Google fonts endpoint URL
     */
    const FONT_API_URL = 'https://www.googleapis.com/webfonts/v1/webfonts?sort=alpha';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getMetadata() ?: [] as $option) {
            if (! array_key_exists('family', $option) || '' === trim($option['family'])) {
                continue;
            }

            $options[] = [
                'label' => $option['family'],
                'value' => $this->prepareIdentifier($option)
            ];
        }

        return $options;
    }

    /**
     * Get font options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];

        foreach ($this->toOptionArray() as $font) {
            $options[$font['value']] = $font['label'];
        }

        return $options;
    }

    /**
     * Download font files
     *
     * @param string $identifier
     * @param bool $force
     * @return Best4u_GoogleFonts_Model_Googlefonts
     */
    public function download($identifier, $force = false)
    {
        $metadata = $this->getMetadata($identifier);

        if (is_array($metadata) && array_key_exists('files', $metadata)) {
            $ioFile = (new Varien_Io_File)->setAllowCreateFolders(true);

            foreach ($metadata['files'] as $file) {
                $path = $this->getFilePath($file);

                if (null === $path) {
                    continue;
                }

                $path = Mage::getSingleton('googlefonts/googlefonts_config')->getMediaPath($path);
                $filename = pathinfo($path, PATHINFO_BASENAME);

                $ioFile->open(array('path' => $ioFile->dirname($path)));

                if ($force || !$ioFile->fileExists($filename)) {
                    $ioFile->streamOpen($filename, 'w');
                    $ioFile->streamLock();
                    $success = false;

                    $curl = new Varien_Http_Adapter_Curl;
                    $curl->setConfig(array(
                        'timeout' => 30,
                        'header' => false
                    ));
                    $curl->addOption(CURLOPT_VERBOSE, false);

                    $curl->write(Zend_Http_Client::GET, $file, Zend_Http_Client::HTTP_0);
                    $font = $curl->read();

                    if ($font && 200 === $curl->getInfo(CURLINFO_HTTP_CODE)) {
                        $ioFile->streamWrite($font);
                        $success = true;
                    }

                    $curl->close();
                    $ioFile->streamUnlock();
                    $ioFile->streamClose();

                    if (! $success) {
                        $ioFile->rm($filename);
                    }
                }

                $ioFile->open();
                $ioFile->close();
            }

            unset($ioFile);
        }

        return $this;
    }

    /**
     * Retrieve font's urls
     *
     * @param string $identifier
     * @return array
     */
    public function getUrls($identifier)
    {
        $metadata = $this->getMetadata($identifier);
        $urls = [];

        if (is_array($metadata) && array_key_exists('files', $metadata)) {
            $ioFile = new Varien_Io_File;

            $this->download($identifier);

            foreach ($metadata['files'] as $variant => $file) {
                $path = $this->getFilePath($file);

                if (null === $path) {
                    continue;
                }

                $absolutePath = Mage::getSingleton('googlefonts/googlefonts_config')->getMediaPath($path);

                if ($ioFile->fileExists($absolutePath)) {
                    $urls[$variant] = Mage::getSingleton('googlefonts/googlefonts_config')->getMediaUrl(
                        $path
                    );
                } else {
                    $urls[$variant] = $file;
                }
            }

            unset($ioFile);
        }

        return $urls;
    }

    /**
     * Retrieve file path
     *
     * @param string $file
     * @return null|string
     */
    protected function getFilePath($file)
    {
        if (preg_match('/fonts\.gstatic\.com\/s\/(.+)$/i', $file, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Retrieve metadata
     *
     * @param null|string $identifier
     * @return null|array
     */
    protected function getMetadata($identifier = null)
    {
        $metadata = Mage::app()->getCache()->load(self::CACHE_KEY) ?: null;
        $helper = Mage::helper('googlefonts');
        $metadata = null;

        if ($metadata) {
            $metadata = $this->decode($metadata);
        }

        if (!$metadata && ($apiKey = $helper->getApiKey())) {
            $url = Mage::helper('core/url')->addRequestParam(
                self::FONT_API_URL,
                array(
                    'key' => $apiKey
                )
            );

            $curl = new Varien_Http_Adapter_Curl;
            $curl->setConfig(array(
                'timeout' => 10
            ));

            $curl->write(Zend_Http_Client::GET, $url, Zend_Http_Client::HTTP_0);
            $data = $curl->read();

            if ($data && 200 === $curl->getInfo(CURLINFO_HTTP_CODE)) {
                $data = preg_split('/^\r?$/m', $data, 2);
                $data = trim($data[1]);

                $metadata = $this->decode($data);

                if ($metadata) {
                    Mage::app()->getCache()->save(
                        Mage::helper('core')->jsonEncode($metadata),
                        self::CACHE_KEY,
                        array(),
                        self::CACHE_TIMEOUT
                    );
                }
            }

            $curl->close();
        }

        if (!is_array($metadata) || !array_key_exists('items', $metadata)) {
            return null;
        }

        if (null !== $identifier) {
            foreach ($metadata['items'] as $item) {
                if ($identifier === $this->prepareIdentifier($item)) {
                    return $item;
                }
            }
        }

        return $metadata['items'];
    }

    /**
     * Prepare identifier
     *
     * @param array $font
     * @return null|string
     */
    protected function prepareIdentifier(array $font)
    {
        if (array_key_exists('files', $font)) {
            foreach ($font['files'] as $file) {
                if (preg_match('/fonts\.gstatic\.com\/s\/([^\/]+)\//i', $file, $matches)) {
                    return $matches[1];
                }
            }
        }

        if (! array_key_exists('family', $font) || '' === trim($font['family'])) {
            return null;
        }

        $identifier = Mage::helper('catalog/product_url')->format($font['family']);
        $identifier = preg_replace('#[^0-9a-z]+#i', '-', $identifier);
        $identifier = strtolower($identifier);
        $identifier = trim($identifier, '-');

        return $identifier;
    }

    /**
     * Decode metadata
     *
     * @param array $metadata
     * @return null|array
     */
    protected function decode($metadata)
    {
        try {
            $metadata = Mage::helper('core')->jsonDecode($metadata);
        } catch (\Exception $e) {
            Mage::logException($e);

            $metadata = null;
        }

        return $metadata;
    }
}
