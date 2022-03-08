<?php
header("Content-Type: text/plain");

$IMAGE_DIR="discussions";
$IMAGE_INFIX="discussion";
$INTERACTIVATE_TYPE="Discussion";

include_once("passwords.php5");
include_once("get-snap2-content-with-images.php5");

$images = array();
$discussions = $sdrDbConn->query($query);
while ($discussion = $discussions->fetch_assoc()) {
  echo "FILENAME::$discussion[shortname]\n---\n";

  // ALIGNED STANDARDS OBJECTIVES
  $query = <<<END
select TSDStandardAlignment.`objectiveId` as objectiveId
from TSDStandardAlignment
where TSDStandardAlignment.`version` = "LIVE"
and TSDStandardAlignment.`resourceId` = $discussion[resourceId]
order by TSDStandardAlignment.`objectiveId`
END;
  $results = $sdrDbConn->query($query);
  if ($results->num_rows > 0) {
    echo "aligned-standards-objectives:\n";
    while ($result = $results->fetch_assoc()) {
      echo "  - \"$result[objectiveId]\"\n";
    }
  }

  // ALIGNED TEXTBOOK SECTIONS
  $query = <<<END
select TSDTextbookAlignment.`sectionId` as sectionId
from TSDTextbookAlignment
where TSDTextbookAlignment.`version` = "LIVE"
and TSDTextbookAlignment.`resourceId` = $discussion[resourceId]
order by TSDTextbookAlignment.`sectionId`
END;
  $results = $sdrDbConn->query($query);
  if ($results->num_rows > 0) {
    echo "aligned-textbook-sections:\n";
    while ($result = $results->fetch_assoc()) {
      echo "  - \"$result[sectionId]\"\n";
    }
  }

  // AUDIENCES
  echo "audiences:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Interactivate_Audience"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // DESCRIPTION
  echo "description: \"";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Description"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // SHORTNAME
  echo "short-name: \"$discussion[shortname]\"\n";

  // SUBJECTS
  echo "subjects:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Primary_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  // TITLE
  echo "title: \"";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Title"
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";

  // TOPICS
  echo "topics:\n";
  $results = getTextValues(
    $sdrDbConn,
    $discussion["versionId"],
    "Related_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }

  echo "---\n";

  $query = <<<END
select Version.`content`
from Resource
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Resource.`canonParentId` = 2203
and ResourceLink.`shortName` = "$discussion[shortname]"
END;
  $versions = $snap2DbConn->query($query);

  $images = echoContentAndGetImages(
    $versions,
    $discussion["shortname"],
    $images
  );
}
foreach ($images as $name => $path) {
  echo "$name,$path\n";
}

?>

