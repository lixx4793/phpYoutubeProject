<?php
  require ("databaseFunc.php");
  if(isset($_GET['num']))
  {
    $keyword = $_GET['keyword'];
    $control = $_GET['num'];
    if($control == 0)
    {
      // $sql = "SELECT * from "

    }
    else
    {
          echo $keyword;
    }
  }



 ?>
 <html>
  <head>
    <title> Manually Insert </title>
    <script>
      function active(num)
      {
        if(num == 0)        // Insert one element
        {
          var key = document.getElementById("item").value;
          window.location.href = "manInsert.php?num=0&keyword="+key;
        }
        else                // Insert from a file
        {
          var key = document.getElementById("file").value;
          window.location.href = "manInsert.php?num=1&keyword="+key;
        }

      }

    </script>
  </head>

  <body style="text-align: center; margin-top: 10%">
    <div>
    <div>
      <input type = "text" placeholder="input name or publishedid to insert" id ="item" sytle = "margin-left: 2%"/ >
      <input type = "button" value = "insert" onclick = "active(0)"/>
    </div>

    <div>
      <input type = "text" placeholder="input name of file" id = "file" sytle = "margin-left: 2%"/>
      <input type = "button" value = "insert" onclick = "active(1)"/>
    </div>
    <div>

  </body>
</html>
