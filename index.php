<?php
/**
 * index.php 文件单一入口
 *
 * @author    lirongyao0916
 * @license    https://www.lrycms.com
 * @lastmodify       2025.07.07
 */

define('RY_DEBUG', true);

define('RYPHP_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);

require(RYPHP_ROOT . 'ryphp' . DIRECTORY_SEPARATOR . 'ryphp.php');

//URL模式: 0=>mca兼容模式，1=>s兼容模式，2=>REWRITE模式，3=>SEO模式，4=>兼容性PATHINFO模式。
define('URL_MODEL', '3');

ryphp::app_init();