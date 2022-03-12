-- Download activity textbook alignment reasons
select concat(
  "- activity: \"", replace(substring_index(UrlTV.`entry`, "/", -2), "/", ""), "\"\r",
  "  reason: \"", replace(TSDTextbookAlignment.`reason`, "\"", "\\\""), "\"\r",
  "  textbook-section: \"", TSDTextbookAlignment.`sectionId`, "\""
)
from TSDTextbookAlignment
left join SDRVersion on SDRVersion.`cserdId` = TSDTextbookAlignment.`resourceId`
left join SDRVersionFieldValue on SDRVersionFieldValue.`versionId` = SDRVersion.`id`
left join SDRField on SDRField.`id` = SDRVersionFieldValue.`fieldId`
left join SDRTextValue on SDRTextValue.`valueId` = SDRVersionFieldValue.`valueId`
left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`
where TSDTextbookAlignment.`reason` != ""
and TSDTextbookAlignment.`version` = "LIVE"
and SDRVersion.`state` = "live"
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "Activity"
and UrlF.`name` = "Url"
order by resourceId, TSDTextbookAlignment.`sectionId`
