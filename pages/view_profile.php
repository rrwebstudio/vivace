<?php

class ViewProfile {

    public $user_id;

    function set_user($user_id) {
        $this->user_id = $user_id;
    }

    function get_profile() {

        $user_id = $this->user_id;

        // Connect to mysql DB
        $connect_db = connect_db();
        // Check connection
        if ($connect_db->connect_error) {
            die("Connection failed: " . $connect_db->connect_error);
        }

        // Prepare sql statement to get profile data
        $check_user = $connect_db->prepare("SELECT email, company_name, company_address, first_name, last_name, phone_number, mobile_number, user_type FROM users WHERE ID = ?");
        $check_user -> bind_param('s', $user_id);
        $check_user->execute();
        $check_user -> store_result();
        $check_user -> bind_result($email, $company_name, $company_address, $first_name, $last_name, $phone_number, $mobile_number, $user_type);

        // statement result
        if($check_user->num_rows == 1) {                                               
            // Get result
          if($check_user->fetch()) {
                $widget = new Widget();
                $widget->set_user($user_id);                
                $profile = '
                <div class="row p-0 m-0 position-relative bg-image" style="background-image:url('.SITE_URL.'/assets/images/bg_search.jpg); height: 100px; background-position: 50% 50%;">
                    <div class="col-auto mx-auto position-absolute top-100 start-50 translate-middle">
                    '.$widget->get_avatar().'
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <p class="display-6 text-center mt-5 pt-5">
                        '.$company_name.'
                        </p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-auto mx-auto">
                        <a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="#" role="button">Follow</a>
                        <a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="#" role="button">Message</a>';
                        if($user_id == $_SESSION['user_id']) {
                            $profile .='<a class="btn btn-outline-dark rounded-0 btn bi mx-1 border-3 border-light fw-bold" href="?page=account_dashboard&action=edit_profile" role="button">Edit Profile</a>';
                        }
                    $profile .='
                    </div>
                </div>

                <div class="container py-5">
                    <div class="row">
                        <div class="col">
                        '.$widget->get_listings().'
                        </div>
                        <div class="col-3">
                        '.$widget->profile_user_data().'
                        </div>
                    </div>
                </div>
                ';
          }

        }

        return $profile;

    }
}