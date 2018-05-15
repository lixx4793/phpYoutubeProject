
<?php
require ("localConfig.php");
$conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
  or die("Unable to connect to localDatabase");
$selected = mysqli_select_db($conn, $db_nameL)
  or die("Could not select youtubevideos");
$sql = "SELECT * from `videos` where `section_id` = 4;";
$htmlBody =<<<END
  <form method="post" style=" margin-left: 5%">
END;
$count = 1;
$sqlResult = mysqli_query($conn, $sql);

// delete loading timer by changing video iframe to image
// manually change url
// more information
// on and off flag -- change structre of table


while($count <= 6)
{
  $row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC);
  $htmlBody .= <<<END

    <div class = "container-row" id = "container$count">
      <div class = "container">
        <div class = "blk">
          <iframe src="{$row['pri1']}" width = "16%">
          </iframe>
        </div>
        <div class = "blk">
          <iframe src="{$row['pri2']}" width = "16%">
          </iframe>
        </div>
        <div class = "blk">
          <iframe src="{$row['pri3']}" width = "16%">
          </iframe>
        </div>
        <div class = "blk" >
          <iframe  src="{$row['pri4']}" width = "16%">
          </iframe>
        </div>
        <div class = "blk" >
          <iframe  src="{$row['pri5']}" width = "16%">
          </iframe>
        </div>
      </div>
      <div class = "container-right">
        <input type = "text" name = "newFPri" value = 1 class = "inputL"></input>
        <input type = "button" value = "update" class = "inputL2"
        onclick = "reloadContent('container$count', {$row['publishedid']})"> </input>
      </div>
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
     <link href="playGround.css" rel="stylesheet">
     <script>
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
          // uncomment for debug
          doc.innerHTML = xmlhttp.responseText;
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
