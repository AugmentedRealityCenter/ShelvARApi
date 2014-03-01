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
        case "book_tags":       include $root.$get_book_tags; break;
        case "lc_numbers":      include $root.$get_lc_numbers; break;
        case "book_pings":      handle_bp($path_arr, $req_type); break;
        case "users":           handle_users($path_arr, $req_type); break;
        case "institutions":    handle_inst($path_arr, $req_type); break;
        case "make_tags":       handle_mt($path_arr, $req_type); break;
        case "oauth":           handle_oauth($path_arr, $req_type);
        case "oauth_test":      include $root.$get_reg_user_test; break; 
        case "notifications":   include $root.$get_notif; break;
        default:                throw_404(); break;
    }

    function handle_bp($path_arr, $req_type) {
        global $root, $post_bp, $get_bp, $get_bp_count, $get_bp_id;
        if (count($path_arr) === 1) {
            if ($req_type === "POST") {
                include $root.$post_bp;
            } else if($req_type === "GET") {
                include $root.$get_bp;
            } else {
                throw_405();
            }
        } else if (count($path_arr) === 2) {
            if ($req_type === "GET") {
                if ($path_arr[1] === "count") {
                    print_r($_POST);
                    include $root.$get_bp_count;
                } else {
                    include $root.$get_bp_id;
                }
            } else {
                throw_405();
            }
        } else {
            throw_404();
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

    function throw_404() {
        // throw 404
        http_response_code(404);
        echo "<h1>404 - not found</h1>";
    }

    function throw_405() {
        http_response_code(405);
        echo "<h1>405 - method not allowed</h1>";
    }
