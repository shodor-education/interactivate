-- Download dictionary
select concat("- term: \"", word, "\"\r  definition: \"", replace(definition, "\"", "\\\""), "\"")
from TSDDictionary
where word like "a%"
order by word
