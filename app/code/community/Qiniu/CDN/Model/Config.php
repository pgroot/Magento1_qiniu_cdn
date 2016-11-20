<?php
/**
 * User: GROOT (pzyme@outlook.com)
 * Date: 2016/7/25
 * Time: 15:13
 */

abstract class Qiniu_CDN_Model_Config {

    const ENABLE = 'system/cdn/enable';
    const ACCESS_KEY = 'system/cdn/access_key';
    const SECRET_KEY = 'system/cdn/secret_key';
    const BUCKET = 'system/cdn/bucket';
    const PREFIX = 'system/cdn/prefix';
    const DOMAIN = 'system/cdn/domain';
    const SMALL_IMAGE_STYLE = 'system/cdn/small_image_style';
    const THUMBNAIL_STYLE = 'system/cdn/thumbnail_style';
    const IS_RENAME_FILE = 'system/cdn/md5_file_name';
}