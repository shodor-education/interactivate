<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

$files = array();
$activities = getSdrResources("Activity");
while ($activity = $activities->fetch_assoc()) {
  $shortname = getShortname($activity);
  echo "FILENAME::$shortname\n";

  $query = <<<END
select Version.`content`
from Directory
left join DirectoryLink on DirectoryLink.`childId` = Directory.`id`
left join Resource on Resource.`canonParentId` = Directory.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Directory.`canonParentId` = 2202
and Resource.`name` = "learner"
and DirectoryLink.`shortName` = "$shortname"
END;
  $versions = $snap2DbConn->query($query);
  $version = $versions->fetch_assoc();
  $content = $version["content"];

  $result = xmlToHtmlWithFiles(
    $content,
    $shortname,
    "activities/learner",
    "learner",
    $files
  );
  $content = $result[0];
  $files = $result[1];
  echo "$content\n";
}
foreach ($files as $name => $path) {
  echo "$name,$path\n";
}

?>

