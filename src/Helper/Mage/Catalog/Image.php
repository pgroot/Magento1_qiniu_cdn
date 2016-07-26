<?php

/**
 * User: GROOT (pzyme@outlook.com)
 * Date: 2016/7/25
 * Time: 22:13
 */
class Qiniu_CDN_Helper_Mage_Catalog_Image extends Mage_Catalog_Helper_Image
{


    public function __toString()
    {

        $store = Mage::app()->getStore();

        $enable = Mage::getStoreConfig(Qiniu_CDN_Model_Config::ENABLE, $store);
        if ($enable == 0) return parent::__toString();

        $model = $this->_getModel();
        $product = $this->getProduct();

        $attr = $model->getDestinationSubdir();

        if (!$imageFileName = $this->getImageFile())
            $imageFileName = $product->getData($attr);

        if ($imageFileName == 'no_selection') {
            $this->_placeholder = 'images/catalog/product/placeholder/' . $attr . '.jpg';
            return Mage::getDesign()->getSkinUrl($this->_placeholder);
        }

        $store = Mage::app()->getStore();
        $small_image_style = Mage::getStoreConfig(Qiniu_CDN_Model_Config::SMALL_IMAGE_STYLE,$store);
        $thumbnail_style = Mage::getStoreConfig(Qiniu_CDN_Model_Config::THUMBNAIL_STYLE,$store);

        $map = [
            'small_image' => $small_image_style,
            'image' => null,
            'thumbnail' => $thumbnail_style
        ];

        return $store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product/'
        . ltrim($imageFileName,'/') . (isset($map[$attr]) && $map[$attr] !== null ? '-' . $map[$attr] : '');

    }
}
