<?php
/*
 * @ https://EasyToYou.eu - IonCube v10 Decoder Online
 * @ PHP 5.6
 * @ Decoder version: 1.0.3
 * @ Release: 10.12.2019
 *
 * @ ZendGuard Decoder PHP 5.6
 */

class General_ext
{
    public $cache = array();
    public $cache_hits = 0;
    public $cache_misses = 0;
    public $global_groups = array();
    public $ride_prefix = NULL;
    public function add($key_val_val, $data, $group = "default", $kill_me = 0)
    {
        if (Memory_suspend_cache_addition()) {
            return false;
        }
        if (empty($group)) {
            $group = "default";
        }
        $id = $key_val_val;
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $id = $this->ride_prefix . $key_val_val;
        }
        if ($this->_exists($id, $group)) {
            return false;
        }
        return $this->set($key_val_val, $data, $group, (int) $kill_me);
    }
    public function add_global_groups($groups)
    {
        $groups = (array) $groups;
        $groups = array_fill_keys($groups, true);
        $this->global_groups = array_merge($this->global_groups, $groups);
    }
    public function decr($key_val_val, $offset = 1, $group = "default")
    {
        if (empty($group)) {
            $group = "default";
        }
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $key_val = $this->ride_prefix . $key_val;
        }
        if (!$this->_exists($key_val, $group)) {
            return false;
        }
        if (!is_numeric($this->cache[$group][$key_val])) {
            $this->cache[$group][$key_val] = 0;
        }
        $offset = (int) $offset;
        $this->cache[$group][$key_val] -= $offset;
        if ($this->cache[$group][$key_val] < 0) {
            $this->cache[$group][$key_val] = 0;
        }
        return $this->cache[$group][$key_val];
    }
    public function delete($key_val, $group = "default", $force = false)
    {
        if (empty($group)) {
            $group = "default";
        }
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $key_val = $this->ride_prefix . $key_val;
        }
        if (!$force && !$this->_exists($key_val, $group)) {
            return false;
        }
        unset($this->cache[$group][$key_val]);
        return true;
    }
    public function flush()
    {
        $this->cache = array();
        return true;
    }
    public function get($key_val, $group = "default", $force = false, &$found = NULL)
    {
        if (empty($group)) {
            $group = "default";
        }
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $key_val = $this->ride_prefix . $key_val;
        }
        if ($this->_exists($key_val, $group)) {
            $found = true;
            $this->cache_hits += 1;
            if (is_object($this->cache[$group][$key_val])) {
                return clone $this->cache[$group][$key_val];
            }
            return $this->cache[$group][$key_val];
        }
        $found = false;
        $this->cache_misses += 1;
        return false;
    }
    public function incr($key_val, $offset = 1, $group = "default")
    {
        if (empty($group)) {
            $group = "default";
        }
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $key_val = $this->ride_prefix . $key_val;
        }
        if (!$this->_exists($key_val, $group)) {
            return false;
        }
        if (!is_numeric($this->cache[$group][$key_val])) {
            $this->cache[$group][$key_val] = 0;
        }
        $offset = (int) $offset;
        $this->cache[$group][$key_val] += $offset;
        if ($this->cache[$group][$key_val] < 0) {
            $this->cache[$group][$key_val] = 0;
        }
        return $this->cache[$group][$key_val];
    }
    public function replace($key_val, $data, $group = "default", $kill_me = 0)
    {
        if (empty($group)) {
            $group = "default";
        }
        $id = $key_val;
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $id = $this->ride_prefix . $key_val;
        }
        if (!$this->_exists($id, $group)) {
            return false;
        }
        return $this->set($key_val, $data, $group, (int) $kill_me);
    }
    public function reset()
    {
        _deprecated_function("reset", "3.5", "switch_to_ride()");
        foreach (array_keys($this->cache) as $group) {
            if (!isset($this->global_groups[$group])) {
                unset($this->cache[$group]);
            }
        }
    }
    public function set($key_val, $data, $group = "default", $kill_me = 0)
    {
        if (empty($group)) {
            $group = "default";
        }
        if ($this->multisite && !isset($this->global_groups[$group])) {
            $key_val = $this->ride_prefix . $key_val;
        }
        if (is_object($data)) {
            $data = clone $data;
        }
        $this->cache[$group][$key_val] = $data;
        return true;
    }
    public function stats()
    {
        echo "<p>";
        echo "<strong>Cache Hits:</strong> " . $this->cache_hits . "<br />";
        echo "<strong>Cache Misses:</strong> " . $this->cache_misses . "<br />";
        echo "</p><ul>";
        foreach ($this->cache as $group => $cache) {
            echo "<li><strong>Group:</strong> " . $group . " - ( " . number_format(strlen(serialize($cache)) / 1024, 2) . "k )</li>";
        }
        echo "</ul>";
    }
    public function switch_to_ride($ride_id)
    {
        $ride_id = (int) $ride_id;
        $this->ride_prefix = $this->multisite ? $ride_id . ":" : "";
    }
    protected function _exists($key_val, $group)
    {
        return isset($this->cache[$group]) && (isset($this->cache[$group][$key_val]) || array_key_exists($key_val, $this->cache[$group]));
    }
    public function __construct()
    {
        global $ride_id;
        $this->multisite = is_multisite();
        $this->ride_prefix = $this->multisite ? $ride_id . ":" : "";
        register_shutdown_function(array($this, "__destruct"));
    }
    public function __destruct()
    {
        return true;
    }
    public function getGeneralVar()
    {
        global $obj;
        $wri_usql = "SELECT * FROM configurations where eStatus='Active'";
        $wri_ures = $obj->MySQLSelect($wri_usql);
        for ($i = 0; $i < count($wri_ures); $i++) {
            $vName = $wri_ures[$i]["vName"];
            $vValue = $wri_ures[$i]["vValue"];
            global ${$vName};
            ${$vName} = $vValue;
        }
    }
    public function encrypt($data)
    {
        $i = 0;
        $key = 27;
        for ($c = 48; $i <= 255; $i++) {
            $c = 255 & ($key ^ $c << 1);
            $table[$key] = $c;
            $key = 255 & $key + 1;
        }
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $data[$i] = chr($table[ord($data[$i])]);
        }
        return base64_encode($data);
    }
    public function decrypt($data)
    {
        $data = base64_decode($data);
        $i = 0;
        $key = 27;
        for ($c = 48; $i <= 255; $i++) {
            $c = 255 & ($key ^ $c << 1);
            $table[$c] = $key;
            $key = 255 & $key + 1;
        }
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $data[$i] = chr($table[ord($data[$i])]);
        }
        return $data;
    }
    public function check_password($pass, $hash)
    {
        if (password_verify($pass, $hash)) {
            $test = 1;
        } else {
            $test = 0;
        }
        return $test;
    }
    public function getParentCatNew($iParentId = 0, $old_cat = "", $iCatIdNot = "0", $loop = 1, $iCategoryId)
    {
        global $obj;
        global $par_arr_new;
        $sql_query = "select iMenuId, vMenu, iParentId from menu  where iParentId='" . $iParentId . "' and eStatus='Active'";
        $db_cat_rs = $obj->MySQLSelect($sql_query);
        $n = count($db_cat_rs);
        if (0 < $n) {
            for ($i = 0; $i < $n; $i++) {
                $par_arr_new[] = array("iMenuId" => $db_cat_rs[$i]["iMenuId"], "vMenu" => $old_cat . "--|" . $loop . "|&nbsp;&nbsp;" . $db_cat_rs[$i]["vMenu"]);
                $this->getParentCatNew($db_cat_rs[$i]["iMenuId"], $old_cat . "&nbsp;&nbsp;&nbsp;&nbsp;", $iCatIdNot, $loop + 1, $iCategoryId);
            }
            $old_cat = "";
        }
        return $par_arr_new;
    }
    public function PrintComboBoxNew($arr, $selVal, $name, $title, $key = "", $val = "", $ext = "", $onchange = "", $selectboxName = "", $multiple_select = "")
    {
        $dcombo = "";
        $a = strrpos($name, "[]");
        if ($a) {
            $id = substr($name, 0, $a);
        } else {
            $id = $name;
        }
        if ($multiple_select != "") {
            $id = $selectboxName;
            $selectboxName = $selectboxName . "[]";
            $multiple_select = "multiple=" . $multiple_select;
        } else {
            $multiple_select = "";
        }
        if ($onchange != "") {
            $onchange = "onchange='" . $onchange . "'";
        }
        $dcombo .= "<select " . $multiple_select . " name=\"" . $name . "\" style=\"width:250px;\"   id=\"" . $id . "\" class=INPUT " . $ext . " " . $onchange . ">";
        if ($title != "") {
            if (empty($selVal)) {
                $sel = "selected";
            } else {
                $sel = "";
            }
            $dcombo .= "<option value='' " . $sel . ">" . $title . "</option>";
        }
        if ($key == "") {
            $key = 0;
        }
        if ($val == "") {
            $val = 1;
        }
        for ($i = 0; $i < count($arr); $i++) {
            if (@is_array($selVal)) {
                if (@in_array(@trim($arr[$i][$key]), $selVal)) {
                    $dcombo .= "<option value=" . $arr[$i][$key] . " selected>" . $arr[$i][$val] . "</option>";
                } else {
                    $dcombo .= "<option value=" . $arr[$i][$key] . ">" . $arr[$i][$val] . "</option>";
                }
            } else {
                if (trim($selVal) == trim($arr[$i][$key])) {
                    $dcombo .= "<option value=" . $arr[$i][$key] . " selected>" . $arr[$i][$val] . "</option>";
                } else {
                    $dcombo .= "<option value=" . $arr[$i][$key] . ">" . $arr[$i][$val] . "</option>";
                }
            }
        }
        $dcombo .= "</select>";
        return $dcombo;
    }
    public function checkDuplicate($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = "", $con = " or ")
    {
        global $obj;
        if ($iDbKeyValue != "") {
            $ssql = " and " . $iDbKeyName . " <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " " . $db_duplicateFieldArr[$i] . " = '" . $_REQUEST["Data"][$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count(" . $iDbKeyName . ") as tot from " . $TableName . " where 1 " . $ssql;
        $db_cnt = $obj->MySQLSelect($sql);
        if (0 < $db_cnt[0]["tot"]) {
            $_POST["duplicate"] = 1;
            $this->getPostForm($_POST, $msg, $vRedirectFile);
            exit;
        }
    }
    public function checkDuplicateFront($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = "", $con = " or ")
    {
        global $obj;
        $ssql = "";
        if ($iDbKeyValue != "") {
            $ssql = " and " . $iDbKeyName . " <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " " . $db_duplicateFieldArr[$i] . " = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count(" . $iDbKeyName . ") as tot from " . $TableName . " where 1 " . $ssql;
        $db_cnt = $obj->MySQLSelect($sql);
        if (0 < $db_cnt[0]["tot"]) {
            $_POST["duplicate"] = 1;
            $this->getPostForm($_POST, $msg, $vRedirectFile);
            exit;
        }
    }
    public function checkDuplicateAdmin($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = "", $con = " or ")
    {
        global $obj;
        $ssql = "";
        if ($iDbKeyValue != "") {
            $ssql = " and " . $iDbKeyName . " <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " " . $db_duplicateFieldArr[$i] . " = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count(" . $iDbKeyName . ") as tot from " . $TableName . " where 1 " . $ssql;
        $db_cnt = $obj->MySQLSelect($sql);
        if (0 < $db_cnt[0]["tot"]) {
            $duplicate = 1;
        } else {
            $duplicate = 0;
        }
        return $duplicate;
    }
    public function checkDuplicateAdminNew($iDbKeyName, $TableName, $db_duplicateFieldArr, $iDbKeyValue = "", $con = " or ")
    {
        global $obj;
        $ssql = "";
        if ($iDbKeyValue != "") {
            $ssql = " and " . $iDbKeyName . " <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " " . $db_duplicateFieldArr[$i] . " = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count(" . $iDbKeyName . ") as tot from " . $TableName . " where 1 " . $ssql;
        $db_cnt = $obj->MySQLSelect($sql);
        if (0 < $db_cnt[0]["tot"]) {
            $duplicate = 1;
        } else {
            $duplicate = 0;
        }
        return $duplicate;
    }
    public function getPostForm1($POST_Arr, $msg = "", $action = "")
    {
        $str = "\r\n\t\t\t<html>\r\n\t\t\t<form name=\"frm1\" action=\"" . $action . "\" method=post>";
        foreach ($POST_Arr as $key => $value) {
            if ($key != "mode") {
                if (is_array($value)) {
                    for ($i = 0; $i < count($value); $i++) {
                        $str .= "<br><input type=\"Hidden\" name=\"" . $key . "[]\" value=\"" . stripslashes($value[$i]) . "\">";
                    }
                } else {
                    $str .= "<br><input type=\"Hidden\" name=\"" . $key . "\" value=\"" . stripslashes($value) . "\">";
                }
            }
        }
        $str .= "<input type=\"Hidden\" name=var_msg_err value=\"" . $msg . "\">\r\n\t\t\t</form>\r\n\t\t\t<script>\r\n\t\t\tdocument.frm1.submit();\r\n\t\t\t</script>\r\n\t\t\t</html>";
        exit;
    }
    public function getPostForm($POST_Arr, $msg = "", $action = "")
    {
        $str = "\r\n\t\t\t<html>\r\n\t\t\t<form name=\"frm1\" action=\"" . $action . "\" method=post>";
        foreach ($POST_Arr as $key => $value) {
            if ($key != "mode") {
                if (is_array($value)) {
                    foreach ($value as $kk => $vv) {
                        $str .= "<br><input type=\"Hidden\" name=\"Data[" . $kk . "]\" value=\"" . stripslashes($vv) . "\">";
                    }
                    $str .= "<br><input type=\"Hidden\" name=\"" . $key . "[]\" value=\"" . stripslashes($value[$i]) . "\">";
                } else {
                    $str .= "<br><input type=\"Hidden\" name=\"" . $key . "\" value=\"" . stripslashes($value) . "\">";
                }
            }
        }
        $str .= "<input type=\"Hidden\" name=var_msg value=\"" . $msg . "\">\r\n\t\t\t</form>\r\n\t\t\t<script>\r\n\t\t\tdocument.frm1.submit();\r\n\t\t\t</script>\r\n\t\t\t</html>";
        echo $str;
        exit;
    }
}

?>