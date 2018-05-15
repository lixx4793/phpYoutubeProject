<?php
require ("databaseFunc.php")
$targetSql = "select distinct movie_details_developments.title,year,studio,
movie_details_developments.publishedid,movie_details_developments.section_id,
section_name,count(*) as instances from CategoryListItem
INNER JOIN ListMovies on categoryID=category_id
INNER JOIN movie_details_developments on publishedid=moviePublishedID
INNER JOIN sections on sections.id=movie_details_developments.section_id
where publisher_id is NOT NULL and category_list_id = 3
GROUP BY publishedid
ORDER BY count(*) DESC;";

$items = searchData($targetSql);
insertData($items);
?>