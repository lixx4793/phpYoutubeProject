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
      $sqlImg = "UPDATE `videos` SET pri".$index."Img = " . "'" . $imgURL . "'" . "WHERE publishedid = ". $pid;
      $pass = ($pass && mysqli_query($conn, $sqlImg));
      if(!$pass)  $error .= "Unable to update image url<br>";
    }
    if($videoURL != "")
    {
      // update video in index n
      $sqlVideo = "UPDATE `videos` SET pri".$index."= " . "'" . $videoURL . "'" . "WHERE publishedid = ". $pid;
      echo "video!!!!!!!!!!!!!!!";
      $pass = ($pass && mysqli_query($conn, $sqlVideo));
      if(!$pass) $error .= "Unable to update video url<br>";
    }
    if($pass)
    {
    // Regenerate content
    $u1 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri1` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri1'];
    $u2 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri2` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri2'];
    $u3 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri3` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri3'];
    $u4 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri4` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri4'];
    $u5 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri5` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri5'];
    $i1 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri1Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri1Img'];
    $i2 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri2Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri2Img'];
    $i3 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri3Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri3Img'];
    $i4 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri4Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri4Img'];
    $i5 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri5Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri5Img'];

    // re-Generate the context
    $newContent =<<<END
    <div class = "container">
        <div class = "blk">
          <a href = "$u1" class="html5lightbox"> <img src="$i1" width = "16%" alt="image lost" onmouseover="initBox(this)"> </a>
        </div>
        <div class = "blk">
          <a href = "$u2" class="html5lightbox"> <img src="$i2" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk">
          <a href = "$u3" class="html5lightbox"> <img src="$i3" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
          <a href = "$u4" class="html5lightbox"> <img src="$i4" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
          <a href = "$u5" class="html5lightbox"> <img src="$i5" width = "16%" alt="image lost"> </a>
        </div>
      </div>
      <div class = "container-right">
        <input type = "text" name = "newFPri" value = 1 class = "inputL"></input>
        <input type = "button" value = "update" class = "inputL2" onclick = "reloadContent('$divName', $pid )"> </input>
      </div>
      <div class = "container-right">
        <span>Changing - Index:</span> <input type = "text" value = 0 class = "manuIndex"></input>

        <span class = "span2">Image URL:</span> <input type = "text" value = "" class = "manuImg" placeholder = "image URL"> </input>

        <span class = "span2">Video URL:<input type = "text" value ="" class = "manuVideo" placeholder = "Video URLs "> </input>
        <input type = "button" value = "modify"  class = "manuS"
        onclick = "manUpdate('$divName', $pid )"> </input>
        <br> <font size='4' color='red'> *** Leave the URL blank if you don't want to update that section" </font>
      </div>
END;
echo $newContent;
  }
  else
  {
    echo $error;
  }


  $conn->close();
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
  $ImgSqlO1 = "SELECT `pri1Img` from `videos` WHERE `publishedid`=".$pid.";";
  $ImgSqlOn = "SELECT `pri" . $index . "Img" ."` from videos WHERE `publishedid`=" . $pid . ";";
  // Get the first URL value
  $Opri1 = mysqli_fetch_array((mysqli_query($conn, $sqlO1)), MYSQLI_ASSOC);
  $Oprin = mysqli_fetch_array((mysqli_query($conn, $sqlOn)), MYSQLI_ASSOC);

  //  Get the first image Link
  $OimgPir1 = mysqli_fetch_array((mysqli_query($conn, $ImgSqlO1)), MYSQLI_ASSOC);
  $OimgPrin = mysqli_fetch_array((mysqli_query($conn, $ImgSqlOn)), MYSQLI_ASSOC);

  // update url and img
  $sql = "UPDATE `videos` SET pri1 = " . "'". $Oprin['pri'.$index] . "'" . ", " . "pri" . $index .
  " = ". "'" . $Opri1['pri1'] . "'" . ", " . "pri1Img = " . "'" . $OimgPrin['pri'.$index.'Img'] . "'" . ", " .
  "pri" . $index . "Img = " . "'" . $OimgPir1['pri1Img'] . "'" . " WHERE `publishedid` = " . $pid . ";";
  $updateResult = mysqli_query($conn, $sql);
  $newContent = "";
  if(!$updateResult)
  {
    $newContent = "<font size='4' color='red'>Unable to update database </font>";
  }
  else
  {
    $u1 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri1` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri1'];
    $u2 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri2` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri2'];
    $u3 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri3` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri3'];
    $u4 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri4` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri4'];
    $u5 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri5` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri5'];
    $i1 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri1Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri1Img'];
    $i2 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri2Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri2Img'];
    $i3 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri3Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri3Img'];
    $i4 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri4Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri4Img'];
    $i5 = mysqli_fetch_array((mysqli_query($conn,"SELECT `pri5Img` from videos WHERE `publishedid`=".$pid.";")), MYSQLI_ASSOC)['pri5Img'];

    // re-Generate the context
    $newContent =<<<END
    <div class = "container">
        <div class = "blk">
          <a href = "$u1" class="html5lightbox"> <img src="$i1" width = "16%" alt="image lost" onmouseover="initBox(this)"> </a>
        </div>
        <div class = "blk">
          <a href = "$u2" class="html5lightbox"> <img src="$i2" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk">
          <a href = "$u3" class="html5lightbox"> <img src="$i3" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
          <a href = "$u4" class="html5lightbox"> <img src="$i4" width = "16%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
          <a href = "$u5" class="html5lightbox"> <img src="$i5" width = "16%" alt="image lost"> </a>
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
  $result = "";
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
    if($count > 100) break;
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

      //  If url array is empty, break the loop
      if(!$urls || $urls == 0) continue;


      //  insert values into database
      $insertSql = "INSERT INTO videos (`title`, `publishedid`, `section_id`, `instances`,
      `pri1`, `pri2`, `pri3`, `pri4`, `pri5`, `pri1Img`, `pri2Img`, `pri3Img`, `pri4Img`, `pri5Img`)
      VALUES ". "(" . "'" . $row['title']. "'" ."," . $row['publishedid'].
      "," . $row['section_id'] . "," . $row['instances'] . ", " . "'" . $urls[0] . "'". ", " . "'". $urls[2] . "'" . ", " .
      "'" . $urls[4] . "'" . ", " . "'" . $urls[6] .  "'" . ", " . "'". $urls[8] . "'" . ", " . "'" . $urls[1] . "'" .
      ", " . "'" . $urls[3] . "'" . ", " . "'" . $urls[5] . "'" . ", " . "'" . $urls[7] . "'" . ", " .
      "'" . $urls[9] . "'" . ");";
      $inst = mysqli_query($dbhandle, $insertSql);
      $result .= "Successfully Inserted --------------" . $insertSql . "<br><br>";
    }
    else
    {
      // The element is already in the table, notify user and let them update through other tool.
      $result .= "<font size='4' color='red'>" . "The movie" . " '" .
      $row['title'] . "' " . " is already existed, plz update manually" . "</font><br>";

    }
   $count++;
 }
 $dbhandle->close();
 return $result;
}





?>
