-- Download textbook SDR data
select concat(
  "FILENAME::", bookId, "\r---\r",
  "chapters:\r- \"",
  group_concat(TSDChapter.`name` separator "\"\r- \""),
  "\"\r",
  "title: \"", title, " ", gradeOrCourse, "\"\r",
  "---"
)
from TSDBook
left join TSDChapter on TSDChapter.`bookId` = TSDBook.`id`
group by bookId
