<?php
/*
 * @ https://EasyToYou.eu - IonCube v10 Decoder Online
 * @ PHP 5.6
 * @ Decoder version: 1.0.3
 * @ Release: 10.12.2019
 *
 * @ ZendGuard Decoder PHP 5.6
 */

class General_admin_ext
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
    public function DateTime($text, $time = "yes")
    {
        if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00") {
            return "---";
        }
        $date = @date("jS F, Y", @strtotime($text));
        if ($time == "yes") {
            $date .= " " . @date("h:i a", @strtotime($text));
        }
        return $date;
    }
    public function go_to_home()
    {
        global $tconfig;
        $sess_iAdminUserId = isset($_SESSION["sess_iAdminUserId"]) ? $_SESSION["sess_iAdminUserId"] : "";
        if ($sess_iAdminUserId != "") {
            $url = "dashboard.php";
        }
        if ($url != "" && basename($_SERVER["PHP_SELF"]) != $url) {
            echo "<script>window.location=\"" . $url . "\";</script>";
            @header("Location:" . $url);
            exit;
        }
    }
}

?>