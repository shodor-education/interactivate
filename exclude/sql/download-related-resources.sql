select SDRResource2.`url`

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

left join TSDRelation on TSDRelation.`sourceId` = SDRVersion.`cserdId`
left join SDRVersion as SDRVersion2 on SDRVersion2.`cserdId` = TSDRelation.`destId`
left join SDRResource as SDRResource2 on SDRResource2.`cserdId` = SDRVersion2.`cserdId`

where SDRProject.`id` = 3
and SDRField.`name` = "Interactivate_Type"
and SDRTextValue.`entry` = "Activity"
and SDRVersion.`state` = "live"
and TitleF.`name` = "Title"
and UrlF.`name` = "Url"
and (TitleTV.`entry` = "TheChaosGame" or UrlTV.`entry` = concat("http://www.shodor.org/interactivate/activities/TheChaosGame/"))
and TSDRelation.`version` = "LIVE"
and SDRVersion2.`state` = "live"
