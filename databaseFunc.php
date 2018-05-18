<?php
ini_set('display_errors', 'On');

function searchData($sql)
{
  require ("db_config.php");
  //  Connect to tribalist database
  $dbhandle = mysqli_connect($hostname, $username, $password, $db_name)
    or die("Unable to connect to Tribalist");
  $selected = mysqli_select_db($dbhandle, $db_name)
    or die("Could not select Fabric");
  $result = mysqli_query($dbhandle, $sql);
  $dbhandle->close();
  return $result;
}



function updateManu($pid, $index, $divName, $imgURL, $videoURL)
{
  require ("localConfig.php");
  $conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($conn, $db_nameL)
    or die("Could not select youtubevideos");
    $pass = true;
    $error = "";
    $imgURL = str_replace(" ", "", $imgURL);
    $videoURL = str_replace(" ", "", $videoURL);
    if($imgURL != "")
    {
      //update imgURL in index n
      $sqlImg = "UPDATE `videos2` SET `imgUrl` = " . "'" . $imgURL . "'" . " WHERE `publishedid` = " . $pid . " and `priority` = " . $index .";";
      $pass = ($pass && mysqli_query($conn, $sqlImg));
      if(!$pass)  $error .= "Unable to update image url<br>";
    }
    if($videoURL != "")
    {
      // update video in index n
      $sqlVideo = "UPDATE `videos2` SET `videoUrl` = " . "'" . $videoURL . "'" . " WHERE `publishedid` = " . $pid . " and `priority` = " . $index .";";
      $pass = ($pass && mysqli_query($conn, $sqlVideo));
      if(!$pass) $error .= "Unable to update video url<br>";
    }
    echo $error;
  $conn->close();
}



function updateURL($pid, $index, $divName)
{
  require ("localConfig.php");
  $conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($conn, $db_nameL)
    or die("Could not select youtubevideos");
  // Change the priority with 1 and n
  $P10 = "UPDATE `videos2` SET `priority` = -1 WHERE `priority` = 1 and `publishedid` =" . $pid . ";";
  $Pn1 = "UPDATE `videos2` SET `priority` = 1 WHERE `priority` = " . $index . " and `publishedid` =" . $pid .";";
  $P0n = "UPDATE `videos2` SET `priority` = " . $index . " WHERE `priority` = -1 and `publishedid` =" . $pid . ";";
  $error = "";
  if(!mysqli_query($conn, $P10)) $error .= "<font color = 'red' size ='3' > Unable to change priority 1 to -1 for: ". $pid . "</font><br>";
  if(!mysqli_query($conn, $Pn1)) $error .= "<font color = 'red' size ='3' > Unable to change priority ". $index . " to 1 for: ". $pid . "</font><br>";
  if(!mysqli_query($conn, $P0n)) $error .= "<font color = 'red' size ='3' > Unable to change priority -1 to " . $index . " for: ". $pid . "</font><br>";
  if($error == "")
  {
    echo "";
  }
  else
  {
    echo $error;
  }
  $conn->close();
}



//    Set the flag to $flag where publishedid  = pid
function updateFlag($flag, $pid)
{
  require ("localConfig.php");
  $conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($conn, $db_nameL)
    or die("Could not select youtubevideos");

  if($flag == 1)
  {
    $flag = 0;
  }
  else
  {
    $flag = 1;
  }
  $sql = "UPDATE `videos2` SET `flag` = " . $flag . " WHERE `publishedid` = " . $pid . ";";
  $error ="";
  if(!mysqli_query($conn, $sql)) $error .= "Unable to change the status of " . $pid ;
  echo $error;


}







//  Get urls for each element from tribalist query reuturn, and insert them into local database (if the element not exist)
function insertData($queryReturn)
{
  $record = "";
  require ("youtubeApi.php");
  require ("localConfig.php");
  // Connect to local database ( Change table and don't need to reconnect to database again if the table in Tribalist)
  $dbhandle = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($dbhandle, $db_nameL)
    or die("Could not select youtubevideos");

  //  Go through query return
  $count = 0;
  $record = "";
  while ($row = mysqli_fetch_array($queryReturn, MYSQLI_ASSOC))
  {
    if($count > 500) break;
    $searchingArray = [
      "name" => $row['title'],
      "section_id" => $row['section_id'] ,
      "author" => $row["studio"],
      "year" => $row["year"],
    ];
    // The element is not in table videos
    $checkSql = "select title from videos2 where publishedid = ".$row['publishedid'];
    $flag = mysqli_fetch_array((mysqli_query($dbhandle, $checkSql)), MYSQLI_ASSOC);

    if(!$flag['title'])
    {
      //  get url of the videos for this item.
      $urls = getUrls($searchingArray);

      //  If url array is empty, break the loop
      if(!$urls || $urls == 0) continue;


      //  insert values into database
      for( $order = 1; $order <= 5 ; $order++)
      {
        $insertSql = "INSERT INTO `videos2` (`title`, `publishedid`, `section_id`, `instances`,
           `videoUrl`, `imgURL`, `priority`, `channel`, `videoTitle`) VALUES ". "(" . '"' . $row['title']. '"' ."," . $row['publishedid'].
           "," . $row['section_id'] . ", " . $row['instances'] . ", " . '"' . $urls[($order - 1) * 4] . '"' . "," .
           '"' . $urls[($order - 1) * 4 + 1] . '"' . "," . $order .  ", " . '"' . $urls[($order - 1) * 4 + 2] . '"' . ", " . '"' .
           $urls[($order - 1) * 4 + 3] . '"' . ");";
        if(!mysqli_query($dbhandle, $insertSql))
        {
          $record .= "<font size='4' color='blue'>
          Unable to operate ". $insertSql ."</font><br>";
        }
        else
        {
          $record .=" <font size='4'> item " . $row['title'] . " priority " . $order . "Successfully inserted-------- </font><br>";
        }
      }
      $record .= "<br>";
    }
    else
    {
      // The element is already in the table, notify user and let them update through other tool.
      $record .= "<font size='4' color='red'>" . "The movie" . " '" .
      $row['title'] . "' " . " is already existed, plz update manually" . "</font><br>";
    }
   $count++;
 }
 $dbhandle->close();
 return $record;
}





?>
