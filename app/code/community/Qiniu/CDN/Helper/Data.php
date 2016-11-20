<?php

/**
 * User: GROOT (pzyme@outlook.com)
 * Date: 2016/7/26
 * Time: 00:13
 */
require Mage::getBaseDir('lib').'/Qiniu/functions.php';

class Qiniu_CDN_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_prefix = null;

    public function download($path, $website = null, $size = null)
    {
        $config = $this->_getConfig($website);

        try {
            $object = $this->_getObject($path, $size);
            $file = file_get_contents($config['domain'] . ltrim($object, '/'));
            file_put_contents($path, $file);
        } catch (Exception $e) {
            if (file_exists($path))
                unlink($path);
            return false;
        }
        return $path;
    }

    public function upload($from, $path, $website = null, $size = null)
    {
        $config = $this->_getConfig($website);

        $auth = new \Qiniu\Auth($config['accessKey'], $config['secretKey']);
        $token = $auth->uploadToken($config['bucket']);
        $uploadMgr = new \Qiniu\Storage\UploadManager();

        list($ret, $err) = $uploadMgr->putFile($token, $this->_getObject($path, $size), $from);

        return $err == null;
    }


    protected function _getConfig($website = null)
    {
        $store = Mage::app()->getWebsite($website)->getDefaultStore();

        $accessKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ACCESS_KEY, $store);
        $secretKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::SECRET_KEY, $store);
        $prefix = Mage::getStoreConfig(Qiniu_CDN_Model_Config::PREFIX, $store);
        $domain = Mage::getStoreConfig(Qiniu_CDN_Model_Config::DOMAIN, $store);

        $this->_prefix = $prefix;
        return array(
            'accessKey' => $accessKey,
            'secretKey' => $secretKey,
            'prefix' => $prefix,
            'domain' => $domain
        );
    }

    protected function _getObject(&$path, $size = null)
    {
        $config = Mage::getSingleton('catalog/product_media_config');

        $imagePath = str_replace($config->getMediaPath($size), '', $path);

        if (strpos($imagePath, '/') !== 0)
            $imagePath = '/' . $imagePath;

        if ($imagePath == $path)
            $path = $config->getMediaPath($size . $path);

        return $this->_prefix . $imagePath;
    }
}
