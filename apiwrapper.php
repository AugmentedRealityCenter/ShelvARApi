<?php
// request handler variables
$path       = $_GET['path'];
$path_arr   = explode('/', $path);
$req_type   = $_GET['type'];

switch ($path_arr[0]) {
    case "book_tags":       handle_bt($path_arr, $req_type); break;
    case "lc_numbers":      handle_lc($path_arr, $req_type); break;
    case "book_pings":      handle_bp($path_arr, $req_type); break;
    case "users":           handle_users($path_arr); break;
    case "institutions":    handle_inst($path_arr); break;
    case "make_tags":       handle_mt($path_arr, $req_type); break;
    case "notifications":   handle_notif($path_arr, $req_type); break;
    default:                throw_error(404, "404 - not found"); break;
}

/*
    * Function to strip an extension from a string
    * $id is the string with the extension
    * $ext is the extension to strip from the string
    * $ext should include the "."
    * returns the string without the extension
    * (and everything following it), if it is
    * found, otherwise the original string
    */
function strip_ext($id, $ext) {
    // if no extension, return string
    if (!strrpos($id, $ext)) return $id;
    // return stripped string
    return substr($id, 0, strrpos($id, $ext));
}

function get_domain() {
    if (strpos($_SERVER['HTTP_HOST'], 'api.shelvar.com') === 0) {
        return 'https://'.$_SERVER['HTTP_HOST'].'/';
    } else {
        return 'http://'.$_SERVER['HTTP_HOST'].'/';
    }
}

function redir($uri_path) {
    $server = get_domain();
    header('Location: '.$server.$uri_path);
}

function handle_bt($path_arr, $req_type) {
    global $root, $get_book_tags;
    if (count($path_arr) === 2) { // valid request
        if ($req_type === "GET") { // GET book_tags/{id}
            $_GET['B64'] = strip_ext($path_arr[1], ".json");
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
            $_GET['call_number'] = strip_ext($path_arr[1], ".json");
            include $root.$get_lc_numbers;
        } else {                    // some method that's not a GET
            throw_error(405, "405 - method not allowed");
        }
    } else {
        throw_error(404, "404 - not found");
    }
}

function handle_bp($path_arr, $req_type) {
    global $root, $post_bp, $get_bp, $get_bp_count, $get_bp_id;
    if (count($path_arr) === 2) { // valid request 
        if ($req_type === "GET") {  
            if ($path_arr[1] === "count") { // GET book_pings/count
                include $root.$get_bp_count;
            } else if ($path_arr[1] !== "") { // GET book_pings/{id}
                $_GET['book_ping_id'] = strip_ext($path_arr[1], ".json");
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

function handle_users($path_arr) {
    $cnt    = count($path_arr);
    $method = $_SERVER['REQUEST_METHOD'];
    $root   = $_SERVER['DOCUMENT_ROOT'].'/';
    $web    = isset($_GET['web']); // true if request came from front end
    $server = get_domain();

    include $_SERVER['DOCUMENT_ROOT'].'/path_vars_api.php';

    if ($cnt === 1) { // URI paths with a count of 1,2,3 are valid
        if ($method === 'GET') { // GET users
            if ($web) {
                redir($get_user_mult);
            } else {
                include $root.$get_user_mult;
            }
        } else if ($method === 'POST') { // POST users
            include $root.$post_users;
        } else {
            throw_error(405, '405 - method not allowed');
        }
    } else if ($cnt === 2) {
        // GET users/something_here
        if ($method === 'GET') { 
            // GET users/activate_email
            if ($path_arr[1] === 'activate_email') { 
                include $root.$get_act_email;
            // GET users/some_user.json
            } else {
                $_GET['user_id'] = strip_ext($path_arr[1], '.json');
                if ($web) {
                    redir($get_user.'?user_id='.$_GET['user_id']);
                } else {
                    include $root.$get_user;
                }
            }
        // POST users/something_here
        } else if ($method === 'POST') { 
            // POST users/edit
            if ($path_arr[1] === 'edit') { 
                if ($web) {
                    redir($post_users_edit);
                } else {
                    include $root.$post_users_edit;
                }
            } else {
                throw_error(404, '404 - not found');
            }
        } else {
            throw_error(405, '405 - not found');
        }
    } else if ($cnt === 3) {
        // GET users/something/something_else
        if ($method === 'GET') { 
            // GET users/{id}/permissions
            if ($path_arr[2] === 'permissions') { 
                $_GET['user_id'] = $path_arr[1];
                if ($web) {
                    redir($get_user_perm.'?user_id='.$_GET['user_id']);
                } else {
                    include $root.$get_user_perm;
                }

            // GET users/available/{id}.json
            } else if ($path_arr[1] === 'available') {
                $_GET['user_id'] = strip_ext($path_arr[2], '.json');
                include $root.$get_users_avail;
            // GET users/email_registered/{id}
            } else if ($path_arr[1] === 'email_registered') {
                redir($get_email_reg.'?email='.strip_ext($path_arr[2], '.json'));
            } else {
                throw_error(404, '404 - not found');
            }
        } else if ($method === 'POST') {
            // POST users/{id}/permissions
            if ($path_arr[2] === 'permissions') { 
                if ($web) {
                    redir($post_users_perm);
                } else {
                    include $root.$post_users_perm;
                }
            } else {
                throw_error(404, '404 - not found');
            }
        } else {
            throw_error(405, '405 - method not allowed');
        }
    } else {
        throw_error(404, '404 - not found');
    }
}

function handle_inst($path_arr) {
    $cnt    = count($path_arr);
    $method = $_SERVER['REQUEST_METHOD'];
    $root   = $_SERVER['DOCUMENT_ROOT'].'/';
    $web    = isset($_GET['web']); // true if request came from front end
    $server = get_domain();

    include $_SERVER['DOCUMENT_ROOT'].'/path_vars_api.php';

    if ($cnt === 2) {
        if ($method === 'GET') {
            if ($path_arr[1] === '') {
                if ($web) {
                    redir($get_inst_mult);
                } else {
                    include $root.$get_inst_mult;
                }
            } else if ($path_arr[1] === 'activate_inst') {
                include $root.$get_act_inst;
            } else {
                $_GET['inst_id'] = strip_ext($path_arr[1], '.json');
                if ($web) {
                    redir($get_inst.'?inst_id='.$_GET['inst_id']); 
                } else {
                    include $root.$get_inst;
                }
            }
        } else if ($method === 'POST') {
            if ($path_arr[1] === '') {
                include $root.$post_inst_reg;
            } else if ($path_arr[1] === 'edit') {
                if ($web) {
                    redir($post_inst_edit);
                } else {
                    include $root.$post_inst_edit;
                }
            } else {
                throw_error(404, '404 - not found');
            }
        }
    } else if ($cnt === 3) {
        if ($method === 'GET') {
            if ($path_arr[1] === 'available') {
                $_GET['inst_id'] = strip_ext($path_arr[2], '.json');
                include $root.$get_inst_avail;
            } else {
                throw_error(404, '404 - not found');
            }
        } else {
            throw_error(405, '405 - not found');
        }
    } else {
        throw_error(404, '404 - not found');
    }
}

function handle_mt($path_arr, $req_type) {
    global $get_formats, $get_tags, $root;
    if (count($path_arr) === 2) { // valid request
        if ($req_type === "GET") { // GET make_tags/something_here
            if ($path_arr[1] === "paper_formats") { // GET make_tags/paper_formats
                include $root.$get_formats;
            } else {                                // GET make_tags/something_else
                $_GET['type'] = strip_ext($path_arr[1], ".pdf");
                include $root.$get_tags;
            }
        } else {
            throw_error(405, "405 - method not allowed");
        }
    } else {
        throw_error(404, "404 - not found");
    }
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

