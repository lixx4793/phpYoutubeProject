<?php
define("KEY", "AIzaSyAnLOcYLEvkTBqoB7hIrMkCV3-ocGIu3CQ");
ini_set('display_errors', 'On');
require ("db_config.php");
$arrContextOptions=array(
    "ssl"=>array(
        "verify_peer"=>false,
        "verify_peer_name"=>false,
    ),
);

//    input form
$form = <<<END
<form method="GET" style="text-align:center; margin-bottom:6em">
  <h1> Search Video  Here</h1>
  <div style = "margin: 1em">
    Search Term: <input type="search" id="q" name="q" placeholder="Uncoment 118 to Search here" >
  </div>
  <div style = "margin: 1em">
    Max Results: <input type="number" id="maxResults" name="maxResults" min="1" max="12" step="1" value="4">
  </div>
  <input type="submit" value="Search">
</form>
END;

 $dbhandle = mysqli_connect($hostname, $username, $password, $db_name) or die("Unable to connect to MySQL");
 $selected = mysqli_select_db($dbhandle, $db_name)
  or die("Could not select Fabric");

 $sql="select distinct movie_details_developments.title,year,studio,movie_details_developments.section_id,section_name,count(*) as instances from CategoryListItem
INNER JOIN ListMovies on categoryID=category_id
INNER JOIN movie_details_developments on publishedid=moviePublishedID
INNER JOIN sections on sections.id=movie_details_developments.section_id
where publisher_id is NOT NULL and category_list_id = 3
GROUP BY publishedid
ORDER BY count(*) DESC;";
$result = mysqli_query($dbhandle, $sql);
$count = 1;

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
 {
   if($count > 10) break;
   $array = [
     "name" => $row['title'],
     "section_id" => $row['section_id'] ,
     "author" => $row["studio"],
     "year" => $row["year"],
   ];
   $content = getVideos($array, $arrContextOptions);
   echo $content;
   $count++;
 }



function getVideos($arr, $option)
{
$htmlBody = "";
$section_Id =$arr['section_id'];
$name = $arr['name'];
$author = $arr['author'];
$publishedTime = $arr['year'];
// The format is, Name + sectionSpecify + (optional: author) + (optional: location) + (optional: published time);
$query = "";


if($section_Id == 1 || $section_Id == 2)    //For movie & TV query = name + specify + time, specify = trailer
{
  $query .= $name." trailer";
  $query .= " ".$publishedTime;

}
else if( $section_Id == 3)    // For travel query = name + specify
{
  $query .= $name." travel guide";
}
else if( $section_Id == 4)  // For music query = name + specify + author  + time
{
  $query .= $name. " music";
  $query .= " by ".$author;
  $query .= " ".$publishedTime;

}
else if($section_Id == 5)     // For books query = name + specify + author
{
  $query .= $name;
  $query .= " by ".$author;
  $query .= "book review";
}
else if($section_Id == 8)    // For games query = name + specify
{
  $query .= $name." gameplay";
}
else if($section_id == 8)     // For podcast query = name + specify
{
  $query .= $name." podcast";
}
else if($section_Id == 11)    // For restaurants query = name + specify + location
{
  $query .= $name. " restaurant";
}
else          // default
{
  $query .= $name;
}


if (isset($_GET["q"]) && isset($_GET["maxResults"]))
{
  // $q = $_GET['q'];       // uncomment this to search according to form input
  $q = $query;              //  search by given value (name, sectionid, author .....)
  $q = str_replace(" ", "%20", $q);
  $max = $_GET['maxResults'];

// Initialize URL by adding query key words and max result
$url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".
$q."&maxResults=".$max.
"&type=video&key=".KEY;

$contents = file_get_contents($url, false, stream_context_create($option));
$response = json_decode($contents, true);
echo $url;
return;

$videoList= "";
$videoLink = "https://www.youtube.com/embed/";


// Add video to $video
foreach($response["items"] as $video)
{
  $links = "";
  $links.= $videoLink.$video["id"]["videoId"];

  $videoList .= <<<END
    <div  class="col-lg-3 col-md-4 col-xs-6">
      <iframe width="70%" height="100%" class="img-thumbnail" src=$links>
      </iframe>
    </div>
END;
}

//  Add $video to a div container
  $htmlBody .= <<<END
   <div class="row text-center text-lg-left"> $videoList </div>
END;
}
return $htmlBody;
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
    <?=$form?>
  </body>
</html>
