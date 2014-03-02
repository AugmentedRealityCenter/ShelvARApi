<?php
    // get path constants
    $get_book_tags      = "api/lc2bin/book_tags.php";
    $get_lc_numbers     = "api/lc2bin/lc_numbers.php";
    $get_bp_count       = "api/book_pings/get_ping_count_book.php";
    $get_bp_id          = "api/book_pings/get_by_id.php";
    $get_user_perm      = "api/users/get_permissions.php";
    $get_act_email      = "api/users/activate_email.php";
    $get_users_avail    = "api/users/user_available.php";
    $get_email_reg      = "api/users/email_registered.php";
    $get_user           = "api/users/get_user.php";
    $get_user_mult      = "api/users/get_users.php";
    $get_act_inst       = "api/institutions/activate_inst.php";
    $get_inst_avail     = "api/institutions/inst_available.php";
    $get_inst           = "api/institutions/get_institution.php";
    $get_inst_mult      = "api/institutions/get_institutions.php";
    $get_bp             = "api/book_pings/book_pings_get.php";
    $get_tags           = "api/tagmaker/pdfPrint2.php";
    $get_formats        = "api/tagmaker/tagformats.json";
    $get_whoami         = "api/oauth/whoami.php";
    $get_acc_token      = "oauth/access_token.php";
    $get_req_token      = "oauth/request_token.php";
    $get_login          = "oauth/login.php";
    $get_notif          = "api/notifications/get_inst_notification.php";
    $get_post_login     = "oauth/post-login.php";
    $get_reg_user       = "oauth/register_user.php";
    $get_reg_user_test  = "oauth_test/register.html";

    // post path constants
    $post_bp            = "api/book_pings/book_ping.php";
    $post_users_edit    = "api/users/edit_user.php";
    $post_users_perm    = "api/users/edit_permissions.php";
    $post_users         = "api/users/register_user.php";
    $post_inst_edit     = "api/institutions/edit_institution.php";
    $post_inst_reg      = "api/institutions/register_institution.php";
    $post_login         = "oauth/login.php";
    
    // request path constants 
    
    // request handler variables
    $root       = $_SERVER['DOCUMENT_ROOT']."/";
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
        if (count($path_arr) === 2) {
            if ($req_type === "GET") {
                echo "path_arr[1]: ".$path_arr[1]."\n";
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
        if (count($path_arr) === 2) {
            if ($req_type === "GET") {
                $_GET['call_number'] = $path_arr[1];
                include $root.$get_lc_numbers;
            } else {
                throw_error(405, "405 - method not allowed");
            }
        } else {
            throw_error(404, "404 - not found");
        }
    }

    function handle_bp($path_arr, $req_type) {
        global $root, $post_bp, $get_bp, $get_bp_count, $get_bp_id;
        if (count($path_arr) === 2) {
            if ($req_type === "GET") {
                if ($path_arr[1] === "count") {
                    include $root.$get_bp_count;
                } else if ($path_arr[1] !== "") {
                    $_GET['book_ping_id'] = $path_arr[1];
                    include $root.$get_bp_id;
                } else if ($path_arr[1] === "") {
                    include $root.$get_bp;
                } else {
                    throw_error(404, "404 - not found");
                }
            } else if ($req_type === "POST") {
                if ($path_arr[1] === "") {
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

    }

    function handle_oauth($path_arr, $req_type) {

    }

    function handle_notif($path_arr, $req_type) {
        global $root, $get_notif;
        if (count($path_arr) === 2) {
            if ($req_type === "GET") {
                $_GET['inst_id'] = $path_arr[1];
                include $root.$get_notif;
            } else {
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

