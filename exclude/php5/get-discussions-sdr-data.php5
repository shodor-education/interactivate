<?php
header("Content-Type: text/plain");

include_once("passwords.php5");

$DB_SERVER = "mysql-be-yes-I-really-mean-prod.shodor.org";
$SNAP2_DB_NAME = "db_snap";
$SNAP2_DB_USER = "db_snap_user";

$SDR_DB_NAME = "db_sdr";
$SDR_DB_USER = "search_sdr";

$snap2DbConn = new mysqli($DB_SERVER, $SNAP2_DB_USER, $SNAP2_DB_PASS, $SNAP2_DB_NAME);
if ($snap2DbConn->connect_error) {
  die("Database connection failed: " . $snap2DbConn->connect_error);
}
$sdrDbConn = new mysqli($DB_SERVER, $SDR_DB_USER, $SDR_DB_PASS, $SDR_DB_NAME);
if ($sdrDbConn->connect_error) {
  die("Database connection failed: " . $sdrDbConn->connect_error);
}

function getTextValues($dbConn, $versionId, $fieldName) {
  $query = <<<END
select trim(
  trailing from replace(
    replace(
      replace(
        replace(
          SDRTextValue.`entry`,
          "\\"",
          "\\\\\\""
        ),
        "\t",
        " "
      ),
      "\r\n",
      " "
    ),
    "  ",
    " "
  )
) as str
from SDRVersionFieldValue
left join SDRField on SDRField.`id` = SDRVersionFieldValue.`fieldId`
left join SDRTextValue on SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
where SDRVersionFieldValue.`versionId` = $versionId
and SDRField.`name` = "$fieldName"
order by SDRTextValue.`entry`
END;
  return $dbConn->query($query);
}

$query = <<<END
select SDRVersion.`cserdId` as resourceId,
       substring_index(substring_index(UrlTV.`entry`, '/', -2), '/', 1) as shortname,
       SDRVersion.`id` as versionId
from SDRProject
left join SDRProjectField on SDRProjectField.`projectId` = SDRProject.`id`
left join SDRField on SDRField.`id` = SDRProjectField.`fieldId`
left join SDRFieldValue on SDRFieldValue.`fieldId` = SDRField.`id`
left join SDRTextValue on SDRTextValue.`valueId` = SDRFieldValue.`valueId`
left join SDRVersionFieldValue on (SDRVersionFieldValue.`fieldId` = SDRField.`id` and SDRVersionFieldValue.`valueId` = SDRTextValue.`valueId`)
left join SDRVersion on SDRVersion.`id` = SDRVersionFieldValue.`versionId`
left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`
where SDRProject.`id` = 3
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "Discussion"
and SDRVersion.`state` = "live"
and UrlF.`name` = "Url"
order by UrlTV.`entry`
END;

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
  $results = $snap2DbConn->query($query);
  while ($result = $results->fetch_assoc()) {
    $html = $result["content"];
    $html = preg_replace("#\s+\n#", "\n", $html);
    $html = preg_replace("#\s*<XMLResource>\s*\n#", "", $html);
    $html = preg_replace("#\s*<section mapping=\"content\">\s*\n#", "", $html);
    $html = preg_replace("#<link base=\"PATH:dictionary\" href=\"/(.)#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"http://www.shodor.org/interactivate/dictionary/(.)/#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = preg_replace("#<link href=\"/interactivate/dictionary/(.)/?#", "<a href=\"{{ '/dictionary/$1' | relative_url }}", $html);
    $html = str_replace("<b>", "<strong>", $html);
    $html = str_replace("<i>", "<em>", $html);
    $html = str_replace("<u>", "<strong>", $html);
    $html = str_replace("class=\"center\"", "class=\"h-center\"", $html);
    $html = str_replace("</u>", "</u>", $html);
    $html = str_replace("</i>", "</em>", $html);
    $html = str_replace("</b>", "</strong>", $html);
    $html = str_replace("</link>", "</a>", $html);
    $html = preg_replace("#\s*</section>\s*\n#", "", $html);
    $html = preg_replace("#</XMLResource>#", "", $html);
    preg_match_all("<link metaid=\"(\d*)\">", $html, $linkMatches);
    foreach ($linkMatches[1] as $cserdId) {
      $query = <<<END
        select url
        from SDRResource
        where cserdid = $cserdId
END;
      $urlResults = $sdrDbConn->query($query);
      while ($urlResult = $urlResults->fetch_assoc()) {
        $url = str_replace("http://www.shodor.org/interactivate", "", $urlResult["url"]);
        $url = preg_replace("/\/$/", "", $url);
        $html = str_replace("<link metaid=\"$cserdId\">", "<a href=\"{{ '$url' | relative_url }}\">", $html);
        $html = str_replace("</link>", "</a>", $html);
      }
    }
    $nImages = 1;
    preg_match_all("<media .*snapid=\"(\d*)\">", $html, $mediaMatches);
    foreach ($mediaMatches[1] as $snapId) {
      $query = <<<END
select Version.`content`
from Version
where Version.`resourceId` = $snapId
and Version.`status` = 3
END;
      $mediaResults = $snap2DbConn->query($query);
      while ($mediaResult = $mediaResults->fetch_assoc()) {
        $json = json_decode($mediaResult["content"]);
        $path = $json->original->path;
        $extension = end(explode(".", $path));
        $imageName = $discussion["shortname"] . "-discussion-" . ($nImages++) . "." . $extension;
        $images[$imageName] = "http://shodor.org/media/" . $path;
        $html = str_replace(
          "<media snapid=\"$snapId\"",
          "<img src=\"{{ 'img/discussions/$imageName' | relative_url }}\"",
          $html
        );
        $html = str_replace(
          "<media class=\"h-center\" snapid=\"$snapId\"",
          "<img class=\"h-center\" src=\"{{ 'img/discussions/$imageName' | relative_url }}\"",
          $html
        );
      }
    }
    echo "$html\n";
  }
}
foreach ($images as $name => $path) {
  echo "$name,$path\n";
}

?>

