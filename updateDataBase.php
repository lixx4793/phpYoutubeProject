<?php
if(isset($_GET["pid"]) && isset($_GET["index"]))
{
  // This is userd to update manually
  if(isset($_GET["imgURL"])) {
    require ("databaseFunc.php");
    $publishedid = $_GET["pid"];
    $index = $_GET["index"];
    $divName = $_GET['divName'];
    $imgURL = $_GET['imgURL'];
    $videoURL = $_GET["videoURL"];
    echo updateManu($publishedid, $index, $divName, $imgURL, $videoURL);
  }
  else
  {     // This is used to update by exchange index
    $publishedid = $_GET["pid"];
    $index = $_GET["index"];
    $divName = $_GET['divName'];
    require ("databaseFunc.php");
    echo updateURL($publishedid, $index, $divName);
  }
}

  ?>
