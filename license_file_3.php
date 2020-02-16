<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

$lang = isset($_REQUEST["lang"]) ? $_REQUEST["lang"] : "";
if (isset($lang) && $lang != "") {
    $_SESSION["sess_lang"] = $lang;
    $sql1 = "select vTitle, vCode, vCurrencyCode, eDefault,eDirectionCode from language_master where  vCode = '" . $_SESSION["sess_lang"] . "' limit 0,1";
    $db_lng_mst1 = $obj->MySQLSelect($sql1);
    $_SESSION["eDirectionCode"] = $db_lng_mst1[0]["eDirectionCode"];
    $posturi = $_SERVER["HTTP_REFERER"];
    header("Location:" . $posturi);
    exit;
}

?>