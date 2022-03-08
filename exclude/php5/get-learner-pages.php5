<?php
header("Content-Type: text/plain");

$IMAGE_DIR="activities/learner";
$IMAGE_INFIX="learner";
$INTERACTIVATE_TYPE="Activity";

include_once("passwords.php5");
include_once("get-snap2-content-with-images.php5");

$images = array();
$activities = $sdrDbConn->query($query);
while ($activity = $activities->fetch_assoc()) {
  echo "FILENAME::$activity[shortname]\n";

  $query = <<<END
select Version.`content`
from Directory
left join DirectoryLink on DirectoryLink.`childId` = Directory.`id`
left join Resource on Resource.`canonParentId` = Directory.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Directory.`canonParentId` = 2202
and Resource.`name` = "learner"
and DirectoryLink.`shortName` = "$activity[shortname]"
END;

  $versions = $snap2DbConn->query($query);

  $images = echoContentAndGetImages(
    $versions,
    $activity["shortname"],
    $images
  );
}
foreach ($images as $name => $path) {
  echo "$name,$path\n";
}

?>

