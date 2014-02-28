<?php
    // get path constants
    $get_book_tags = "api/lc2bin/book_tags.php";
    $get_lc_numbers = "api/lc2bin/lc_numbers.php";
    $get_bp_count = "api/book_pings/get_ping_count_book.php";
    $get_bp_id = "api/book_pings/get_by_id.php";
    $get_user_perm = "api/users/get_permissions.php";
    $get_act_email = "api/users/activate_email.php";
    $get_users_avail = "api/users/user_available.php";
    $get_email_reg = "api/users/email_registered.php";
    $get_user = "api/users/get_user.php";
    $get_users = "api/users/get_user.php";
    $get_act_inst = "api/institutions/activate_inst.php";
    $get_inst_avail = "api/institutions/inst_available.php";
    $get_inst = "api/institutions/get_institution.php";
    $get_inst_mult = "api/institutions/get_institutions.php";
    $get_bp = "api/book_pings/book_pings_get.php";
    $get_tags = "api/tagmaker/pdfPrint2.php";
    $get_formats = "api/tagmaker/tagformats.json";
    $get_whoami = "api/oauth/whoami.php";
    $get_acc_token = "oauth/access_token.php";
    $get_req_token = "oauth/request_token.php";
    $get_login = "oauth/login.php";
    $get_notif = "api/notifications/get_inst_notification.php";
    $get_post_login = "oauth/post-login.php";
    $get_reg_user = "oauth/register_user.php";
    $get_reg_user_test = "oauth_test/register.html";

    // post path constants
    $post_bp = "api/book_pings/book_ping.php";
    $post_users_edit = "api/users/edit_user.php";
    $post_users_perm = "api/users/edit_permissions.php";
    $post_users = "api/users/register_user.php";
    $post_inst_edit = "api/institutions/edit_institution.php";
    $post_inst_reg = "api/institutions/register_institution.php";
    $post_login = "oauth/login.php";

    // request handler variables
    $root = $_SERVER['DOCUMENT_ROOT']."/";
    $path = $_GET['path'];
    echo $path."\n";
    $req_type = $_GET['type'];

    if ($req_type === 'GET') {
        switch ($path) {
        case "make_tags/paper_formats": include $root.$get_formats; break;
        }
    } else if ($req_type === 'POST') {
        switch ($path) {
        
        }
    } else {
        // throw 404
        http_response_code(404);
        echo "<h1>404 not found</h1>";
    }
?>

