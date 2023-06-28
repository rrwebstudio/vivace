<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class LoginPage {  

  // Get Login Page and subpages
  function get_page() {
    global $connect_db;
    $form = new Form();

    // Login page - Forgot Password form
    if( isset($_GET['action']) && $_GET['action'] == 'reset_password' ) {
        if(empty($_SESSION['user_id'])) {
          $has_error = false;
          $success = null;
          // Process login only in $_POST method
          if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $submitted_email = isset($_POST['email_address']) ? $_POST['email_address'] : null;          
            $email = null;
            $company_name = null;
            $user_id = null;
            $check_user = $connect_db->prepare("SELECT ID, email, company_name FROM users WHERE email = ?");
            $check_user->bind_param('s', $submitted_email);
            $check_user->execute();
            $check_user->store_result();
            $check_user->bind_result($user_id, $email, $company_name);
            $check_user->fetch();
            $has_error = $check_user->num_rows == 1 ? false : true;          

            if($check_user->num_rows == 1){
              // Email checks out, sending random password to email
              $password = randomPassword();
              $hashed_password = isset($submitted_email) ? password_hash($password, PASSWORD_DEFAULT) : null; // we don't store password in db, only hashed ones

              // Update password in MySQL
              $update_password = "UPDATE users SET                                
              pass = '$hashed_password'
              WHERE id='$user_id'"; 

              // If successful, show confirmation text then send email
              if ($connect_db->query($update_password) === TRUE) {
                  $success == true;
                  // Send Email
                  $message = "Hello there, <br>";
                  $message .= "Your account is as follows: <br>";
                  $message .= "<br>";
                  $message .= "Company: $company_name <br>";
                  $message .= "Email: $email <br>";
                  $message .= "New Password: $password <br>";
                  $message .= "<br>";
                  $message .= "<br>";
                  $message .= "Please change this random password after you login.";
                  send_mail(
                      $email,
                      'Your new password', 
                      $message
                  );
              } else {
                $success == false;
              }            
            }
          }
          // User not logged in
          // Show Forgot password form
          return $form->forgot_form($has_error, $success);
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
                        // Then rdirect to Account Dashboard
                        $ref_url = isset($_GET['ref']) ? $_GET['ref'] : '';
                        if(empty($ref_url)){
                          redirect('?page=account_dashboard');
                        } else {
                          redirect($ref_url);
                        }                                                          
                      } else {
                        // Password incorrect
                        // show error in login form
                        $has_error = true;
                        $form->has_error($has_error);
                        return $form->login_form();
                      }
                  }                   
                } else {
                    // No result - either email or password is incorrect
                    // Show error in login form
                    $has_error = true;
                    $form->has_error($has_error);
                    return $form->login_form();
                }

            }

        } else {
            if(empty($_SESSION['user_id'])) {
              $has_error = false;
              $form->has_error($has_error);
              return $form->login_form();
            } else {
                redirect('?page=account_dashboard');
            }                   
        }
                        
    }
  }
}