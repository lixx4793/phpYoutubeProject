<?php
  define("KEY", "REPLACE YOUR KEY");
  ini_set('max_execution_time', 36000);
  function getUrls($searchingArray)
  {
    //  SSL error handler for file_get_contents  function
    $option = array (
        "ssl" => array (
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    $result = array();
    $section_Id = $searchingArray['section_id'];
    $name = $searchingArray['name'];
    $author = $searchingArray['author'];
    $publishedTime = $searchingArray['year'];
    // The format is, Name + sectionSpecify + (optional: author) + (optional: location) + (optional: published time);
    $query = "";

    // Handle query statement according to input array
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
    else if($section_Id == 6)    // For games query = name + specify
    {
      $query .= $name." gameplay";
    }
    else if($section_Id == 8)     // For podcast query = name + specify
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

    //  Sent request to youtube api
    $max = 10;
    //  format correction
    if($query == " ") return 0;
    $query = str_replace(" ", "%20", $query);
    $url = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=".
    $query."&maxResults=".$max.
    "&type=video&key=".KEY;
    //  Read Json result returned and read through it
    if( ! ($contents = file_get_contents($url, false, stream_context_create($option))) ) return null;
    $response = json_decode($contents, true);
    $videoLink = "https://www.youtube.com/embed/";
    $count = 1;
    //  push the link to result array
    if(!$response) return null;
    foreach($response["items"] as $video)
    {
      if($count <= 5)
      {
      // array format videoLink + img, videoLink + img
      if(($video["snippet"]["title"]) == "" || ($video["snippet"]["title"]) == null) return null;
      $chan = str_replace('"', "", $video["snippet"]["channelTitle"]);
      $tit = str_replace('"', "", $video["snippet"]["title"]);
      array_push($result, $videoLink.$video["id"]["videoId"]);
      array_push($result, $video["snippet"]["thumbnails"]["high"]["url"]);
      // CommentOut to use original version
      array_push($result, $chan);
      array_push($result, $tit);
      $count++;
      }
    }
    if($count <= 5)
    {
      array_push($result, "no resource");
      array_push($result, "no resource");
      // CommentOut to use original version
      array_push($result, "no resource");
      array_push($result, "no resource");
      $count++;
    }
    return $result;
  }

 ?>
