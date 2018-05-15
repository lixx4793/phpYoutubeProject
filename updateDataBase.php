<?php
if(isset($_GET["pid"]) && isset($_GET["index"]))
{
  $publishedid = $_GET["pid"];
  $index = $_GET["index"];
  $divName = $_GET['divName'];
  require ("databaseFunc.php");
  $text = updateURL($publishedid, $index, $divName);
}

  ?>
