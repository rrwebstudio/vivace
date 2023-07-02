<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class Form {
    
    //Properties
    public $page;
    public $id;
    public $listing_id;
    public $mode;
    public $action; 
    public $has_error;

    function set_id($id) {
        $this->id = $id;
    }

    function set_listing($listing_id) {
        $this->listing_id = $listing_id;
    }

    function set_mode($mode) {
        $this->mode = $mode;
    }

    function set_action($action) {
        $this->action = $action;
    }

    function has_error($has_error) {
        $this->has_error = $has_error;
    }

    // Methods
    function search_form() {

        // Require category arrays
        require(ROOT_PATH.'data/categories.php');

        if(
            (isset($_SESSION['event_cat_key'])
        && isset($_SESSION['event_type_key'])
        && isset($_SESSION['venue_cat_key'] )
        && isset($_SESSION['venue_type_key'])
        && isset($_SESSION['price_range_key'])) || 
        (isset($_POST['event_category'])
        && isset($_POST['event_type'])
        && isset($_POST['venue_cat'] )
        && isset($_POST['venue_type'])
        && isset($_POST['price_range']))
        ) {
            $search_section_class = 'session_search';
        } else {
            $search_section_class = '';
        }

        $search_form = '
        <div id="search-section" class="py-3 position-relative '.$search_section_class.'" style="z-index: 1;">
            <p class="validation-msg text-danger text-center small"></p>
            <div class="container-lg rounded shadow p-0">            
                <form class="row gx-3 gy-2 align-items-center" id="search_form" method="post" action="'.SITE_URL.'/?page=search&action=view_results">
                    <div class="col-3 col-event-cat border-end mt-0 ps-3">
                        <div class="p-2 rounded border border-2 border-primary my-2">
                        <label for="event_category">Event Category</label>
                        <select class="form-select border-0" id="event_category" name="event_category" required>
                        <option></option>';
                        $val_counter1 = 0;                  
                        foreach($event_cat as $event_category) {
                            $selected_key = isset($_POST['event_category']) ? $_POST['event_category'] : (isset($_SESSION['event_cat_key']) ? $_SESSION['event_cat_key'] : '' );
                            $is_selected = $selected_key == $val_counter1 ? 'selected' : '';
                            $search_form .= '<option value="'.$val_counter1.'" '.$is_selected.'>'.$event_category.'</option>';                    
                            $val_counter1++;
                        }
                        $search_form .= '
                        </select>
                        </div>                
                        </div>
                        <div class="col-2 col-event-type text-muted border-end mt-0">
                        <div class="p-2 rounded border border-2 border-light my-2">
                        <label for="event_category">Event Type</label>
                        <select class="form-select border-0" id="event_type" name="event_type" required>
                        <option></option>';
                        $val_counter2 = 0;
                        foreach($event_types as $event_type) {
                            $selected_key = isset($_POST['event_type']) ? $_POST['event_type'] : (isset($_SESSION['event_type_key']) ? $_SESSION['event_type_key'] : '' );
                            $is_selected = $selected_key == $val_counter2 ? 'selected' : '';
                            $search_form .= '<option value="'.$val_counter2.'" '.$is_selected.'>'.$event_type.'</option>';                    
                            $val_counter2++;
                        }
                        $search_form .= '
                        </select>
                        </div>                
                    </div>
                    <div class="col-2 col-venue-cat text-muted border-end mt-0">
                        <div class="p-2 rounded border border-2 border-light my-2">
                        <label for="venue_type">Venue Category</label>
                        <select class="form-select border-0" id="venue_cat" name="venue_cat" required>
                        <option></option>';
                        $val_counter3 = 0;
                        foreach($venue_cat as $venue_cat) {
                            $selected_key = isset($_POST['venue_cat']) ? $_POST['venue_cat'] : (isset($_SESSION['venue_cat_key'] )? $_SESSION['venue_cat_key'] : '' );
                            $is_selected = $selected_key == $val_counter3 ? 'selected' : '';
                            $search_form .= '<option value="'.$val_counter3.'" '.$is_selected.'>'.$venue_cat.'</option>';                    
                            $val_counter3++;
                        }
                        $search_form .= '
                        </select>
                        </div>                                
                        </div>
                        <div class="col col-venue-type text-muted border-end mt-0">
                        <div class="p-2 rounded border border-2 border-light my-2">
                        <label for="venue_type">Venue Type</label>
                        <select class="form-select border-0" id="venue_type" name="venue_type" required>
                        <option></option>';
                        $val_counter4 = 0;
                        foreach($venue_type as $venue_type) {
                            $selected_key = isset($_POST['venue_type']) ? $_POST['venue_type'] :(isset($_SESSION['venue_type_key']) ? $_SESSION['venue_type_key'] : '' );
                            $is_selected = $selected_key == $val_counter4 ? 'selected' : '';
                            $search_form .= '<option value="'.$val_counter4.'" '.$is_selected.'>'.$venue_type.'</option>';                    
                            $val_counter4++;
                        }
                        $search_form .= '
                        </select>
                        </div>                
                    </div>
                    <div class="col-2 col-price-range text-muted border-end mt-0">
                        <div class="p-2 rounded border border-2 border-light my-2">
                        <label for="price_range">Price Range</label>
                        <select class="form-select border-0" id="price_range" name="price_range" required>
                        <option></option>';
                        $val_counter5 = 0;
                        foreach($price_range as $price_range) {
                            $selected_key = isset($_POST['price_range']) ? $_POST['price_range'] : (isset($_SESSION['price_range_key']) ? $_SESSION['price_range_key'] : '' );
                            $is_selected = $selected_key == $val_counter5 ? 'selected' : '';
                            $search_form .= '<option value="'.$val_counter5.'" '.$is_selected.'>'.$price_range.'</option>';                    
                            $val_counter5++;
                        }
                        $search_form .= '
                        </select>
                        </div>                
                    </div>
                    <div class="col-1 mt-0 p-0 h-100">
                        <div class="d-grid h-100">
                        <button type="submit" class="btn btn-primary p-3 rounded-0 rounded-end h-100">Search</button>
                        </div>                                
                    </div>
                </form>
            </div>
        </div>                    
        ';
        
        return $search_form;
    }

    function register_form() {
        $ref_url = isset($_GET['ref']) ? '&ref='.$_GET['ref'] : '';
        $register_form =  '

        <div class="container position-absolute top-50 start-50 translate-middle text-white">
            <div class="row">
                <div class="col-7 py-4 px-5 shadow-lg" style="background-color: rgba(1, 1, 39, 0.8);">
                    <h1 class="card-title mt-3 text-center">Create Account</h1>
                    <p class="text-center">Get started with your free account</p>
                    <form id="register_form" method="post" action="'.SITE_URL.'/?page=register&action=register_account'.$ref_url.'">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="company_name">Company Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" id="company_name" name="company_name" placeholder="Enter your company name" required>
                        </div>
                        <div class="col">
                        <label class="fw-bold mb-2" for="company_address">Company Address:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-2" id="company_address" name="company_address" placeholder="Enter your company address" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="first_name">First Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" id="first_name" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <label class="fw-bold mb-2" for="last_name">Last Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" id="last_name" name="last_name" placeholder="Last Name" required>
                        </div>    
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                        <label class="fw-bold mb-2" for="email_address">Email Address:<span class="text-danger">*</span></label>
                            <input type="email" class="form-control border-2" id="email_address" name="email_address" placeholder="your@email.com" required>
                        </div>                      
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="password">Password:<span class="text-danger">*</span></label>
                            <input type="password" class="form-control border-2" id="password" name="password" placeholder="" required>
                        </div>                       
                        <div class="col">
                            <label class="fw-bold mb-2" for="password2">Repeat Password:<span class="text-danger">*</span></label>
                            <input type="password" class="form-control border-2" id="password_confirm" name="password_confirm" placeholder="" required>
                       </div>                       
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="phone_number">Phone Number:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" id="phone_number" name="phone_number" placeholder="" required>
                        </div>                        
                        <div class="col">
                            <label class="fw-bold mb-2" for="mobile_number">Mobile Number:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" id="mobile_number" name="mobile_number" placeholder="" required>
                       </div>
                       
                    </div>
                    <div class="row mb-4"> 
                        <div class="col">
                            <label class="fw-bold mb-2" for="account_type">Account Type:<span class="text-danger">*</span></label>
                            <select class="form-select border-2" aria-label="" id="account_type" name="account_type" required>
                                <option value="" selected>Select account type</option>
                                <option value="client">Client</option>
                                <option value="company">Company</option>
                            </select>
                       </div>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary rounded-0" type="submit">Register</button>
                    <p class="text-white mt-5 mb-3 text-center">Already have an account? <a class="text-white fw-bold" href="?page=login'.$ref_url.'">Login</a></p>
                    </form>
                </div>
            </div>            
        </div>

        ';

        return $register_form;
    }

    function login_form(){
        $has_error = $this->has_error;
        $ref_url = isset($_GET['ref']) ? SITE_URL.$_SERVER['REQUEST_URI'].'&action=account_login' : SITE_URL.'/?page=login&action=account_login';
        $login_form = '
        
        <div class="container position-absolute top-50 start-50 translate-middle" style="z-index: 1;">
            <div class="row">
            <div class="col-5 mx-auto">
                <div class="text-center mb-4">'.SITE_LOGO.'</div>    
                <div class="card border-0  rounded-0 shadow-lg bg-white">
                <article class="card-body mx-5">
                    <h1 class="card-title mt-3 text-center">Login</h1>';
                    if($has_error == true) {
                        $login_form .='<p class="text-center text-danger">Username or password is incorrect</p>';
                    }
                    if(isset($_GET['ref']) && $_GET['ref'] == 'password_updated') {
                        $login_form .='<p class="text-center text-success">Password updated. Please login again.</p>';
                    }
                    $login_form .= '                    
                    <form id="login_form" method="post" action="'.$ref_url.'">
                    <div class="mb-3">
                    <label class="fw-bold mb-2" for="email_address">Email Address:<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email_address" name="email_address" placeholder="your@email.com" required>
                    </div>
                    <div class="mb-3">
                    <label class="fw-bold mb-2" for="password">Password:<span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="" required>
                    </div>                  
                    <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Login</button>
                    <p class="mt-5 mb-3 text-body-secondary text-center"><a href="?page=login&action=reset_password">Forgot Password</a></p>
                    </form>
                </article>
                </div>
            </div>
            </div>
        </div>
        <div class="bg-overlay"></div>
        ';

        return $login_form;

    }

    function forgot_form($has_error, $success){        
        $ref_url = SITE_URL.'/?page=login&action=reset_password';
        $email = isset($_POST['email_address']) ? $_POST['email_address'] : null;
        $login_form = '
        
        <div class="container position-absolute top-50 start-50 translate-middle" style="z-index: 1;">
            <div class="row">
            <div class="col-5 mx-auto">
                <div class="text-center mb-4">'.SITE_LOGO.'</div>    
                <div class="card border-0  rounded-0 shadow-lg bg-white pb-4">
                <article class="card-body mx-5">
                    <h1 class="card-title mt-3 mb-3 text-center">Forgot Password</h1>';
                    var_dump($success);
                    if($has_error == true) {
                        $login_form .='<p class="text-center text-danger">Email address not found.</p>';
                    } 
                    
                    if($success == true) {
                        $login_form .='<p class="text-center text-success">Your temporary password has been sent to your email.</p>';
                    } else if($success != false && $success != null) {
                        $login_form .='<p class="text-center text-success">Something went wrong. Please try again.</p>'; 
                    }
                    $login_form .= '                    
                    <form id="reset_form" method="post" action="'.$ref_url.'">
                    <div class="mb-3 mt-3">
                    <label class="fw-bold mb-2" for="email_address">Email Address:<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email_address" name="email_address" value="'.$email.'" placeholder="your@email.com" required>
                    </div>                 
                    <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Reset Password</button>
                    </form>
                </article>
                </div>
            </div>
            </div>
        </div>
        <div class="bg-overlay"></div>
        ';

        return $login_form;

    }

    function post_form(){
        global $connect_db;
        $listing_id = $this->id;
        $mode = $this->mode;
        // Require category arrays
        require(ROOT_PATH.'data/categories.php');        

        $title = null;
        $contenttxt = null;
        $rentprice = null;
        $eventcat = null;
        $eventtype = null;
        $venuecat = null;
        $venuetype = null;
        $setphoto = null;
        $discount = 0;
        if($listing_id) {

            // Get post data from database
            $get_posts = $connect_db->prepare("SELECT ID, user, title, content, post_date, modified_date, rent_price, event_cat, event_type, venue_cat, venue_type, set_photo, discount FROM posts WHERE ID = ?");
            $get_posts->bind_param('i', $listing_id);
            $get_posts->execute();
            $get_posts->store_result();
            $get_posts->bind_result($postid, $userid, $title, $contenttxt, $postdate, $modifieddate, $rentprice, $eventcat, $eventtype, $venuecat, $venuetype, $setphoto, $discount);
            $get_posts->fetch();

        }        

        $title_val = $title ? 'value="'.$title.'"' : '';
        $content_val = $contenttxt ? $contenttxt : '';
        $price_val = $rentprice ? 'value="'.$rentprice.'"' : '';
        $eventcat_arr = $eventcat ? explode(",",$eventcat) : null;
        $eventtype_arr = $eventtype ? explode(",",$eventtype) : null;
        $venuecat_arr = $venuecat ? explode(",",$venuecat) : null;
        $venuetype_arr = $venuetype ? explode(",",$venuetype) : null;

        $action_url = ($mode == 'post') ? SITE_URL.'/?page=account_dashboard&action=post_listing' : SITE_URL.'/?page=account_dashboard&action=edit_listing&id='.$listing_id;
        $post_form = '
        <div class="row">
            <form id="post_form" method="post" action="'.$action_url.'" enctype ="multipart/form-data">
            <div class="mb-3">
            <label class="fw-bold mb-2" for="set_name">Set Name:<span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="set_name" name="set_name" '.$title_val.' placeholder="What is the name of your set?" required>
            </div>
            <div class="mb-3">
            <label class="fw-bold mb-2" for="set_desc">Set Description:<span class="text-danger">*</span></label>
            <textarea class="form-control" id="set_desc" name="set_desc" rows="6" required>'.$content_val.'</textarea>
            </div>
            <div class="mb-3 row align-items-center">
            <div class="col-auto"><label class="fw-bold mb-2" for="rent_price">Rent Price:<span class="text-danger">*</span></label></div>
            <div class="col"><input type="number" class="form-control w-auto" id="rent_price" name="rent_price" '.$price_val.' placeholder="" required></div>
            </div>
            <div class="mb-3">';

            $d_0_sel  = $discount == 0 ? 'selected' : '';
            $d_10_sel  = $discount == 10 ? 'selected' : '';
            $d_20_sel  = $discount == 20 ? 'selected' : '';
            $d_30_sel  = $discount == 30 ? 'selected' : '';
            $d_40_sel  = $discount == 40 ? 'selected' : '';
            $d_50_sel  = $discount == 50 ? 'selected' : '';

            $post_form .='
            <label class="fw-bold mb-2" for="discount_settings">Discount Option:<span class="text-danger">*</span></label>
            <select class="form-select border-0" id="discount_settings" name="discount_settings" required>
                <option></option>
                <option value="0" '.$d_0_sel.'>Turn off discount</option>
                <option value="10" '.$d_10_sel.'>10% off</option>
                <option value="20" '.$d_20_sel.'>20% off</option>
                <option value="30" '.$d_30_sel.'>30% off</option>
                <option value="40" '.$d_40_sel.'>40% off</option>
                <option value="50" '.$d_50_sel.'>50% off</option>
                </select>
            </div>
            <div class="mb-3">
            <label class="fw-bold mb-2" for="event_category">Event Type:<span class="text-danger">*</span></label>
            <select class="form-select border-0" id="event_category" name="event_category[]" required multiple>
            <option></option>';
            $val_counter1 = 0;
            foreach($event_cat as $event_category) {
                $is_selected = $eventcat_arr ? (in_array($event_category, $eventcat_arr) ? 'selected' : '') : '';
                $post_form .= '<option value="'.$val_counter1.'" '.$is_selected.'>'.$event_category.'</option>';                    
                $val_counter1++;
            }
            $post_form.= '
            </select>            
            <div class="row align-items-center p-3">';
            $val_counter2 = 0;
            foreach($event_types as $event_type) {
                $is_selected = $eventtype_arr ? (in_array($event_type, $eventtype_arr) ? 'checked' : '') : '';
                $post_form .= '<div class="form-check col-auto">
                <input class="form-check-input mt-2" type="checkbox" value="'.$val_counter2.'" name="event_type_'.$event_type.'" id="event_type_'.$event_type.'" '.$is_selected.'>
                <span class="form-check-label">
                    '.$event_type.'
                </span>                
                </div>';                    
                $val_counter2++;
            }
            $post_form.= '
            <label class="form-check-label"  for="event_type"></label>          
            </div>
            </div>
            <div class="mb-3">
            <label class="fw-bold mb-2" for="venue_category">Venue Type:<span class="text-danger">*</span></label>
            <select class="form-select border-0" id="venue_category" name="venue_category[]" required multiple>
            <option></option>';
            $val_counter3 = 0;
            foreach($venue_cat as $venue_cat) {
                $is_selected = $venuecat_arr ? (in_array($venue_cat, $venuecat_arr) ? 'selected' : '') : '';
                $post_form .= '<option value="'.$val_counter3.'" '.$is_selected.'>'.$venue_cat.'</option>';                    
                $val_counter3++;
            }
            $post_form.= '
            </select>            
            <div class="row align-items-center p-3">';
            $val_counter4 = 0;
            foreach($venue_type as $venue_type) {
                $is_selected = $venuetype_arr ? (in_array($venue_type, $venuetype_arr) ? 'checked' : '') : '';
                $post_form .= '<div class="form-check col-auto">
                <input class="form-check-input mt-2" type="checkbox" value="'.$val_counter4.'" name="venue_type_'.$venue_type.'" id="venue_type_'.$venue_type.'" '.$is_selected.'>
                <span class="form-check-label">
                    '.$venue_type.'
                </span>

                </div>';                    
                $val_counter4++;
            }
            $post_form.= '
            <label class="form-check-label" for="venue_type"></label>       
            </div>
            </div>
            <div class="mb-3">
            <label for="set_photo" class="form-label fw-bold mb-2 set_photo">Photo of your set:</label>';

            if($setphoto){
                $post_form .='<p><img class="img-thumbnail" src="'.SITE_URL.'/uploads/'.$setphoto.'" style="width: 200px;"></p>';                
            }
            $is_required = $mode == 'update' ? '' : 'required';
            $button_text = $mode == 'update' ? 'Update Listing' : 'Submit Listing';
            $post_form .='
            <input class="form-control" type="file" id="set_photo" name="set_photo" '.$is_required.'>
            </div>
            <button class="w-100 btn btn-lg btn-dark mt-5 rounded-0" type="submit">'.$button_text.'</button>
            </form>
        </div>
        ';

        return $post_form;
    }

    function upload_photo_form(){
        $photo_form = '
        <div class="row">
            <form id="post_form" method="post" action="?page=account_dashboard&action=edit_profile&update=photo" enctype ="multipart/form-data">
            <div class="mb-1">
            <label class="fw-bold mb-2" for="set_name">Profile Photo:</label>
            <input class="form-control" type="file" id="profile_photo" name="profile_photo">
            </div>
            </div>
            <button class="w-100 btn btn-lg btn-dark mt-5 rounded-0" type="submit">Upload Photo</button>
            </form>
        </div>
        ';

        return $photo_form;
    }

    function profile_form() {
        global $connect_db;
        $user_id = $this->id;
        $email = null;
        $company_name  = null;
        $company_address = null;
        $first_name = null;
        $last_name = null;
        $phone_number = null;
        $mobile_number = null;

        // Get user data from database
        $check_user = $connect_db->prepare("SELECT email, company_name, company_address, first_name, last_name, phone_number, mobile_number FROM users WHERE ID = ?");
        $check_user -> bind_param('i', $user_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($email, $company_name, $company_address, $first_name, $last_name, $phone_number, $mobile_number);
        $check_user->fetch();

        $profile_form =  '

        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <form id="profile_form" method="post" action="?page=account_dashboard&action=edit_profile">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="company_name">Company Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" value="'.$company_name.'" id="company_name" name="company_name" placeholder="Enter your company name" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                        <label class="fw-bold mb-2" for="company_address">Company Address:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control border-2" value="'.$company_address.'" id="company_address" name="company_address" placeholder="Enter your company address" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="first_name">First Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" value="'.$first_name.'" id="first_name" name="first_name" placeholder="First Name" required>
                        </div>
                        <div class="col">
                            <label class="fw-bold mb-2" for="last_name">Last Name:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" value="'.$last_name.'" id="last_name" name="last_name" placeholder="Last Name" required>
                        </div>    
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                        <label class="fw-bold mb-2" for="email_address">Email Address:<span class="text-danger">*</span></label>
                            <input type="email" class="form-control border-2" value="'.$email.'" id="email_address" name="email_address" placeholder="your@email.com" required>
                        </div>                      
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="fw-bold mb-2" for="phone_number">Phone Number:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" value="'.$phone_number.'" id="phone_number" name="phone_number" placeholder="" required>
                        </div>                        
                        <div class="col">
                            <label class="fw-bold mb-2" for="mobile_number">Mobile Number:<span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-2" value="'.$mobile_number.'" id="mobile_number" name="mobile_number" placeholder="" required>
                       </div>
                       
                    </div>
                    <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Update Profile</button>
                    </form>
                </div>
            </div>            
        </div>

        ';

        return $profile_form;
    }

    function password_form() {
        //$user_id = $this->id;

        $password_form =  '

        <form id="password_form" method="post" action="?page=account_dashboard&action=update_password">
            <div class="row mb-3">
                <div class="col">
                    <label class="fw-bold mb-2" for="password">New Password:<span class="text-danger">*</span></label>
                    <input type="password" class="form-control border-2" id="password" name="password" placeholder="" required>
                </div>                       
                <div class="col">
                    <label class="fw-bold mb-2" for="password2">Repeat New Password:<span class="text-danger">*</span></label>
                    <input type="password" class="form-control border-2" id="password_confirm" name="password_confirm" placeholder="" required>
                </div>                       
            </div>
            <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Change Password</button>
        </form>

        ';

        return $password_form;
    }

    function discount_form() {
        global $connect_db;
        $discount_id = $this->id;
        $discount = '';

        // Get discount from database
        $check_discount = $connect_db->prepare("SELECT title FROM discounts WHERE ID = ?");
        $check_discount -> bind_param('i', $discount_id);
        $check_discount->execute();
        $check_discount-> store_result();
        $check_discount-> bind_result($discount);
        $check_discount->fetch();

        $discount_form =  '

        <form id="discount_form" method="post" action="?page=account_dashboard&view=discount_settings">
            <div class="row mb-3">
                <div class="col">
                <select class="form-select border-0" id="discount_settings" name="discount_settings" required>
                <option></option>
                <option value="0">Turn off discount</option>
                <option value="10">10% off</option>
                <option value="20">20% off</option>
                <option value="30">30% off</option>
                <option value="40">40% off</option>
                <option value="50">50% off</option>
                </select>
                </div>                     
            </div>
            <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Set Discount Option</button>
        </form>

        ';

        return $discount_form;
    }

    function message_form(){
        global $connect_db, $widget;
        $recipient_id = $this->id;
        $listing_id = $this->listing_id;

        // Get post data from database
        $get_posts = $connect_db->prepare("SELECT title, content, modified_date, rent_price, set_photo, discount FROM posts WHERE ID = ?");
        $get_posts->bind_param('i', $listing_id);
        $get_posts->execute();
        $get_posts->store_result();
        $get_posts->bind_result($title, $contenttxt, $modifieddate, $rentprice, $setphoto, $discount);
        $get_posts->fetch();

        //Get poster's contact details from database
        $get_author = $connect_db->prepare("SELECT company_name, follower_discount FROM users WHERE ID = ?");
        $get_author->bind_param('i', $recipient_id);
        $get_author->execute();
        $get_author->store_result();
        $get_author->bind_result( $author_company, $global_discount);
        $get_author->fetch();

        // Get post thumbnail
        $bg_setphoto = SITE_URL.'/uploads/'.$setphoto;

        // Format money
        $money_formatter = new NumberFormatter('en_GB', NumberFormatter::DECIMAL);
        $money_formatter->setAttribute(NumberFormatter::MIN_FRACTION_DIGITS, 2);

        // Set profiile id and listing id
        $widget->set_profile($recipient_id);
        $widget->set_listing($listing_id);

        // Discount if aplicable
        $applicable_discount = $discount > 0 ? $discount : $global_discount;     
        $discount_amount =  $rentprice * ($applicable_discount / 100);
        $discount_price = $rentprice - $discount_amount;

        $subject_value = (isset($listing_id) && isset($title)) ? $title : (isset($_POST['subject']) ? $_POST['subject'] : '');
        $message_body = isset($_POST['message']) ? $_POST['message'] : '';
        $message_form = '';

        $message_form =  '

        <form id="discount_form" method="post" action="?page=account_dashboard&action=create_message&recipient_id='.$recipient_id.'&listing_id='.$listing_id.'">
            <div class="row mb-3">
                <div class="col">
                    <label class="fw-bold mb-2" for="subject">Subject: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control border-2" id="subject" name="subject" value="'.$subject_value.'" placeholder="Enter your subject" required>
                </div>                     
            </div>
            <div class="row mb-3">
            <div class=" post post-'.$listing_id.' card p-0 shadow mb-4">
            <div class="card-body p-0">
                <div class="row m-0">';
                    $message_form .= '<div class="col-3 bg-image rounded-start" style="background-image:url('.$bg_setphoto.');">';                                                                                                                 
                    $message_form .= '
                    </div>
                    <div class="col p-3">                                                      
                        <div class="post-content position-relative" style="height: 170px; overflow: hidden;">
                            <h3 class="card-title h4 mb-1 d-flex align-items-center"><span class="listing_title">'.$title.' </span></h3>
                            '.$contenttxt.'
                        </div>
                    </div>
                    <div class="col-4 py-3 pe-4">
                        <div class="alert alert-success py-1 px-2">
                            <div class="row gx-1 d-flex align-items-center mb-2 bg-white p-1 rounded">
                                <div class="col-auto">
                                    <div class="round-circle" style="width: 20px;">'
                                    .$widget->get_avatar().
                                    '</div>
                                </div>
                                <div class="col">
                                    <a class="text-dark fw-bold" href="?page=view_profile&id='.$recipient_id.'">'.$author_company.'</a>
                                </div>
                            </div>';
                            $is_following = $widget->is_following();                                          
                            $message_form .='
                            <div class="row">
                                <div class="col fw-bold price-text text-center">';
                                if($global_discount > 0 && $is_following == true ) {
                                    $message_form .='<p class="mb-0"><span class="text-decoration-line-through">₱'.$money_formatter->format($rentprice).'</span>
                                    <span class="text-danger fw-bold">₱'.$money_formatter->format($discount_price).'</span></p>';
                                } else {
                                    $message_form .='<p class="mb-0">₱'.$money_formatter->format($rentprice).'</p>';
                                }                                                
                                $message_form .='
                                </div>
                            </div>
                            <div class="row">
                            <div class="col">
                            <div class="d-grid gap-2">
                                <a class="btn btn-success btn-sm" href="?page=search&view_listing='.$listing_id.'" role="button">View Details</a>
                            </div>                                    
                            </div>
                        </div>
                        </div>
                        <div class="alert alert-light border py-1 px-2">
                            <div class="post-meta">
                                <div class="meta-date">';
                                $message_form .= '
                                    Posted on '.$modifieddate.'
                                </div>
                            </div>
                        </div> 
                    </div>                          
                </div>                   
            </div>
        </div>
            </div>                
            <div class="row mb-3">
                <div class="col">
                    <label class="fw-bold mb-2" for="message">Message: <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="message" name="message" rows="6" required="required">'.$message_body.'</textarea>
                </div>                     
            </div>
            <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Send Message</button>
        </form>

        ';
        return $message_form;
    }

    function reply_form(){
        $thread_id = $this->id;
        $message_body = isset($_POST['reply_message']) ? $_POST['reply_message'] : '';

        $reply_form =  '

        <form id="discount_form" method="post" action="?page=account_dashboard&view=messages&thread_id='.$thread_id.'">
            <div class="row mb-3">
                <div class="col">
                    <textarea class="form-control" id="reply_message" name="reply_message" rows="6" required="required">'.$message_body.'</textarea>
                </div>                     
            </div>
            <button class="w-100 btn btn-lg btn-dark rounded-0" type="submit">Reply</button>
        </form>

        ';
        return $reply_form;
    }
}