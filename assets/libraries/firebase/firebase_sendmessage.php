<?
include_once('../configuration.php');
require_once('src/firebaseInterface.php');
require_once('src/firebaseLib.php');

$DEFAULT_URL = 'https://kidsplace.firebaseio.com/';
$DEFAULT_TOKEN = 'MqL0c8tKCtheLSYcygYNtGhU8Z2hULOFs9OKPdEp';
$DEFAULT_PATH = '/firebase/example';

$firebase = new \Firebase\FirebaseLib($DEFAULT_URL, $DEFAULT_TOKEN);

// --- storing an array ---
$test = array(
    "foo" => "bar",
    "i_love" => "lamp",
    "id" => 42
);
//$dateTime = new DateTime();
//$firebase->set(DEFAULT_PATH . '/' . $dateTime->format('c'), $test);
$dateTime = date("Y-m-d H:i:s");
$firebase->set($DEFAULT_PATH . '/' . $dateTime, $test);

// --- storing a string ---
$firebase->set($DEFAULT_PATH . '/name/contact001', "John Doe");

// --- reading the stored string ---
$name = $firebase->get($DEFAULT_PATH . '/name/contact001');
?>