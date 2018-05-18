
<?php
require ("localConfig.php");
$conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
  or die("Unable to connect to localDatabase");
$selected = mysqli_select_db($conn, $db_nameL)
  or die("Could not select youtubevideos");


$section = 1;     // Defalut section is movie
$page = 1;        //Defalut page is 1
$resultPerPage = 15;  // How many item will be displayed in one page
$filter = 1;    // Status filter



if(isset($_GET["section"]))
{
  $section = $_GET["section"];
}
if(isset($_GET["page"]))
{
  $page = $_GET["page"];
}
if(isset($_GET["filter"]))
{
  $filter = $_GET["filter"];
}

// The result between min and max will be shown
$min = ($page - 1) * $resultPerPage + 1;
$max = $page * $resultPerPage;

if(isset($_GET['searching']))
{
  $sql = "SELECT * from `videos2` where `publishedid` = " . ($_GET['searching']) . ";";
}
else
{
  $sql = "SELECT `publishedid` from `videos2` where `section_id` = ". $section . " and `flag` = " . $filter ." order by instances DESC";
}
$count = 1;
$sqlResult = mysqli_query($conn, $sql);
if(!$sqlResult)
{
  $htmlBody = "<font size = 10 color = 'red'>invalid input of searching, only allowed number</font>";
}
else
{
  $num = mysqli_num_rows($sqlResult);
  $showPages = ($num / 5) / $resultPerPage + 1;
  $pidArray = array();

  //   Generating pages ---------------------------------------------------------------
  $pageSelector ="<div class = 'pageSection'>";
  for($p = 1; $p <= $showPages; $p++)
  {
    // current page
    if($p == $page)
    {
      $pageSelector .= <<<END
      <input type = "button"  class = "page" value = $page id = "currentPage" onclick ="updatePage($p, $section)"> </input>
END;
    }
    else
    {
      $pageSelector .= <<<END
      <input type = "button"  class = "page" value = $p onclick ="updatePage($p, $section)" > </input>
END;
    }
  }
  $pageSelector .= "</div>";

  // Generating Tool bar  -------------------------------------------------------
  $toolbar =<<<END
    <div class = 'toolbar'>
    <input type = "button"  value = On  class = "filter" onclick = "chooseFilter(1, $page, $section)"></input>
    <input type = "button"  value = OFF class = "filter" onclick = "chooseFilter(0, $page, $section)"></input> <font> <b> - Filter Status </b></font>
      <input type = "text" placeholder="Search By Item Id or Item Name" style = "margin-left: 18%;width:30%;border: 3px solid" id = "sea"> </input>
      <input type = "button" value = "Search" class = "filter" style="width:10%" onclick = "searchId('sea')"></input>
    </div>
END;


  // Generating Session tags----------------------------------------------------------------------
  $htmlBody =<<<END
  <div class = "head" style="text-align:center; margin-bottom:1em">
      <div class = "category" onclick=changeSection(1)> <font size = "6"> Movie </font> </div>
      <div class = "category" onclick=changeSection(2)>  <font size = "6"> TV </font> </div>
      <div class = "category" onclick=changeSection(4)>  <font size = "6"> Music </font> </div>
      <div class = "category" onclick=changeSection(5)>  <font size = "6"> Books </font> </div>
      <div class = "category" onclick=changeSection(6)>  <font size = "6"> Game </font> </div>
      <div class = "category" onclick=changeSection(8)>  <font size = "6"> Podcast </font> </div>
      <div class = "category" onclick=changeSection(3)>  <font size = "6"> Travel </font> </div>
      <div class = "category" onclick=changeSection(11)>  <font size = "6"> Restaurant </font> </div>
  </div>
  <div class = "head2">
  $toolbar
  $pageSelector
  </div>
END;

  $indexResult = 1;
  while($count <= $resultPerPage )
  {
    if($indexResult < $min)
    {
      // Ignore this row
      $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
      $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
      $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
      $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
      $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
      $indexResult++;
      continue;
    }
    else if($indexResult > $max)
    {
      break;
    }
    else
    {
    $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
    if($row["publishedid"] == null)
    {
      $count++;
      continue;
    }
    $htmlBody .= <<<END
          <div class = "container-row" id = "container$count" >
END;
    // make sure the video is not been shown before
    if(in_array($row['publishedid'], $pidArray))
    {
      continue;
    }
    else
    {
        // push to pidArray, and generate html content to htmlbody
        array_push($pidArray, $row['publishedid']);
        $sqlPid = "SELECT * FROM `videos2` WHERE `publishedid` = " . $row['publishedid'] . " order by `priority` ASC;";
        $search = mysqli_query($conn, $sqlPid);
        for($i = 0; $i < 5; $i++)
        {
          //  five results will be returned from database for same publishedid
          $sameItem =  mysqli_fetch_array($search, MYSQLI_ASSOC);
          $htmlBody .= <<<END



            <div class = "blk" >
              <a href = "{$sameItem['videoUrl']}" class="html5lightbox">
                <div class="container-image">
                  <img src="{$sameItem['imgUrl']}" width = "16%" alt="image lost">
                  <div class="middle">
                    <div class="text">  {$sameItem['title']}   <br><br>channel: {$sameItem['channel']}</div>
                  </div>
                  <div class = "info">
                    <div class = "text2">
                    <font size = "2" color = "black">{$sameItem['videoTitle']}</font><br>
                    <font size = "3.5" color = "#4CAF50" ><b>channel: {$sameItem['channel']}</b></font>
                    </div>
                  </div>
                </div>
              </a>
            </div>
END;
        }

      // Determine the status of item
        if($filter == 1)
        {
          $StatusControl =<<<END
          <br>
          <span>Status: <font color = "green" size = "4"> Open </font> </span>
          <input type = "button" onclick="updateStatus(1, {$row['publishedid']})" value = "Update"> </input>
END;
        }
        else
        {
          $StatusControl =<<<END
          <br>
          <span>Status: <font color = "red" size ="4"> Closed </font></span>
          <input type = "button" onclick="updateStatus(0, {$row['publishedid']})" value = "Update"> </input>
END;
        }

      $htmlBody .= <<<END

            <div class = "container-right1">
            <input type = "text" value = 1 class = "inputL"></input>
            <input type = "button" value = "update" class = "inputL2"
              onclick = "reloadContent('container$count', {$row['publishedid']})"> </input>
          </div>

          <div class = "container-right">
            <span>Changing - Index:</span> <input type = "text" value = 0 class = "manuIndex"></input>
            <span class = "span2">Image URL:</span> <input type = "text" value = "" class = "manuImg" placeholder = "image URL"> </input>
            <span class = "span2">Video URL:<input type = "text" value ="" class = "manuVideo" placeholder = "Video URLs "> </input>
            <input type = "button" value = "modify"  class = "manuS"
              onclick = "manUpdate('container$count', {$row['publishedid']})"> </input>
            <br>$StatusControl
          </div>
          <br>
          <br>
    </div>
END;
      }
      $htmlBody .= "</div>";
      $count++;
      $indexResult++;
    }
}
}

?>



<!DOCTYPE html>
<html>
  <head>
    <title>youtube api</title>
     <link href="playGround.css" rel="stylesheet">
     <!-- <script type="text/javascript" src="html5lightbox/jquery.js"></script> -->
     <script type="text/javascript" src="html5lightbox/html5lightbox.js"></script>


<script>

function searchId(divName)
{
  var div = document.getElementById(divName);
  window.location.href = "playGround.php?searching=" + div.value;
}


function chooseFilter(flag, page, section)
{
    window.location.href = "playGround.php?section="+section+"&page="+page+"&filter="+flag;
}



function updatePage(page, section)
{
  var s1 = "playGround.php?page="+page;
  var s2 = "&section="+section;
  window.location.href = s1 + s2;
}


function changeSection(section)
{
  window.location.href = "playGround.php?section="+section;
}


  function updateStatus(status, publishedid)
 {
   if (window.XMLHttpRequest)
   {
     xmlhttp = new XMLHttpRequest();
   }
   else
   {  // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
   }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status==200) {
      if(xmlhttp.responseText != "")
      {
        alert(xmlhttp.responseText);
      }
      window.location.reload(false);
    }
  }
      //  Go to update.php which updates database , and return new content
  xmlhttp.open("GET","updateDataBase.php?publishedid="+publishedid+"&status="+status,true);
  xmlhttp.send();
  }


// Allow user to update url manually from input
  function manUpdate(div, pubId)
  {
   var doc = document.getElementById(div);
   var index = doc.getElementsByClassName("manuIndex")[0].value;
   var imgURL = doc.getElementsByClassName("manuImg")[0].value;
   var videoURL = doc.getElementsByClassName("manuVideo")[0].value;
    if(index > 5 || index <= 0)
    {
     alert("The index modifying is invaild");
     return;
    }
    if(imgURL == "" && videoURL == "") return;
    if (window.XMLHttpRequest)
    {
     xmlhttp = new XMLHttpRequest();
    }
    else
    {  // code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
      if (xmlhttp.readyState == 4 && xmlhttp.status==200) {
      if(xmlhttp.responseText != "")
      {
        alert(xmlhttp.responseText);
      }
      window.location.reload(false);
    }
  }
  if(imgURL == "")imgURL=" ";
  if(videoURL == "")videoURL = " ";
    //  Go to update.php which updates database , and return new content
  xmlhttp.open("GET","updateDataBase.php?index="+index+"&pid="+pubId+"&divName="+div+"&imgURL="+imgURL+"&videoURL="+videoURL,true);
  xmlhttp.send();
 }


 function reloadContent(div, pubId) {
  var doc = document.getElementById(div);
  var index = doc.getElementsByClassName("inputL")[0].value;
  if(index == 1)
  {
   // The priority does not need to be changed
   return;
  }
 // Make sure the index is valid
  else if(index > 5 || index <= 0)
  {
   alert("The index should from 1 - 5 (1 will update noting)");
   return;
  }
  if (window.XMLHttpRequest) {
    xmlhttp = new XMLHttpRequest();
  } else {  // code for IE6, IE5
    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
  if (xmlhttp.readyState == 4 && xmlhttp.status==200) {
    if(xmlhttp.responseText != "")
    {
      alert(xmlhttp.responseText);
    }
      window.location.reload(false);
  }
}      //  Go to update.php which updates database , and return new content
    xmlhttp.open("GET","updateDataBase.php?index="+index+"&pid="+pubId+"&divName="+div,true);
    xmlhttp.send();
}
</script>
  </head>


  <body>
    <?= $htmlBody ?>
  </body>
</html>
