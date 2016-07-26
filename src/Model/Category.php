<?php
/**
 * Created by PhpStorm.
 * User: pgroot
 * Date: 16/7/26
 * Time: 00:31
 */
require dirname(__FILE__).'/../sdk/autoload.php';

class Qiniu_CDN_Model_Category {

    public function upload ($observer) {

        $category = $observer->getEvent()->getCategory();
        $image = $category->getImage();

        if(!is_string($image)) {
            return;
        }

        $store = Mage::app()->getStore();
        $enable = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ENABLE,$store);
        if($enable == 0) return;

        $accessKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ACCESS_KEY, $store);
        $secretKey = Mage::getStoreConfig(Qiniu_CDN_Model_Config::SECRET_KEY, $store);
        $bucket = Mage::getStoreConfig(Qiniu_CDN_Model_Config::BUCKET, $store);
        $prefix = Mage::getStoreConfig(Qiniu_CDN_Model_Config::PREFIX, $store);
        //$rename = Mage::getStoreConfig(Qiniu_CDN_Model_Config::IS_RENAME_FILE,$store);

        if (!($accessKey && $secretKey && $bucket))
            return;

        //todo use category config
        $config = Mage::getSingleton('catalog/product_media_config');

        $auth = new Qiniu\Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);
        $uploadMgr = new Qiniu\Storage\UploadManager();

        $file = str_replace('product','category',$config->getMediaPath($image));

        $cdnPath = $prefix .'/catalog/category/'. ltrim($image,'/');

        list($ret, $err) = $uploadMgr->putFile($token, $cdnPath, $file);

        if ($err !== null) {
            $msg = 'Can\'t upload original image (' . $file . ') to Qiniu with '
                . $cdnPath . ' key';

            throw new Mage_Core_Exception($msg);
        }
    }
}