<?php
/**
 * User: GROOT (pzyme@outlook.com)
 * Date: 2016/7/25
 * Time: 15:13
 */
require Mage::getBaseDir('lib').'/Qiniu/functions.php';


class Qiniu_CDN_Model_Observer {

    public function upload ($observer) {

        $product = $observer->getEvent()->getProduct();

        if ($product->getIsDuplicate()
            || $product->getData('qiniu_update_duplicate'))
            return;

        $images = $observer->getEvent()->getImages();

        $store = Mage::app()->getStore();


        $enable = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ENABLE,$store);
        if($enable == 0) return;

        $accessKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ACCESS_KEY, $store);
        $secretKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::SECRET_KEY, $store);
        $bucket = Mage::getStoreConfig(Qiniu_CDN_Model_Config::BUCKET, $store);
        $prefix = Mage::getStoreConfig(Qiniu_CDN_Model_Config::PREFIX, $store);
        $rename = Mage::getStoreConfig(Qiniu_CDN_Model_Config::IS_RENAME_FILE,$store);

        if (!($accessKey && $secretKey && $bucket))
            return;

        $config = Mage::getSingleton('catalog/product_media_config');

        $auth = new \Qiniu\Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new \Qiniu\Storage\UploadManager();

        foreach ($images['images'] as &$image) {
            if (isset($image['value_id']))
                continue;

            $fileName = $image['file'];
            $cdnPath = $prefix .'/catalog/product'. $fileName;

            $file = $config->getMediaPath($fileName);
            if($rename == 1) {
                $md5 = md5_file($file);
                $fileName = substr($md5,0,6).'/'.substr($md5,6,12).'/'.substr($md5,12).'.'.pathinfo($file,PATHINFO_EXTENSION);
                $cdnPath = $prefix .'/catalog/product/'. $fileName;
            }

            list($ret, $err) = $uploadMgr->putFile($token, $cdnPath, $file);

            if ($err !== null) {
                $msg = 'Can\'t upload original image (' . $file . ') to Qiniu with '
                    . $cdnPath . ' key';

                throw new Mage_Core_Exception($msg);
            }
            $image['file'] = $fileName;
        }
    }
}