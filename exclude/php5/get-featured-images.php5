<?php
header("Content-Type: text/plain");
include_once("helpers.php5");
$query = <<<END
select Version.`content`
from Version
left join ResourceLink on ResourceLink.`childId` = Version.`resourceId`
where ResourceLink.`parentId` = 5352
and Version.`status` = 3
order by ResourceLink.`shortName`
END;

$results = $snap2DbConn->query($query);
while ($result = $results->fetch_assoc()) {
  preg_match(
    "#http://shodor.org/interactivate/activities/(.+)\" #"
  , $result["content"]
  , $matches
  );
  $shortname = str_replace("/", "", $matches[1]);
  preg_match(
    "#snapid=\"(.+)\"#"
  , $result["content"]
  , $matches
  );
  $query = <<<END
select Version.`content`
from Version
where Version.`resourceId` = $matches[1]
and Version.`status` = 3
END;
  $results2 = $snap2DbConn->query($query);
  while ($result2 = $results2->fetch_assoc()) {
    $json = json_decode($result2["content"]);
    $url = "http://shodor.org/media/"
    . $json->original->path;
  }
  echo "$shortname.jpg,$url\n";
}

?>

