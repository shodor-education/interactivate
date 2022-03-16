<?php
header("Content-Type: text/plain");
include_once("helpers.php5");
$query = <<<END
select TSDBook.`id`, TSDBook.`coverImage`
from TSDBook
order by TSDBook.`id`
END;
$results = $sdrDbConn->query($query);
while ($result = $results->fetch_assoc()) {
  $query = <<<END
select Version.`content`
from Version
where Version.`resourceId` = $result[coverImage]
and Version.`status` = 3
END;
  $results2 = $snap2DbConn->query($query);
  while ($result2 = $results2->fetch_assoc()) {
    $json = json_decode($result2["content"]);
    $url = "http://shodor.org/media/"
    . $json->original->path;
  }
  echo "$result[id].gif,$url\n";
}
?>

