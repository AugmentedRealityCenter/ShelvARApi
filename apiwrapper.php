<?php
    $root = $_SERVER['DOCUMENT_ROOT']."/";
    include $root."wrapper_constants.php";

    // request path constants 
    
    // request handler variables
    $path       = $_GET['path'];
    $path_arr   = explode('/', $path);
    $req_type   = $_GET['type'];

    switch ($path_arr[0]) {
        case "book_tags":       handle_bt($path_arr, $req_type); break;
        case "lc_numbers":      handle_lc($path_arr, $req_type); break;
        case "book_pings":      handle_bp($path_arr, $req_type); break;
        case "users":           handle_users($path_arr, $req_type); break;
        case "institutions":    handle_inst($path_arr, $req_type); break;
        case "make_tags":       handle_mt($path_arr, $req_type); break;
        case "oauth":           handle_oauth($path_arr, $req_type); break;
        case "notifications":   handle_notif($path_arr, $req_type); break;
        default:                throw_error(404, "404 - not found"); break;
    }

    function handle_bt($path_arr, $req_type) {
        global $root, $get_book_tags;
        if (count($path_arr) === 2) { // valid request
            if ($req_type === "GET") { // GET book_tags/{id}
                $_GET["B64"] = $path_arr[1];
                include $root.$get_book_tags;
            } else {
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - not found");
        }
    }

    function handle_lc($path_arr, $req_type) {
        global $root, $get_lc_numbers; 
        if (count($path_arr) === 2) { // valid request
            if ($req_type === "GET") { // GET lc_numbers/{call_number}
                $_GET['call_number'] = $path_arr[1];
                include $root.$get_lc_numbers;
            } else {                    // some method that's not a GET
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - not found");
        }
    }

    function handle_bp($path_arr, $req_type) {
        echo "DOES IT WORK?";
        global $root, $post_bp, $get_bp, $get_bp_count, $get_bp_id;
        if (count($path_arr) === 2) { // valid request 
            if ($req_type === "GET") {  
                if ($path_arr[1] === "count") { // GET book_pings/count
                    include $root.$get_bp_count;
                } else if ($path_arr[1] !== "") { // GET book_pings/{id}
                    $_GET['book_ping_id'] = $path_arr[1];
                    include $root.$get_bp_id;
                } else if ($path_arr[1] === "") { // GET book_pings/
                    include $root.$get_bp;
                } else {                            // some other path, so throw error
                    throw_error(404, "404 - not found");
                }
            } else if ($req_type === "POST") {
                if ($path_arr[1] === "") { // POST book_pings/
                    include $root.$post_bp;
                } else {
                    throw_error(404, "404 - not found");
                }
            } else {
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - not found");
        }
    }

    function handle_users($path_arr, $req_type) {

    }

    function handle_inst($path_arr, $req_type) {

    }

    function handle_mt($path_arr, $req_type) {
        global $get_formats, $get_tags, $root;
        if (count($path_arr) === 2) { // valid request
            if ($req_type === "GET") { // GET make_tags/something_here
                if ($path_arr[1] === "paper_formats") { // GET make_tags/paper_formats
                    include $root.$get_formats;
                } else {                                // GET make_tags/something_else
                    $_GET['type'] = $path_arr[1];       // set type in $_GET
                    include $root.$get_tags;
                }
            } else {
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - method not allowed");
        }
    }

    function handle_oauth($path_arr, $req_type) {

    }

    function handle_notif($path_arr, $req_type) {
        global $root, $get_notif;
        if (count($path_arr) === 2) { // valid request
            if ($req_type === "GET") { // GET notifications/{inst_id}
                $_GET['inst_id'] = $path_arr[1];
                include $root.$get_notif;
            } else {                    // some method that's not a GET
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - not found");
        }
    }

    function throw_error($code, $out_string) {
        http_response_code($code);
        echo "<h1>".$out_string."</h1>";
    }

