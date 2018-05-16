
<?php
require ("localConfig.php");
$conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
  or die("Unable to connect to localDatabase");
$selected = mysqli_select_db($conn, $db_nameL)
  or die("Could not select youtubevideos");
$sql = "SELECT * from `videos` where `section_id` = 1;";
$htmlBody =<<<END
  <form method="post" style=" margin-left: 5%">
END;
$count = 1;
$sqlResult = mysqli_query($conn, $sql);

// delete loading timer by changing video iframe to image
// manually change url
// more information
// on and off flag -- change structre of table


while($count <= 100)
{
  $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
  if($row["pri1"] == null)continue;
  $htmlBody .= <<<END
    <div class = "container-row" id = "container$count">
      <div class = "container">
        <div class = "blk">
          <a href = "{$row['pri1']}" class="html5lightbox"> <img src="{$row['pri1Img']}" width = "17%" alt="image lost"> </a>
        </div>
        <div class = "blk">
          <a href = "{$row['pri2']}" class="html5lightbox"> <img src="{$row['pri2Img']}" width = "17%" alt="image lost"> </a>
        </div>
        <div class = "blk">
        <a href = "{$row['pri3']}" class="html5lightbox">  <img src="{$row['pri3Img']}" width = "17%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
        <a href = "{$row['pri4']}" class="html5lightbox">  <img  src="{$row['pri4Img']}" width = "17%" alt="image lost"> </a>
        </div>
        <div class = "blk" >
        <a href = "{$row['pri5']}" class="html5lightbox">  <img  src="{$row['pri5Img']}" width = "17%" alt="image lost"> </a>
        </div>
      </div>
      <div class = "container-right">
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

        <br> <font size='4' color='red'> *** Leave the URL blank if you don't want to update that section" </font>
      </div>
      <br>
      <br>
    </div>
END;
  $count++;
}
  $htmlBody .= "</form>";

?>


<!DOCTYPE html>
<html>
  <head>
    <title>youtube api</title>
     <link href="playGround3.css" rel="stylesheet">
     <!-- <script type="text/javascript" src="html5lightbox/jquery.js"></script> -->
     <script type="text/javascript" src="html5lightbox/html5lightbox.js"></script>

     <script>

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
       if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }

      xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status==200) {
          //TODO: Regenerate the content for document.ID to xmlhttp.responseText
          // alert(xmlhttp.responseText+"...");
           // Reactivate The light box after ajax loading content
          doc.innerHTML = xmlhttp.responseText;
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
         alert("The index should from 0 - 5");
         return;
       }

       if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {  // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }

      xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status==200) {
          //TODO: Regenerate the content for document.ID to xmlhttp.responseText
          // console.log(xmlhttp.responseText+"...");
          // Reactivate The light box after ajax loading content
          doc.innerHTML = xmlhttp.responseText;
          window.location.reload(false);
        }
      }

      //  Go to update.php which updates database , and return new content
      xmlhttp.open("GET","updateDataBase.php?index="+index+"&pid="+pubId+"&divName="+div,true);
      xmlhttp.send();
     }
     </script>
  </head>
  <body>
    <?= $htmlBody ?>
  </body>
</html>
