<?php

class AccountDashboard {

    // Get Dashboard pages - Main Dashboard, Edit Profile, Post a Rental, Logout
    function get_page($page){
        global $connect_db, $widget, $current_user;
        $user = $widget->get_user();
        // First let's check if the usre is logged in
        // If not redirect to login page
        if(empty($current_user)) {
            $ref_url = isset($_GET['ref']) ? $_GET['ref'] : '';
            redirect('?page=login'.$ref_url);
        }
        // User is logged in
        // In case the users wannts to login..
        // Destroy session in action=logout
        else if( isset($_GET['action']) && $_GET['action'] == 'logout' ) {
            session_destroy();
            redirect($_SERVER['PHP_SELF']);
        }
        // User is logged in
        // Show account dashboard
        else {
            $form = new Form();
            $content = '
            <div class="container-lg py-5">
                <div class="row gx-5">
                    <div class="col-4">'                        
                        .$widget->dashboard_links();
                        if($user['user_type'] == 'company') $content .= $widget->followers();
                        $content .= $widget->message_box().
                    '</div>
                    <div class="col custom-padding">';
                        // action=post_listing = post equipment set for rent to database
                        if( isset($_GET['action']) && $_GET['action'] == 'post_listing' ) {
                            $form->set_mode('post');
                            if($user['user_type'] == 'company'){
                                // Only procces the submission on POST method
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    // Require category arrays
                                    require(ROOT_PATH.'data/categories.php');
                                    // Get submitted data
                                    $post_title = isset($_POST['set_name']) ? $_POST['set_name'] : null;
                                    $post_content = isset($_POST['set_desc']) ? $_POST['set_desc'] : null;
                                    $rent_price = isset($_POST['rent_price']) ? $_POST['rent_price'] : null;
                                    $event_cat_selected = isset($_POST['event_category']) ? $_POST['event_category'] : null;
                                    $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                    if(isset($_POST['event_type_Local'])) {
                                        $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                    } else {
                                        $event_type_local = null;
                                    }  
                                    if(isset($_POST['event_type_Foreign'])) {
                                        $event_type_foreign = is_numeric($_POST['event_type_Foreign']) ? $_POST['event_type_Foreign'] : null;
                                    } else {
                                        $event_type_foreign = null;
                                    }                            
                                    $venue_cat_selected = isset($_POST['venue_category']) ? $_POST['venue_category'] : null;
                                    if(isset($_POST['venue_type_Indoor'])){
                                        $venue_type_indoor = is_numeric($_POST['venue_type_Indoor']) ? $_POST['venue_type_Indoor'] : null;
                                    } else {
                                        $venue_type_indoor = null;
                                    }
                                    if(isset($_POST['venue_type_Outdoor'])){
                                        $venue_type_outdoor = is_numeric($_POST['venue_type_Outdoor']) ? $_POST['venue_type_Outdoor'] : null;
                                    } else {
                                        $venue_type_outdoor = null;
                                    }
                                    $event_cat_arr = [];
                                    foreach($event_cat_selected as $event_selected) {
                                        $event_cat_arr[] = $event_cat[$event_selected];
                                    }
                                    $event_category = count($event_cat_arr) == 1 ? $event_cat_arr[0] : implode(',',$event_cat_arr);
                                    $event_type_arr = [];
                                    if ($event_type_local === '0') {
                                        array_push($event_type_arr, 'Local');
                                    }
                                    if($event_type_foreign === '1') {
                                        array_push($event_type_arr, 'Foreign');
                                    }
                                    $event_type = count($event_type_arr) == 1 ? $event_type_arr[0] : implode(',',$event_type_arr);
                                    $venue_cat_arr = [];
                                    foreach($venue_cat_selected as $venue_selected) {
                                        $venue_cat_arr[] = $venue_cat[$venue_selected];
                                    }
                                    $venue_category = count($venue_cat_arr) == 1 ? $venue_cat_arr[0] : implode(',',$venue_cat_arr);
                                    $venue_type_arr = [];
                                    if ($venue_type_indoor === '0') {
                                        array_push($venue_type_arr, 'Indoor');
                                    }
                                    
                                    if($venue_type_outdoor === '1') {
                                        array_push($venue_type_arr, 'Outdoor');
                                    }
                                    $venue_type = count($venue_type_arr) == 1 ? $venue_type_arr[0] : implode(',',$venue_type_arr);
                                    $post_date = new DateTime();
                                    $post_date =  $post_date->format('Y-m-d H:i:s');
                                    $set_photo = isset($_FILES["set_photo"]) ? $_FILES["set_photo"]["name"] : null;
                                    $discount_option = isset($_POST['discount_settings']) ? $_POST['discount_settings'] : 0;
                                    // Let's upload the image file first
                                    $target_dir = ROOT_PATH."uploads/";
                                    $target_file = $target_dir . basename($_FILES["set_photo"]["name"]);
                                    $uploadOk = 1;
                                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                                    // Check if image file is a actual image or fake image
                                    if(!empty($_FILES["set_photo"]["tmp_name"])) {
                                        $check = getimagesize($_FILES["set_photo"]["tmp_name"]);
                                        if($check !== false) {
                                            $uploadOk = 1;
                                        } else {
                                            $uploadOk = 0;
                                        }
                                    }

                                    // Check if file already exists
                                    if (file_exists($target_file)) {
                                        $error = "Sorry, file already exists.";
                                        $uploadOk = 0;
                                    }

                                    // Check file size
                                    if ($_FILES["set_photo"]["size"] > 500000) {
                                        $error = "Sorry, your file is too large.";
                                        $uploadOk = 0;
                                    }

                                    // Allow certain file formats
                                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                    && $imageFileType != "gif" ) {
                                        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                        $uploadOk = 0;
                                    }

                                    // Check if $uploadOk is set to 0 by an error
                                    if ($uploadOk == 0) {
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    // if everything is ok, try to upload file
                                    } else {
                                        if (move_uploaded_file($_FILES["set_photo"]["tmp_name"], $target_file)) {
                                            // file uploaded
                                            //$content .= '<div class="alert alert-success" role="alert">File Uploaded</div>';
                                        } else {
                                            $error = 'Sorry, there was an error uploading your file.';
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                    }

                                    // Insert data to table posts
                                    $user_data = "INSERT INTO posts (
                                        user,
                                        title,
                                        content,
                                        post_date,
                                        modified_date,
                                        rent_price,
                                        event_cat,
                                        event_type,
                                        venue_cat,
                                        venue_type,
                                        set_photo,
                                        discount
                                    )
                                    VALUES (
                                        '$current_user',
                                        '$post_title',
                                        '$post_content',
                                        '$post_date',
                                        '$post_date',
                                        '$rent_price',
                                        '$event_category',
                                        '$event_type',
                                        '$venue_category',
                                        '$venue_type',
                                        '$set_photo',
                                        '$discount_option'
                                    )";

                                    // If successful, show confirmation text and link
                                    if ($connect_db->query($user_data) === TRUE) {
                                        $content .= '<div class="alert alert-success" role="alert">Post submitted! <a href="?page=search&view_listing='.$connect_db->insert_id.'">View your listing</a>.</div>';
                                        redirect('?page=account_dashboard&action=edit_listing&id='.$connect_db->insert_id.'&ref=post_submitted');
                                    } else {
                                        $error = 'Sorry, your post was not submitted. You may try again after a short while.';
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    }
                                }
                                $content .= '
                                <div class="card p-5 mb-5">
                                    <h3 class="mb-5">Post a Listing</h3>'
                                    .$form->post_form().'
                                </div>   
                                ';
                            } else {
                                redirect('?page=account_dashboard');
                            }                    
                        } 
                        // Edit listing
                        else if( isset($_GET['action']) && $_GET['action'] == 'edit_listing' ) {
                            if($user['user_type'] == 'company'){
                                if( !isset($_GET['id'])){
                                    redirect('?page=account_dashboard');
                                } else {                                 

                                    // Get post data from database
                                    $get_posts = $connect_db->prepare("SELECT user FROM posts WHERE ID = ?");
                                    $get_posts->bind_param('i', $_GET['id']);
                                    $get_posts->execute();
                                    $get_posts->store_result();
                                    $get_posts->bind_result($userid);
                                    $get_posts->fetch();

                                    // If logged user doesn't owned the listing, do not allow or redirect to dashboard
                                    if( $userid != $current_user){
                                        redirect('?page=account_dashboard');
                                    }
                                    // Only procces the submission on POST method
                                    if ($userid == $current_user && $_SERVER["REQUEST_METHOD"] == "POST") {
                                        // Require category arrays
                                        require(ROOT_PATH.'data/categories.php');
                                        // Get submitted data
                                        $post_title = isset($_POST['set_name']) ? $_POST['set_name'] : null;
                                        $post_content = isset($_POST['set_desc']) ? $_POST['set_desc'] : null;
                                        $rent_price = isset($_POST['rent_price']) ? $_POST['rent_price'] : null;
                                        $event_cat_selected = isset($_POST['event_category']) ? $_POST['event_category'] : null;
                                        $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                        if(isset($_POST['event_type_Local'])) {
                                            $event_type_local = is_numeric($_POST['event_type_Local']) ? $_POST['event_type_Local'] : null;
                                        } else {
                                            $event_type_local = null;
                                        }  
                                        if(isset($_POST['event_type_Foreign'])) {
                                            $event_type_foreign = is_numeric($_POST['event_type_Foreign']) ? $_POST['event_type_Foreign'] : null;
                                        } else {
                                            $event_type_foreign = null;
                                        }                            
                                        $venue_cat_selected = isset($_POST['venue_category']) ? $_POST['venue_category'] : null;
                                        if(isset($_POST['venue_type_Indoor'])){
                                            $venue_type_indoor = is_numeric($_POST['venue_type_Indoor']) ? $_POST['venue_type_Indoor'] : null;
                                        } else {
                                            $venue_type_indoor = null;
                                        }
                                        if(isset($_POST['venue_type_Outdoor'])){
                                            $venue_type_outdoor = is_numeric($_POST['venue_type_Outdoor']) ? $_POST['venue_type_Outdoor'] : null;
                                        } else {
                                            $venue_type_outdoor = null;
                                        }
                                        $event_cat_arr = [];
                                        foreach($event_cat_selected as $event_selected) {
                                            $event_cat_arr[] = $event_cat[$event_selected];
                                        }
                                        $event_category = count($event_cat_arr) == 1 ? $event_cat_arr[0] : implode(',',$event_cat_arr);
                                        $event_type_arr = [];
                                        if ($event_type_local === '0') {
                                            array_push($event_type_arr, 'Local');
                                        }
                                        if($event_type_foreign === '1') {
                                            array_push($event_type_arr, 'Foreign');
                                        }
                                        $event_type = count($event_type_arr) == 1 ? $event_type_arr[0] : implode(',',$event_type_arr);
                                        $venue_cat_arr = [];
                                        foreach($venue_cat_selected as $venue_selected) {
                                            $venue_cat_arr[] = $venue_cat[$venue_selected];
                                        }
                                        $venue_category = count($venue_cat_arr) == 1 ? $venue_cat_arr[0] : implode(',',$venue_cat_arr);
                                        $venue_type_arr = [];
                                        if ($venue_type_indoor === '0') {
                                            array_push($venue_type_arr, 'Indoor');
                                        }
                                        
                                        if($venue_type_outdoor === '1') {
                                            array_push($venue_type_arr, 'Outdoor');
                                        }
                                        $venue_type = count($venue_type_arr) == 1 ? $venue_type_arr[0] : implode(',',$venue_type_arr);
                                        $set_photo = isset($_FILES["set_photo"]) ? $_FILES["set_photo"]["name"] : null;
                                        $discount_option = isset($_POST['discount_settings']) ? $_POST['discount_settings'] : 0;
                                        
                                        
                                        // Let's upload the image file first
                                        $target_dir = ROOT_PATH."uploads/";
                                        $target_file = $target_dir . basename($_FILES["set_photo"]["name"]);
                                        $uploadOk = 1;
                                        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                                        // Upload image if ther's any
                                        if(!empty($_FILES["set_photo"]["tmp_name"])) {
                                            // Check if image file is a actual image or fake image
                                            $check = getimagesize($_FILES["set_photo"]["tmp_name"]);
                                            if($check !== false) {
                                                $uploadOk = 1;
                                            } else {
                                                $uploadOk = 0;
                                            }

                                            // Check if file already exists
                                            if (file_exists($target_file)) {
                                                $error = "Sorry, file already exists.";
                                                $uploadOk = 0;
                                            }

                                            // Check file size
                                            if ($_FILES["set_photo"]["size"] > 500000) {
                                                $error = "Sorry, your file is too large.";
                                                $uploadOk = 0;
                                            }

                                            // Allow certain file formats
                                            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                            && $imageFileType != "gif" ) {
                                                $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                                $uploadOk = 0;
                                            }                                            
                                            // Check if $uploadOk is set to 0 by an error
                                            if ($uploadOk == 0) {
                                                $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                            // if everything is ok, try to upload file
                                            } else {
                                                if (move_uploaded_file($_FILES["set_photo"]["tmp_name"], $target_file)) {
                                                    // file uploaded
                                                    //$content .= '<div class="alert alert-success" role="alert">File Uploaded</div>';
                                                } else {
                                                    $error = 'Sorry, there was an error uploading your file.';
                                                    $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                                }
                                            }
                                        }
                                        $modified_date = new DateTime();
                                        $modified_date = $modified_date->format('Y-m-d H:i:s');
                                        $listing_id = $_GET['id'];
                                        if($set_photo) {
                                            $listing_update = "UPDATE posts SET
                                            title ='$post_title',
                                            content ='$post_content',
                                            rent_price ='$rent_price',
                                            modified_date = '$modified_date',
                                            event_cat = '$event_category',
                                            event_type = '$event_type',
                                            venue_cat = '$venue_category',
                                            venue_type = '$venue_type',
                                            set_photo = '$set_photo',
                                            discount = '$discount_option'
                                            WHERE id='$listing_id'"; 
                                        } else {
                                            $listing_update = "UPDATE posts SET
                                            title ='$post_title',
                                            content ='$post_content',
                                            rent_price ='$rent_price',
                                            modified_date = '$modified_date',
                                            event_cat = '$event_category',
                                            event_type = '$event_type',
                                            venue_cat = '$venue_category',
                                            venue_type = '$venue_type',
                                            discount = '$discount_option'
                                            WHERE id='$listing_id'"; 
                                        }                                                            
                                        // If successful, show confirmation text and link
                                        if ($connect_db->query($listing_update) === TRUE) {
                                            $content .= '<div class="alert alert-success" role="alert">Post updated! <a href="?page=search&view_listing='.$listing_id.'">View your listing</a></div>';
                                        } else {
                                            $error = 'Sorry, your post was not updated. You may try again after a short while.';
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                        
                                    }
                                    $listing_id = isset($_GET['id']) ? $_GET['id'] : null;
                                    $form->set_id($listing_id);
                                    $form->set_mode('update');
                                    if(isset($_GET['ref']) && $_GET['ref'] == 'post_submitted') {
                                        $content .= '<div class="alert alert-success" role="alert">Post submitted! <a href="?page=search&view_listing='.$listing_id.'">View your listing</a>.</div>';
                                    }
                                    $content .= '
                                    <div class="card p-5 mb-5">
                                        <h3 class="mb-5">Edit a Listing</h3>'                                
                                        .$form->post_form().'
                                    </div> 
                                    ';
                                }   
                            }

                            else {
                                redirect('?page=account_dashboard');
                            }                        
                        }

                        // Delete Listing
                        else if( isset($_GET['action']) && $_GET['action'] == 'delete_listing' ) {
                            $listing_id = isset($_GET['id']) ? $_GET['id'] : null;
                            if($user['user_type'] == 'company'){
                                if( !isset($_GET['id'])){
                                    redirect('?page=account_dashboard');
                                } else { 

                                    // Get post data from database
                                    $get_posts = $connect_db->prepare("SELECT user FROM posts WHERE ID = ?");
                                    $get_posts->bind_param('i', $listing_id);
                                    $get_posts->execute();
                                    $get_posts->store_result();
                                    $get_posts->bind_result($userid);
                                    $get_posts->fetch();
                                    // If logged user doesn't owned the listing, do not allow or redirect to dashboard
                                    if( $userid != $current_user){
                                        redirect('?page=account_dashboard');
                                    } else {
                                        $stmt = $connect_db->prepare('DELETE FROM posts WHERE ID = ?');
                                        $stmt->bind_param('i', $listing_id);
                                        $stmt->execute();                                                          
                                        // If successful, show confirmation text and link
                                        if ($stmt -> affected_rows == 1) {
                                            redirect('?page=account_dashboard&ref=listing_deleted');
                                        } else {
                                            $error = 'Sorry, your post was not deleted. You may try again after a short while.';
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        }
                                        
                                    }
                                }   
                            }

                            else {
                                redirect('?page=account_dashboard');
                            }                        
                        }

                        // Edit Profile // Upload avatar
                        else if( isset($_GET['action']) && $_GET['action'] == 'edit_profile' ) {
                            if( isset($_GET['update']) && $_GET['update'] == 'photo') {
                                // Only procces the submission on POST method
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                                    // Let's upload the image file
                                    $target_dir = ROOT_PATH."uploads/";
                                    $temp = explode(".", $_FILES["profile_photo"]["name"]);
                                    $newfilename = round(microtime(true)) . '.' . end($temp);
                                    $target_file = $target_dir . basename($newfilename);
                                    $uploadOk = 1;
                                    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                                    // Upload image if ther's any
                                    if(isset($_FILES["profile_photo"])) {
                                        // Check if image file is a actual image or fake image
                                        $check = getimagesize($_FILES["profile_photo"]["tmp_name"]);
                                        if($check !== false) {
                                            $uploadOk = 1;
                                        } else {
                                            $uploadOk = 0;
                                        }

                                        // Check if file already exists
                                        if (file_exists($target_file)) {
                                            $error = "Sorry, file already exists.";
                                            $uploadOk = 0;
                                        }

                                        // Check file size
                                        if ($_FILES["profile_photo"]["size"] > 500000) {
                                            $error = "Sorry, your file is too large.";
                                            $uploadOk = 0;
                                        }

                                        // Allow certain file formats
                                        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                                        && $imageFileType != "gif" ) {
                                            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                                            $uploadOk = 0;
                                        }

                                        // Check if $uploadOk is set to 0 by an error
                                        if ($uploadOk == 0) {
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        // if everything is ok, try to upload file
                                        } else {                                        
                                            if (move_uploaded_file($_FILES["profile_photo"]["tmp_name"], $target_file)) {
                                                // file uploaded
                                                //$content .= '<div class="alert alert-success" role="alert">File Uploaded</div>';
                                                $avatar_update = "UPDATE users SET
                                                    avatar ='$newfilename'
                                                    WHERE ID='$current_user'";

                                                // If successful, show confirmation text and link
                                                if ($connect_db->query($avatar_update) === TRUE) {
                                                    $content .= '<div class="alert alert-success" role="alert">Avatar/logo updated.</div>';
                                                } else {
                                                    $error = 'Sorry, your photo was not updated.';
                                                    $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                                }
                                            } else {
                                                $error = 'Sorry, there was an error uploading your file.';
                                                $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                            }
                                        }                                
                                    }                                
                                }
                                $content .='
                                    <div class="card p-5 mb-5">
                                    <h3 class="mb-5">Update Photo</h3>
                                    <div class="row gx-5 mb-3">
                                    <div class="col text-center">';
                                    $widget->set_profile($current_user);
                                    $content .= $widget->get_avatar();           
                                    $content .='
                                    </div>
                                    </div>
                                    <div class="row mt-5">
                                    <div class="col">';
                                    $content .= '<p class="text-center">Recommended size: 200 x 200 pixels.</p>';
                                    $content .= $form->upload_photo_form();
                                    $content .='
                                    </div>
                                    </div>
                                    </div>
                                ';
                            } else {
                                // Only procces the submission on POST method
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                                    // UPDATE user data to mySQL
                                    $company_name = $_POST['company_name'];
                                    $company_address = $_POST['company_address'];
                                    $first_name = $_POST['first_name'];
                                    $last_name = $_POST['last_name'];
                                    $email_address = $_POST['email_address'];
                                    $phone_number = $_POST['phone_number'];
                                    $mobile_number = $_POST['mobile_number'];
                                    $update_profile = "UPDATE users SET
                                    company_name ='$company_name',
                                    company_address ='$company_address',
                                    first_name ='$first_name',
                                    last_name = '$last_name',
                                    email = '$email_address',
                                    phone_number = '$phone_number',
                                    mobile_number = '$mobile_number'
                                    WHERE id='$current_user'"; 

                                    // If successful, show confirmation text
                                    if ($connect_db->query($update_profile) === TRUE) {
                                        $content .= '<div class="alert alert-success" role="alert">Profile updated.</div>';
                                    } else {
                                        $error = 'Sorry, your profile was not updated.';
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    }  

                                }
                                $form->set_id($current_user);
                                $content .='
                                    <div class="card p-5 mb-5">
                                    <h3 class="mb-5">Edit Profile</h3>
                                    <div class="row gx-5 mb-3">';
                                    $content .= $form->profile_form();
                                    $content .= '
                                    </div>
                                    </div>
                                ';
                            }
                        }

                        // Change Password
                        else if( isset($_GET['action']) && $_GET['action'] == 'update_password' ) {

                            // Only procces the submission on POST method
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                                // UPDATE user data to mySQL
                                $password = isset($_POST['password']) ? $_POST['password']  : null;
                                $hashed_password = isset($_POST['password']) ? password_hash($password, PASSWORD_DEFAULT) : null; // we don't store password in db, only hashed ones
                                $update_password = "UPDATE users SET                                
                                    pass = '$hashed_password'
                                    WHERE id='$current_user'"; 

                                    // If successful, show confirmation text
                                    if ($connect_db->query($update_password) === TRUE) {
                                        $content .= '<div class="alert alert-success" role="alert">Password updated.</div>';
                                        session_destroy();
                                        $ref_url = '&status=password_updated';
                                        redirect('?page=login'.$ref_url);
                                    } else {
                                        $error = 'Sorry, your password was not updated.';
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    }   
                            }
                            
                            $form->set_id($current_user);
                            $content .='
                                <div class="card p-5 mb-5">
                                <h3 class="mb-5">Change Password</h3>
                                <div class="row gx-5 mb-3">
                                <div class="col">';
                                $content .= $form->password_form();
                                $content .= '
                                </div>
                                </div>
                                </div>
                            ';

                        }
                        // View / Add Discounts
                        else if( isset($_GET['view']) && $_GET['view'] == 'discount_settings' ) {
                            $discount_id = isset($_GET['id']) ? $_GET['id'] : null;
                            $post_date = new DateTime();
                            $post_date =  $post_date->format('Y-m-d H:i:s');
                            
                            // Only procces the submission on POST method
                            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                // Discount Title
                                $discount = isset($_POST['discount_settings']) ? $_POST['discount_settings'] : 0;

                                 // Update data to table discounts
                                 $discount_update = "UPDATE users SET
                                 follower_discount ='$discount'
                                 WHERE ID='$current_user'";

                                 // If successful, show confirmation text
                                 if ($connect_db->query($discount_update) === TRUE) {   
                                    $user = $widget->get_user();                                     
                                     $content .= '<div class="alert alert-success" role="alert">Global discount updated.</div>';
                                 } else {
                                     $error = 'Sorry, your discount was not saved. You may try again after a short while.';
                                     $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                 }
                            }  

                            $content .='
                            <div class="card p-5 mb-5">
                                <div class="row d-flex align-items-center mb-3">
                                    <div class="col"><h3 class="mb-0">Global Discount Settings</h3></div>
                                </div>
                                <div class="row gx-5 mb-3">';
                                if(empty($user['follower_discount']) || $user['follower_discount'] == 0) {
                                    $content .= '<p>Currently, you are not giving any discount to your followers.</p>';
                                } else {
                                    $content .= '<p>The discount you are giving to your followers is set to <strong>'.$user['follower_discount'].'% off</strong> in all of your listings.</p>';
                                }                                    
                                    $content .= '<p>You can set individual discount per listing, just edit a specific listing and choose a discount option.</p><p>Please take note that any individual discount will overwrite the global discount option set in this page.</p>';
                                    $content .= '<p>You can change the global discount option below:</p>';
                                    $content .= $form->discount_form();
                                    $content .= '
                                </div>
                            </div>
                            ';

                        }
                        // View Followers
                        else if( isset($_GET['view']) && $_GET['view'] == 'followers' ) {
                            if($user['user_type'] == 'client') {
                                redirect('?page=account_dashboard');
                            } else {
                                $content .='
                                <div class="card p-5 mb-5">
                                <h3 class="mb-5">View Followers</h3>';
                                $content .= $widget->get_follower_list();
                                $content .= '
                                </div>
                            ';
                            }

                        }

                        // View Messages
                        else if( isset($_GET['view']) && $_GET['view'] == 'messages' ) { 
                            if (isset($_GET['thread_id'])) {
                                $thread_id = isset($_GET['thread_id']) ? $_GET['thread_id'] : null;
                                $widget->set_thread($thread_id);
                                $message_info = $widget->get_thread_info();
                                // Only procces the submission on POST method
                                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                    $sender_id = $current_user;
                                    $recipient_id = $message_info['recipient_id'];                                                                      
                                    $message_subject = $message_info['subject'];
                                    $message_body = addslashes($_POST['reply_message']);
                                    $sender_status = 1;
                                    $recipient_status = 0;
                                    $message_date = new DateTime();
                                    $message_date =  $message_date->format('Y-m-d H:i:s');
                                    $is_first_message = 0;
                                    $send_message = "INSERT INTO messages (
                                        thread_id,
                                        sender_id,
                                        recipient_id,
                                        message_subject,
                                        message_body,
                                        sender_status,
                                        recipient_status,
                                        message_date,
                                        is_first_message
                                    )
                                    VALUES (
                                        '$thread_id',
                                        '$sender_id',
                                        '$recipient_id',
                                        '$message_subject',
                                        '$message_body',
                                        '$sender_status',
                                        '$recipient_status',
                                        '$message_date',
                                        '$is_first_message'
                                    )";
                                    // If successful, show confirmation text
                                    if ($connect_db->query($send_message) === TRUE) {

                                    } else {
                                        $error = 'Sorry, your message was not sent.';
                                        $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                    }
                                }
                                $content .='
                                    <div class="card p-5 mb-5">';                                    
                                    $content .= $widget->get_message_thread();
                                    $content .= '
                                    </div>
                                ';
                                $form->set_id($thread_id);
                                $content .='
                                    <div class="card p-5 mb-5">
                                        <h3 class="mb-5">Reply</h3>
                                        <div class="row gx-1 mb-3 d-flex align-items-center">
                                            <div class="col">';
                                            $content .= $form->reply_form();
                                            $content .= '
                                            </div>
                                        </div>
                                    </div>
                                ';                                                                

                            } else {
                                if(isset($_GET['ref'])){
                                    $content .= '<div class="alert alert-success" role="alert">Message sent.</div>';
                                }

                                $widget->set_position('content');
                                $content .='
                                    <div class="card p-5 mb-5">
                                    <h3 class="mb-5">Messages</h3>';
                                    $content .= $widget->get_messages();
                                    $content .= '
                                    </div>
                                ';
                            }                            
                            

                        }

                        // Create Message
                        else if( (isset($_GET['action']) && $_GET['action'] == 'create_message') && isset($_GET['recipient_id'])) {

                            // Recipient ID
                            $recipient_id = isset($_GET['recipient_id']) ? $_GET['recipient_id'] : null;

                            // Prepare sql statement to get profile data
                            $check_user = $connect_db->prepare("SELECT company_name FROM users WHERE ID = ?");
                            $check_user -> bind_param('i', $recipient_id);
                            $check_user->execute();
                            $check_user -> store_result();
                            $check_user -> bind_result($company_name);

                            // statement result
                            if($check_user->num_rows == 1) {
                                    // Get result
                                if($check_user->fetch()) {
                                    $widget->set_profile($recipient_id);
                                    $form->set_id($recipient_id);
                                    // Only procces the submission on POST method
                                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                                        if(empty($_POST['message'] || empty($_POST['subject']))){
                                            $error = 'One or more required fields are empty.';
                                            $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                        } else {
                                            // Prepare sql statement to check messages
                                            $check_thread = $connect_db->prepare("SELECT ID FROM messages");
                                            $check_thread->execute();
                                            $check_thread->store_result();
                                            $check_thread->bind_result($message_id);

                                            // Get max thread id number
                                            $max_thread_id = 0;
                                            $thread_id_query = $connect_db->query("SELECT MAX(thread_id) as max_thread_id FROM messages");
                                            if($thread_id_query->num_rows > 0) {
                                                while($row = $thread_id_query->fetch_assoc()) {
                                                    $max_thread_id = $row['max_thread_id'];
                                                  }
                                            }
                                            $new_thread_id = $max_thread_id + 1;
                                            $thread_id = $check_thread->num_rows == 0 ? 1 : $new_thread_id;
                                            $sender_id = $current_user;
                                            $message_subject = isset($_POST['subject']) ? $_POST['subject'] : null;
                                            $message_body = isset($_POST['message']) ?  addslashes($_POST['message']) : null;
                                            $sender_status = 1;
                                            $recipient_status = 0;
                                            $message_date = new DateTime();
                                            $message_date =  $message_date->format('Y-m-d H:i:s');
                                            $is_first_message = 1;
                                            $send_message = "INSERT INTO messages (
                                                thread_id,
                                                sender_id,
                                                recipient_id,
                                                message_subject,
                                                message_body,
                                                sender_status,
                                                recipient_status,
                                                message_date,
                                                is_first_message
                                            )
                                            VALUES (
                                                '$thread_id',
                                                '$sender_id',
                                                '$recipient_id',
                                                '$message_subject',
                                                '$message_body',
                                                '$sender_status',
                                                '$recipient_status',
                                                '$message_date',
                                                '$is_first_message'
                                            )";
                                            // If successful, show confirmation text
                                            if ($connect_db->query($send_message) === TRUE) {
                                                $content .= '<div class="alert alert-success" role="alert">Message sent.</div>';
                                                redirect('?page=account_dashboard&view=messages&ref=message_sent');
                                            } else {
                                                $error = 'Sorry, your message was not sent.';
                                                $content .= '<div class="alert alert-danger" role="alert">'.$error.'</div>';
                                            }
                                        }
                                    }
                                    $content .='
                                    <div class="card p-5 mb-5">
                                        <h3 class="mb-5">Send Message</h3>
                                        <div class="row gx-1 mb-3 d-flex align-items-center">
                                            <div class="col-auto fw-bold me-2">To: </div>';
                                            $content .= '<div class="col-auto" style="width: 70px;">';
                                            $content .= $widget->get_avatar();
                                            $content .= '</div>
                                            <div class="col fw-bold">'.$company_name.'</div>
                                        </div>
                                        <div class="row gx-1 mb-3 d-flex align-items-center">
                                            <div class="col">';
                                            $content .= $form->message_form();
                                            $content .= '
                                            </div>
                                        </div>
                                    </div>
                                    ';
                                }
                            }

                        }
                        // Account Dashboard Main/Home
                        else {
                            if($user['user_type'] == 'company'){
                                $widget->set_profile($current_user);
                                $content .= '<h3 class="mb-5">My Listings</h3>';
                                if(isset($_GET['ref']) && $_GET['ref'] == 'listing_deleted'){
                                    $content .= '<div class="alert alert-success" role="alert">Listing deleted</div>';
                                }                            
                                $content .= $widget->get_listings();  
                            }

                            else {  
                                if(isset($_GET['remove_bookmark'])){
                                    $listing_id = $_GET['remove_bookmark'];
                                    $check_bookmark = $connect_db->prepare("SELECT ID FROM bookmarks WHERE client_id = ? AND listing_id = ?");
                                    $check_bookmark -> bind_param('ii', $current_user, $listing_id);
                                    $check_bookmark->execute();
                                    $check_bookmark -> store_result();
                                    $check_bookmark -> bind_result($bookmark_id);
                                    $check_bookmark->fetch();
                                    
                                    if($check_bookmark->num_rows == 1) {
                                        $stmt = $connect_db->prepare('DELETE FROM bookmarks WHERE ID = ?');
                                        $stmt->bind_param('i', $bookmark_id);
                                        $stmt->execute();
                                    }                                    
                                }
                                $widget->set_profile($current_user);
                                $content .= $widget->get_follow_list();   
                                $content .='<h3 class="mb-4">Bookmarks</h3>';
                                $content .= $widget->get_bookmarks();
                            }                                                                                
                        }
                        $content .= '                        
                    </div>
                </div>
            </div>                
            ';
            return $content;
        }
    }

}