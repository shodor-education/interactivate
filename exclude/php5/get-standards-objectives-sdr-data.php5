<?php
header("Content-Type: text/plain");

include_once("helpers.php5");

function getAlignedResources($dbConn, $propertyName, $objectiveId, $type, $shortnameFilter) {
  $query = <<<END
select $shortnameFilter as str
from TSDStandardAlignment
left join SDRVersion on SDRVersion.`cserdId` = TSDStandardAlignment.`resourceId`
left join SDRVersionFieldValue on SDRVersionFieldValue.`versionId` = SDRVersion.`id`
left join SDRField on SDRField.`id` = SDRVersionFieldValue.`fieldId`
left join SDRTextValue on SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`
left join SDRVersionFieldValue as TitleVFV on TitleVFV.`versionId` = SDRVersion.`id`
left join SDRField as TitleF on TitleF.`id` = TitleVFV.`fieldId`
left join SDRTextValue as TitleTV on TitleTV.`valueId` = TitleVFV.`valueId`
where TSDStandardAlignment.`objectiveId` = $objectiveId
and TSDStandardAlignment.`version` = "LIVE"
and SDRVersion.`state` = "live"
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "$type"
and UrlF.`name` = "Url"
and TitleF.`name` = "Title"
order by TitleTV.`entry`
END;
  $results = $dbConn->query($query);
  if ($results->num_rows > 0) {
    echo "aligned-$propertyName:\n";
    while ($result = $results->fetch_assoc()) {
      echo "  - \"$result[str]\"\n";
    }
  }
}

$sdrDbConn = new mysqli($DB_SERVER, $SDR_DB_USER, $SDR_DB_PASS, $SDR_DB_NAME);
if ($sdrDbConn->connect_error) {
  die("Database connection failed: " . $sdrDbConn->connect_error);
}

$query = <<<END
select
  TSDObjective.`id` as id,
  trim(trailing from
    replace(
      replace(
        replace(
          replace(
            TSDObjective.`description`,
            "\"",
            "\\\\\""
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
  ) as description,
  trim(trailing from TSDCategory.`name`) as categoryName,
  TSDGrade.`id` as gradeId
from TSDObjective
left join TSDCategory on TSDCategory.`id` = TSDObjective.`categoryId`
left join TSDGrade on TSDGrade.`id` = TSDCategory.`gradeId`
order by TSDObjective.`id`
END;

$objectives = $sdrDbConn->query($query);
while ($objective = $objectives->fetch_assoc()) {
  echo "FILENAME::$objective[id]\n---\n";

  // ALIGNED ACTIVITIES
  $normalShortnameFilter = "substring_index(substring_index(UrlTV.`entry`, '/', -2), '/', 1)";
  getAlignedResources(
    $sdrDbConn,
    "activities",
    $objective["id"],
    "Activity",
    $normalShortnameFilter
  );

  // ALIGNED DISCUSSIONS
  getAlignedResources(
    $sdrDbConn,
    "discussions",
    $objective["id"],
    "Discussion",
    $normalShortnameFilter
  );

  // ALIGNED LESSONS
  getAlignedResources(
    $sdrDbConn,
    "lessons",
    $objective["id"],
    "Lesson",
    $normalShortnameFilter
  );

  // ALIGNED WORKSHEETS
  $results = getAlignedResources(
    $sdrDbConn,
    "worksheets",
    $objective["id"],
    "Worksheet",
    "substring_index(UrlTV.`entry`, '/', -1)"
  );

  // STANDARDS CATEGORY
  echo "standards-category: \"$objective[categoryName]\"\n";

  // DESCRIPTION
  echo "description: \"$objective[description]\"\n";

  // GRADE BAND
  echo "grade-band: \"$objective[gradeId]\"\n";

  echo "---\n";
}
echo "\n";

?>

