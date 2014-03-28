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

    if ($cnt === 1) { // URI paths with a count of 1,2,3 are valid
        if ($method === 'GET') { // GET users
            include $root.'api/users/get_users.php';
        } else if ($method === 'POST') { // POST users
            include $root.'api/users/register_user.php';
        } else {
            throw_error(405, '405 - method not allowed');
        }
    } else if ($cnt === 2) {
        if ($method === 'GET') { // GET users/something_here
            if ($path_arr[1] === 'activate_email') { // GET users/activate_email
                include $root.'api/users/activate_email.php';
            } else {                                // GET users/some_user.json
                $_GET['user_id'] = strip_ext($path_arr[1], '.json');
                include $root.'api/users/get_user.php';
            }
        } else if ($method === 'POST') { // POST users/something_here
            if ($path_arr[1] === 'edit') { // POST users/edit
                include $root.'api/users/edit_user.php';
            } else {
                throw_error(404, '404 - not found');
            }
        } else {
            throw_error(405, '405 - not found');
        }
    } else if ($cnt === 3) {
        if ($method === 'GET') { // GET users/something/something_else
            if ($path_arr[2] === 'permissions') { // GET users/{id}/permissions
                $_GET['user_id'] = $path_arr[1];
                include $root.'api/users/get_permissions.php';
            } else if ($path_arr[1] === 'available') {
                // GET users/available/{id}.json
                $_GET['user_id'] = strip_ext($path_arr[2], '.json');
                include $root.'api/users/user_available.php';
            } else if ($path_arr[1] === 'email_registered') {
                // GET users/email_registered/{id}
                $_GET['email'] = $path_arr[2];
                include $root.'api/user/email_registered.php';
            } else {
                throw_error(404, '404 - not found');
            }
        } else if ($method === 'POST') {
            if ($path_arr[2] === 'permissions') { // POST users/{id}/permissions
                $_POST['user_id'] = $path_arr[1];
                include $root.'api/users/edit_permissions.php';
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
    // used to determine if request came from
    // shelvar front end
    $web    = isset($_GET['web']);
    $server = 'http://'.$_SERVER['HTTP_HOST'].'/';

    if ($cnt === 2) {
        if ($method === 'GET') {
            if ($path_arr[1] === '') {
                if ($web) {
                    header('Location: '.$server
                        .'api/institutions/get_institutions.php');
                } else {
                    include $root.'api/institutions/get_institutions.php';
                }
            } else if ($path_arr[1] === 'activate_inst') {
                include $root.'api/institutions/activate_inst.php';
            } else {
                $_GET['inst_id'] = strip_ext($path_arr[1], '.json');
                if ($web) {
                    header('Location: '.$server
                        .'api/institutions/get_institution.php?inst_id='
                        .$_GET['inst_id']); 
                } else {
                    include $root.'api/institutions/get_institution.php';
                }
            }
        } else if ($method === 'POST') {
            if ($path_arr[1] === '') {
                include $root.'api/institutions/register_institution.php';
            } else if ($path_arr[1] === 'edit') {
                if ($web) {
                    header('Location: '.$server
                        .'api/institutions/edit_institution.php');
                } else {
                    include $root.'api/institutions/edit_institution.php';
                }
            } else {
                throw_error(404, '404 - not found');
            }
        }
    } else if ($cnt === 3) {
        if ($method === 'GET') {
            if ($path_arr[1] === 'available') {
                $_GET['inst_id'] = strip_ext($path_arr[2], '.json');
                include $root.'api/institutions/inst_available.php';
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

