This script will return the data for a single book ping, based on id. URL will be:
http://dev.shelvar.com/api/book_pings/12345.json
Use .htaccess to block non-json formats, and to make the 12345 the id to be looked up

If the URL is
http://dev.sehlvar.com/api/book_pings/count
just return a count of pings. Optional parameters which limit the search: 
start_date (default 0), end_date (default infinity), book_tag (default empty)

If the URL is
http://dev.shelvar.com/api/book_pings/by_tag/12345.json
Look up all book pings that have the matching book_tag

Future functionality: Some day allow lookup by call number. Save this for later.
http://dev.shelvar.com/api/book_pings/by_lc_number/nx543%20.F47.json
Note that the LC number must be urlencoded because of possible spaces, commas, etc.
