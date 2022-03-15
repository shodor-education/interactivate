<?php
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

function echoActivityInstructorJsonHtml($property) {
  echoJsonHtml("Activity", 2202, $property, "", "instructor");
}

function echoActivityInstructorJsonArray($property) {
  global $sdrDbConn;
  $activities = getSdrResources("Activity");
  while ($activity = $activities->fetch_assoc()) {
    $shortname = getShortname($activity);
    $json = json_decode(
      getActivitySection(
        "instructor"
      , $shortname
      )
    );
    if (isset($json->$property)) {
      echo "$shortname:\n";
      foreach ($json->$property as $item) {
        $item = preg_replace("#<p>\n\s*#", "", $item);
        $item = preg_replace("#\s*\n</p>#", "", $item);
        $item = preg_replace("#\n\s*<link#", " <link", $item);
        $item = preg_replace("#\s*\n\s*#", "", $item);
        $item = preg_replace("#\s+#", " ", $item);
        $item = preg_replace(
          "#<link base=\"PATH:dictionary\" href=\"/(.)\">#",
          "<a href=\"/dictionary/$1\">",
          $item
        );
        $item = preg_replace("#</link>#", "</a>", $item);
        preg_match_all("<link metaid=\"(\d*)\">", $item, $linkMatches);
        foreach ($linkMatches[1] as $cserdId) {
    $query = <<<END
select url
from SDRResource
where cserdid = $cserdId
END;
          $urlResults = $sdrDbConn->query($query);
          while ($urlResult = $urlResults->fetch_assoc()) {
            $url = str_replace(
              "http://www.shodor.org/interactivate"
            , ""
            , $urlResult["url"]
            );
            $url = preg_replace("/\/$/", "", $url);
            $item = str_replace(
              "<link metaid=\"$cserdId\">"
            , "<a href=\"$url\">"
            , $item
            );
          }
        }
        $item = str_replace("\"", "\\\"", $item);
        echo "- \"$item\"\n";
      }
    }
  }
}

function echoAlignment($label, $table, $column, $resourceId) {
  global $sdrDbConn;
  $query = <<<END
select $table.`$column` as $column
from $table
where $table.`version` = "LIVE"
and $table.`resourceId` = $resourceId
order by $table.`$column`
END;
  $results = $sdrDbConn->query($query);
  if ($results->num_rows > 0) {
    echo "$label:\n";
    while ($result = $results->fetch_assoc()) {
      echo "  - \"$result[$column]\"\n";
    }
  }
}

function echoAlignedStandardsObjectives($resourceId) {
  echoAlignment(
    "aligned-standards-objectives",
    "TSDStandardAlignment",
    "objectiveId",
    $resourceId
  );
}

function echoAlignedTextbookSections($resourceId) {
  echoAlignment(
    "aligned-textbook-sections",
    "TSDTextbookAlignment",
    "sectionId",
    $resourceId
  );
}

function echoAudiences($resource) {
  echoSdrArray(
    "audiences",
    $resource["versionId"],
    "Interactivate_Audience"
  );
}

function echoDescription($resource) {
  echoSingleSdrValue(
    "description",
    $resource["versionId"],
    "Description"
  );
}

function echoDiscussions($resource) {
  echo "subjects:\n";
  $results = getTextValues(
    $sdrDbConn,
    $resource["versionId"],
    "Primary_Subject"
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }
}

function echoJsonArray($json, $property, $label) {
  if (isset($json->$property)) {
    echo "$label:\n";
    foreach ($json->$property as $item) {
      $item = str_replace("\"", "\\\"", $item);
      echo "  - \"$item\"\n";
    }
  }
}

function echoJsonHtml($type, $parentId, $property, $customFunc = ""
  , $activitySectionType = "") {
  $files = array();
  $resources = getSdrResources($type);
  while ($resource = $resources->fetch_assoc()) {
    $shortname = getShortname($resource);
    if ($type == "Activity") {
      $json = json_decode(
        getActivitySection(
          $activitySectionType
        , $shortname
        )
      );
    }
    else {
      $json = getVersionContentJson($shortname, $parentId);
    }
    if (isset($json->$property) && $json->$property) {
      echo "FILENAME::$shortname\n";
      if ($customFunc) {
        $files = call_user_func(
          $customFunc,
          $json->$property,
          $shortname,
          $files
        );
      }
      else {
        $result = xmlToHtmlWithFiles(
          $json->$property,
          $shortname,
          $files
        );
        $files = $result[1];
        echo "$result[0]\n";
      }
    }
  }
  foreach ($files as $name => $url) {
    echo "$name,$url\n";
  }
}

function echoSdrArray($label, $versionId, $field) {
  global $sdrDbConn;
  echo "$label:\n";
  $results = getTextValues(
    $sdrDbConn,
    $versionId,
    $field
  );
  while ($result = $results->fetch_assoc()) {
    echo "  - \"$result[str]\"\n";
  }
}

function echoSingleSdrValue($label, $versionId, $field) {
  global $sdrDbConn;
  echo "$label: \"";
  $results = getTextValues(
    $sdrDbConn,
    $versionId,
    $field
  );
  $result = $results->fetch_assoc();
  echo "$result[str]\"\n";
}

function echoSubjects($resource) {
  echoSdrArray(
    "subjects",
    $resource["versionId"],
    "Primary_Subject"
  );
}

function echoTitle($resource) {
  echoSingleSdrValue(
    "title",
    $resource["versionId"],
    "Title"
  );
}

function echoTopics($resource) {
  echoSdrArray(
    "topics",
    $resource["versionId"],
    "Related_Subject"
  );
}

function getActivityHelpSubsections($mapping, $override = false) {
  $files = array();
  $activities = getSdrResources("Activity");
  while ($activity = $activities->fetch_assoc()) {
    $shortname = getShortname($activity);
    $content = getActivitySection("help", $shortname);
    $content = str_replace("&ndash;", "&#8211;", $content);
    $content = str_replace("&ne;", "&#8800;", $content);
    $xml = simplexml_load_string($content);
    $section = $xml->xpath("//section[@mapping='$mapping']");
    if ($section || $override) {
      $sectionXml = $section
        ? $section[0]->asXML()
        : $xml->children()->asXML();
      $result = xmlToHtmlWithFiles(
        $sectionXml,
        $shortname,
        $files
      );
      if ($result[0]) {
        $files = $result[1];
        echo "FILENAME::$shortname\n";
        echo "$result[0]\n";
      }
    }
  }
  foreach ($files as $name => $path) {
    echo "$name,$path\n";
  }
}

function getActivitySection($tabName, $shortname) {
  global $snap2DbConn;
  $query = <<<END
select Version.`content`
from Directory
left join DirectoryLink on DirectoryLink.`childId` = Directory.`id`
left join Resource on Resource.`canonParentId` = Directory.`id`
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
left join Version on Version.`id` = Resource.`liveVersionId`
where Directory.`canonParentId` = 2202
and ResourceLink.`shortname` = "$tabName"
and DirectoryLink.`shortName` = "$shortname"
END;
  $versions = $snap2DbConn->query($query);
  $version = $versions->fetch_assoc();
  return $version["content"];
}

function getActivitySections($tabName) {
  $files = array();
  $activities = getSdrResources("Activity");
  while ($activity = $activities->fetch_assoc()) {
    $shortname = getShortname($activity);
    echo "FILENAME::$shortname\n";
    $xml = getActivitySection($tabName, $shortname);
    $result = xmlToHtmlWithFiles(
      $xml,
      $shortname,
      $files
    );
    $content = $result[0];
    $files = $result[1];
    echo "$content\n";
  }
  foreach ($files as $name => $path) {
    echo "$name,$path\n";
  }
}

function getPath($snapId) {
  global
    $snap2DbConn;
  $query = <<<END
select parentId, shortName
from DirectoryLink
where childId = $snapId
END;
  $results = $snap2DbConn->query($query);
  while ($result = $results->fetch_assoc()) {
    if ($result["parentId"] != 1) {
      return getPath($result["parentId"]) . "/" . $result["shortName"];
    }
    else {
      return $result["shortName"];
    }
  }
}

function getSdrResources($interactivateType) {
  global $sdrDbConn;
  $query = <<<END
select
  SDRVersion.`cserdId` as resourceId,
  UrlTV.`entry` as url,
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
and SDRTextValue.`entry` = "$interactivateType"
and SDRVersion.`state` = "live"
and UrlF.`name` = "Url"
order by UrlTV.`entry`
END;
  return $sdrDbConn->query($query);
}

function getShortname($resource) {
  $shortname = $resource["url"];
  if (substr($shortname, -1) == "/") {
    $shortname = substr($shortname, 0, -1);
  }
  $shortname = substr($shortname, strrpos($shortname, "/") + 1);
  return $shortname;
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

function getVersionContent($shortname, $parentId) {
  global $snap2DbConn;
  $query = <<<END
select Version.`content`
from ResourceLink
left join Version on Version.`resourceId` = ResourceLink.`childId`
where ResourceLink.`parentId` = $parentId
and Version.`status` = 3
and ResourceLink.`shortName` = "$shortname"
END;
  $versions = $snap2DbConn->query($query);
  $version = $versions->fetch_assoc();
  return $version["content"];
}

function getVersionContentJson($shortname, $parentId) {
  $content = getVersionContent($shortname, $parentId);
  return json_decode($content);
}

function xmlToHtmlWithFiles($xml, $shortname, $files) {
  global $sdrDbConn, $snap2DbConn;
  $html = $xml;
  $html = preg_replace("#\s+\n#", "\n", $html);
  $html = preg_replace("#\s*<XMLResource>\s*\n#", "", $html);
  $html = preg_replace("#\s*<section mapping=\".*\"\s*/?>\s*\n?#", "", $html);
  $html = preg_replace(
    "#<link base=\"PATH:dictionary\" href=\"/(.)#"
  , "<a href=\"{{ '/dictionary/$1' | relative_url }}"
  , $html
  );
  $html = preg_replace(
    "#<link base=\"PATH:resources\" href=\"/(.*)\"#"
  , "<a href=\"{{ '/resources/$1' | relative_url }}"
  , $html
  );
  $html = preg_replace(
    "#<link href=\"http://www.shodor.org/interactivate/dictionary/(.)/#"
  , "<a href=\"{{ '/dictionary/$1' | relative_url }}"
  , $html
  );
  $html = preg_replace(
    "#<link href=\"/interactivate/dictionary/(.)/?#"
  , "<a href=\"{{ '/dictionary/$1' | relative_url }}"
  , $html
  );
  $html = preg_replace(
    "#<p>\s*<span class=\"bold header\">(.*)</span>\s*</p>#"
  , "<h3>$1</h3>"
  , $html
  );
  $html = preg_replace(
    "#link href=\"../(.+)\"#"
  , "a href=\"{{ '/resources/$1' | relative_url }}\""
  , $html
  );
  $html = preg_replace(
    "#link href=\"http://www.shodor.org/interactivate/activities/(.+)\"#"
  , "a href=\"{{ '/activities/$1' | relative_url }}\""
  , $html
  );
  $html = str_replace("<link href=\"#", "<a href=\"#", $html);
  $html = str_replace("<link name=\"", "<a name=\"", $html);
  $html = str_replace("<p/>", "", $html);
  $html = str_replace("<b>", "<strong>", $html);
  $html = str_replace("<i>", "<em>", $html);
  $html = str_replace("<u>", "<strong>", $html);
  $html = str_replace("class=\"center\"", "class=\"h-center\"", $html);
  $html = str_replace("</u>", "</u>", $html);
  $html = str_replace("</i>", "</em>", $html);
  $html = str_replace("</b>", "</strong>", $html);
  $html = str_replace("</link>", "</a>", $html);
  $html = preg_replace("#\s*</section>\s*\n?#", "", $html);
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
      $html = str_replace(
        "<link metaid=\"$cserdId\">"
      , "<a href=\"{{ '$url' | relative_url }}\">"
      , $html
      );
    }
  }
  preg_match_all("<media .*snapid=\"(\d*)\">", $html, $mediaMatches);
  foreach ($mediaMatches[1] as $snapId) {
    $query = <<<END
select Version.`content`, Resource.`contentType`, ResourceLink.`shortName`
from Version
left join Resource on Resource.`id` = Version.`resourceId`
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
where Version.`resourceId` = $snapId
and Version.`status` = 3
END;
    $mediaResults = $snap2DbConn->query($query);
    while ($mediaResult = $mediaResults->fetch_assoc()) {
      if ($mediaResult["contentType"] == 2) {
        $json = json_decode($mediaResult["content"]);
        $path = $json->original->path;
        $extension = end(explode(".", $path));
        $imageName = "$mediaResult[shortName].$extension";
        $files[$imageName] = "http://shodor.org/media/" . $path;

        $html = str_replace(
          "<media snapid=\"$snapId\"",
          "<img src=\"{{ 'img/$imageName' | relative_url }}\"",
          $html
        );
        $html = str_replace(
          "<media class=\"h-center\" snapid=\"$snapId\"",
          "<img class=\"h-center\" src=\"{{ 'img/$imageName' | relative_url }}\"",
          $html
        );
      }
      else if ($mediaResult["contentType"] == 9) {
        $query = <<<END
select ResourceLink.`parentId`
from Resource
left join ResourceLink on ResourceLink.`childId` = Resource.`id`
where Resource.`id` = $snapId
END;
        $json = json_decode($mediaResult["content"]);
        $filename = $json->file->name;
        $results = $snap2DbConn->query($query);
        while ($result = $results->fetch_assoc()) {
          $path = getPath($result["parentId"]) . "/" . $filename;
          $html = str_replace(
            "<media snapid=\"$snapId\" />",
            "<a href=\"http://shodor.org/media/content/$path\">Download File</a>",
            $html
          );
        }
      }
    }
  }
  return array($html, $files);
}

?>

