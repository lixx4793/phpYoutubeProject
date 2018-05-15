<?php
  require ("localConfig.php");
  $htmlbody ="";
  $conn = mysqli_connect($hostnameL, $usernameL, $passwordL, $db_nameL)
    or die("Unable to connect to localDatabase");
  $selected = mysqli_select_db($dbhandle, $db_nameL)
    or die("Could not select youtubevideos");
  $sql = "select * from videos";
  while ($row = mysqli_fetch_array($queryReturn, MYSQLI_ASSOC))
  {



  }



 ?>

 <!DOCTYPE html>
 <html>
   <head>
     <title>youtube api</title>
     <link rel="stylesheet"
     href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
     integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
     crossorigin="anonymous">
      <link href="css/thumbnail-gallery.css" rel="stylesheet">
   </head>

   <body>
     <!-- Display view -->
     <?=$htmlBody?>
   </body>
 </html>
