<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

ob_start();
@session_start();
@header("P3P:CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");
define("_TEXEC", 1);
define("TPATH_BASE", dirname(__FILE__));
define("DS", DIRECTORY_SEPARATOR);
require_once TPATH_BASE . DS . "assets" . DS . "libraries" . DS . "defines.php";
require_once TPATH_BASE . DS . "assets" . DS . "libraries" . DS . "configuration.php";

?>