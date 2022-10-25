Wordpress Pages will allow you to create Wordpress pages on the fly, with a simple PHP array. It currently only works for 1 levvel of pages and currently works best with sites with only unique page names.

create_pages.php should be included in your functions.php file and called once (or whenever you need it). It is set up so that you can call it in the admin screen by appending the GET variable to the url. See the bottom of the code to find the variable and value 
