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

function updateURL($pid, $index, $divName)
{
  require ("localConfig.php");
  $conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($conn, $db_nameL)
    or die("Could not select youtubevideos");
  $sqlO1 = "SELECT `pri1` from videos WHERE `publishedid`=".$pid.";";
  $sqlOn = "SELECT `pri" . $index . "` from videos WHERE `publishedid`=" . $pid . ";";
  // Get the first URL value
  $Opri1 = mysqli_fetch_array((mysqli_query($conn, $sqlO1)), MYSQLI_ASSOC);
  $Oprin = mysqli_fetch_array((mysqli_query($conn, $sqlOn)), MYSQLI_ASSOC);
  // update query
  $sql = "UPDATE `videos` SET pri1 = " . "'". $Oprin['pri'.$index] . "'" . ", " . "pri" . $index .
  " = ". "'" . $Opri1['pri1'] . "'" . " WHERE `publishedid` = " . $pid . ";";
  $updateResult = mysqli_query($conn, $sql);
  $newContent = "";
  if(!$updateResult)
  {
    $newContent = "<font size='4' color='red'>Unable to update database </font>";
  }
  else
  {
    $u1 =  $Oprin['pri'.$index];
    $u2 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri2` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri2'];
    $u3 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri3` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri3'];
    $u4 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri4` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri4'];
    $u5 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri5` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri5'];
    // re-Generate the context
    $newContent =<<<END
    <div class = "container">
        <div class = "blk">
          <iframe src="$u1" width = "16%">
          </iframe>
        </div>
        <div class = "blk">
          <iframe src="$u2" width = "16%">
          </iframe>
        </div>
        <div class = "blk">
          <iframe src="$u3" width = "16%">
          </iframe>
        </div>
        <div class = "blk" >
          <iframe  src="$u4" width = "16%">
          </iframe>
        </div>
        <div class = "blk" >
          <iframe  src="$u5" width = "16%">
          </iframe>
        </div>
      </div>
      <div class = "container-right">
        <input type = "text" name = "newFPri" value = 1 class = "inputL"></input>
        <input type = "button" value = "update" class = "inputL2" onclick = "reloadContent('$divName', $pid )"> </input>
      </div>
END;
  }
  $conn->close();
  echo $newContent;
}


//  Get urls for each element from tribalist query reuturn, and insert them into local database (if the element not exist)
function insertData($queryReturn)
{
  require ("youtubeApi.php");
  require ("localConfig.php");
  // Connect to local database ( Change table and don't need to reconnect to database again if the table in Tribalist)
  $dbhandle = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($dbhandle, $db_nameL)
    or die("Could not select youtubevideos");

  //  Go through query return
  $count = 0;
  while ($row = mysqli_fetch_array($queryReturn, MYSQLI_ASSOC))
  {
    if($count > 10) break;
    $searchingArray = [
      "name" => $row['title'],
      "section_id" => $row['section_id'] ,
      "author" => $row["studio"],
      "year" => $row["year"],
    ];
    // The element is not in table videos
    $checkSql = "select title from videos where publishedid = ".$row['publishedid'];
    $flag = mysqli_fetch_array((mysqli_query($dbhandle, $checkSql)), MYSQLI_ASSOC);

    if(!$flag['title'])
    {
      //  get url of the videos for this item.
      $urls = getUrls($searchingArray);
      //  insert values into database
      $insertSql = "INSERT INTO videos (`title`, `publishedid`, `section_id`, `instances`,
      `pri1`, `pri2`, `pri3`, `pri4`, `pri5`) VALUES ". "(" . "'" . $row['title']. "'" ."," . $row['publishedid'].
      "," . $row['section_id'] . "," . $row['instances'] . ", " . "'" . $urls[0] . "'". ", " . "'". $urls[1] . "'" . ", " .
      "'" . $urls[2] . "'" . ", " . "'" . $urls[3] .  "'" . ", " . "'". $urls[4] . "'" . ");";
      $inst = mysqli_query($dbhandle, $insertSql);

    }
    else
    {
      // The element is already in the table, notify user and let them update through other tool.
      echo "<font size='4' color='red'>" . "The movie" . " '" .
      $row['title'] . "' " . " is already existed, plz update manually" . "</font><br>";

    }
   $count++;
 }
 $dbhandle->close();
}





?>
