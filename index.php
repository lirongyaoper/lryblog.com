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


ryphp::app_init();