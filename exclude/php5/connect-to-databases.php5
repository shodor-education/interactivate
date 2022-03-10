<?php
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
and SDRTextValue.`entry` = "$INTERACTIVATE_TYPE"
and SDRVersion.`state` = "live"
and UrlF.`name` = "Url"
order by UrlTV.`entry`
END;

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
?>

