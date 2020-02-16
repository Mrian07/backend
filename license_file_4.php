<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (!isset($_SESSION["sess_lang"])) {
    $sql = "select vTitle, vCode, vCurrencyCode, eDefault,eDirectionCode from language_master where eDefault='Yes' limit 0,1";
    $db_lng_mst = $obj->MySQLSelect($sql);
    $_SESSION["sess_lang"] = $db_lng_mst[0]["vCode"];
    $_SESSION["eDirectionCode"] = $db_lng_mst[0]["eDirectionCode"];
}

?>