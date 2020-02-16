<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

if (isset($currency) && $currency != "") {
    $_SESSION["sess_currency"] = $currency;
} else {
    $sql1 = "SELECT * FROM `currency` WHERE `eDefault` = 'Yes' AND `eStatus` = 'Active' ";
    $db_currency_mst = $obj->MySQLSelect($sql1);
    $_SESSION["sess_currency"] = $db_currency_mst[0]["vName"];
    $_SESSION["sess_currency_smybol"] = $db_currency_mst[0]["vSymbol"];
}

?>