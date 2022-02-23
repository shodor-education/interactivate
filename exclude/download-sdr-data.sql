-- Download activity SDR data
select concat(
  concat(substring_index(substring_index(UrlTV.`entry`, '/', -2), '/', 1), ".html\r"),
  "---\r",
  concat_ws(
    "\r",
    concat("audiences:\r  - \"", group_concat(distinct AudienceTV.`entry` order by AudienceTV.`entry` separator "\"\r  - \"" ), "\""),
    concat("description: \"", replace(DescriptionTV.`entry`, "\"", "\\\""), "\""),
    "layout: activity",
    concat("short-name: \"", substring_index(substring_index(UrlTV.`entry`, '/', -2), '/', 1), "\""),
    concat("subjects:\r  - \"", group_concat(distinct SubjectTV.`entry` order by SubjectTV.`entry` separator "\"\r  - \"" ), "\""),
    concat("title: \"", TitleTV.`entry`, "\""),
    concat("topics:\r  - \"", group_concat(distinct TopicTV.`entry` order by TopicTV.`entry` separator "\"\r  - \"" ), "\"")
  ),
  "\r---"
)
from SDRProject
left join SDRProjectField on SDRProjectField.`projectId` = SDRProject.`id`
left join SDRField on SDRField.`id` = SDRProjectField.`fieldId`
left join SDRFieldValue on SDRFieldValue.`fieldId` = SDRField.`id`
left join SDRTextValue on SDRTextValue.`valueId` = SDRFieldValue.`valueId`
left join SDRVersionFieldValue on (SDRVersionFieldValue.`fieldId` = SDRField.`id` and SDRVersionFieldValue.`valueId` = SDRTextValue.`valueId`)
left join SDRVersion on SDRVersion.`id` = SDRVersionFieldValue.`versionId`

left join SDRVersionFieldValue as TitleVFV on TitleVFV.`versionId` = SDRVersion.`id`
left join SDRField as TitleF on TitleF.`id` = TitleVFV.`fieldId`
left join SDRTextValue as TitleTV on TitleTV.`valueId` = TitleVFV.`valueId`

left join SDRVersionFieldValue as UrlVFV on UrlVFV.`versionId` = SDRVersion.`id`
left join SDRField as UrlF on UrlF.`id` = UrlVFV.`fieldId`
left join SDRTextValue as UrlTV on UrlTV.`valueId` = UrlVFV.`valueId`

left join SDRVersionFieldValue as DescriptionVFV on DescriptionVFV.`versionId` = SDRVersion.`id`
left join SDRField as DescriptionF on DescriptionF.`id` = DescriptionVFV.`fieldId`
left join SDRTextValue as DescriptionTV on DescriptionTV.`valueId` = DescriptionVFV.`valueId`

left join SDRVersionFieldValue as AudienceVFV on AudienceVFV.`versionId` = SDRVersion.`id`
left join SDRField as AudienceF on AudienceF.`id` = AudienceVFV.`fieldId`
left join SDRTextValue as AudienceTV on AudienceTV.`valueId` = AudienceVFV.`valueId`

left join SDRVersionFieldValue as TopicVFV on TopicVFV.`versionId` = SDRVersion.`id`
left join SDRField as TopicF on TopicF.`id` = TopicVFV.`fieldId`
left join SDRTextValue as TopicTV on TopicTV.`valueId` = TopicVFV.`valueId`

left join SDRVersionFieldValue as SubjectVFV on SubjectVFV.`versionId` = SDRVersion.`id`
left join SDRField as SubjectF on SubjectF.`id` = SubjectVFV.`fieldId`
left join SDRTextValue as SubjectTV on SubjectTV.`valueId` = SubjectVFV.`valueId`

where SDRProject.`id` = 3
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "Activity"
and SDRVersion.`state` = "live"

and TitleF.`name` = "Title"
and UrlF.`name` = "Url"
and DescriptionF.`name` = "Description"
and AudienceF.`name` = "Interactivate_Audience"
and TopicF.`name` = "Related_Subject"
and SubjectF.`name` = "Primary_Subject"

group by SDRVersion.`id`
order by UrlTV.`entry`