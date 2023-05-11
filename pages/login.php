<?php

class LoginPage {

  public $action;

  function set_action($action) {
    $this->action = $action;
  }

  // Get Login Page and subpages
  function get_page() {
    $form = new Form();

    // Login page - Forgot Password form
    if( isset($_GET['action']) && $_GET['action'] == 'forgot' ) {
        if(empty($_SESSION)) {
            // User not logged in
            // Show Forgot password form
            //return $form->login_form(false);
        } else {
            // User is already logged in
            // No need to visit this page
            // redirect to Account Dashboard
            redirect('?page=account_dashboard');
        }  
    }
    // Login page - login form
    else {
        // ?page=login&action=account_login
        // Loads when user submits the login credentials
        // Verifies user/pass with the database and saves user data in session
        if( isset($_GET['action']) && $_GET['action'] == 'account_login' ) {

            // Process login only in $_POST method
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Get submitted credentials
                $email_address = isset($_POST['email_address']) ? $_POST['email_address'] : null;
                $password = isset($_POST['password']) ? $_POST['password'] : null;      

                // Connect to mysql DB
                $connect_db = connect_db();
                // Check connection
                if ($connect_db->connect_error) {
                    die("Connection failed: " . $connect_db->connect_error);
                }

                // Check if email and password is accepted
                $check_user = $connect_db->prepare("SELECT ID, email, pass, company_name, company_address, first_name, last_name, phone_number, mobile_number, user_type, avatar FROM users WHERE email = ?");
                $check_user -> bind_param('s', $email_address);
                $check_user->execute();
                $check_user -> store_result();
                $check_user -> bind_result($user_id, $email, $hashed_pass, $company_name, $company_address, $first_name, $last_name, $phone_number, $mobile_number, $user_type, $avatar);
                //var_dump($check_user->fetch());

                // statement result
                if($check_user->num_rows == 1) {                                               
                      // Get result
                    if($check_user->fetch()) {
                      // Verify Password
                      if(password_verify($password, $hashed_pass)) {
                        // Password verified
                        // Get user data and save to current session
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['email'] = $email;
                        $_SESSION['company_name'] = $company_name;
                        $_SESSION['company_address'] = $company_address;
                        $_SESSION['first_name'] = $first_name;
                        $_SESSION['last_name'] = $last_name;
                        $_SESSION['phone_number'] = $phone_number;
                        $_SESSION['mobile_number'] = $mobile_number;
                        $_SESSION['account_type'] = $user_type;
                        $_SESSION['avatar'] = $avatar;
                        // Then rdirect to Account Dashboard
                        redirect('?page=account_dashboard');                                   
                      } else {
                        // Password incorrect
                        // show error in login form
                        $has_error = true;
                        return $form->login_form($has_error);
                      }
                  }                   
                } else {
                    // No result - either email or password is incorrect
                    // Show error in login form
                    $has_error = true;
                    return $form->login_form($has_error);
                }

                // Close connection to db
                $connect_db->close();

            }

        } else {
            if(empty($_SESSION)) {
              $has_error = false;
              return $form->login_form($has_error);
            } else {
                redirect('?page=account_dashboard');
            }                   
        }
                        
    }
  }
}