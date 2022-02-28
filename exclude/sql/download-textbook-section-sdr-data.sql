-- Download textbook section SDR data
select concat(
  "FILENAME::", TSDSection.`id`, "\r---\r",
  "chapter: \"", TSDChapter.`name`, "\"\r",
  "textbook: \"", TSDBook.`id`, "\"\r",
  "title: \"", replace(TSDSection.`name`, "\"", "\\\""), "\"\r",
  "---"
)
from TSDSection
left join TSDChapter on TSDChapter.`id` = TSDSection.`chapterId`
left join TSDBook on TSDBook.`id` = TSDChapter.`bookId`
