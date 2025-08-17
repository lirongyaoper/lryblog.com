<?php
/**
 * index.php 文件单一入口
 *
 * @author    lirongyao0916
 * @license    https://www.lrycms.com
 * @lastmodify       2025.07.07
 */

define('RYPHP_DEBUG', true);

define('RYPHP_ROOT',dirname(__FILE__).DIRECTORY_SEPARATOR);

require(RYPHP_ROOT . 'ryphp' . DIRECTORY_SEPARATOR . 'ryphp.php');

define('URL_MODEL', '3');

ryphp::app_init();