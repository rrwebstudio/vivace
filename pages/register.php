<?php

// Connect to DB
require(ROOT_PATH.'db.php');

class RegisterPage {    

    public $page;

    function get_page(){
        
        global $connect_db;

        // $_GET['action'] == 'register_account'
        // This is when registered form is submitted
        // Registration form is somewhere below
        if( isset($_GET['action']) && $_GET['action'] == 'register_account') {

            //var_dump(connect_db());
            // Register submitted form data to database
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Save registration data to variables
                $email_address = isset($_POST['email_address']) ? $_POST['email_address']  : null;
                $password = isset($_POST['password']) ? $_POST['password']  : null;
                $hashed_password = isset($_POST['password']) ? password_hash($password, PASSWORD_DEFAULT) : null; // we don't store password in db, only hashed ones
                $company_name = isset($_POST['company_name']) ? $_POST['company_name']  : null;
                $company_address = isset($_POST['company_address']) ? $_POST['company_address']  : null;
                $first_name = isset($_POST['first_name']) ? $_POST['first_name']  : null;
                $last_name = isset($_POST['last_name']) ? $_POST['last_name']  : null;
                $phone_number = isset($_POST['phone_number']) ? $_POST['phone_number']  : null;
                $mobile_number = isset($_POST['mobile_number']) ? $_POST['mobile_number']  : null;
                $account_type = isset($_POST['account_type']) ? $_POST['account_type']  : null;
                $register_date = new DateTime();
                $register_date =  $register_date->format('Y-m-d H:i:s');
            
                // Check if email is already taken
                $check_emails = $connect_db->prepare("SELECT email FROM users");
                $check_emails->execute();
                $user_emails = [];
                foreach ($check_emails->get_result() as $row){
                    $user_emails[] = $row['email'];
                }
            
                if(in_array($email_address, $user_emails)){
                    // Show validation error - email is already taken
                    $html = '
            
                    <div class="container position-absolute top-50 start-50 translate-middle text-white">
                    <div class="row">
                        <div class="col-7 py-4 px-5 shadow-lg text-center" style="background-color: rgba(1, 1, 39, 0.8);">
                            '.SITE_LOGO.'
                            <p class="text-center txt-danger">Error: email is already taken</p>
                            <a class="btn btn-black rounded-0" href="?page=register" role="button">Go back</a>
                            </div>
                        </div>
                        </div>
                    </div>
            
                    ';
                } 
                else {
                    // Email is valid, proceed to registration
                    // INSERT user data to mySQL
                    $user_data = "INSERT INTO users (
                        email,
                        pass,
                        company_name,
                        company_address,
                        first_name,
                        last_name,
                        phone_number,
                        mobile_number,
                        website_url,
                        registered_date,
                        user_type,
                        avatar,
                        follower_discount
                    )
                    VALUES (
                        '$email_address',
                        '$hashed_password',
                        '$company_name',
                        '$company_address',
                        '$first_name',
                        '$last_name',
                        '$phone_number',
                        '$mobile_number',
                        '',
                        '$register_date',
                        '$account_type',
                        '',
                        0
                    )";
                        
                    // If successful, show confirmation text and send email
                    if ($connect_db->query($user_data) === TRUE) {
        
                    // Send Email
                    $message = "Hi, $first_name, <br><br>";
                    $message .= "Welcome to Vivace, here is your info:<br><br>";
                    $message .= "Email: $email_address<br>";
                    $message .= "Account type: $account_type<br>";
                    $message .= "Company: $company_name<br><br>";
                    $message .= "You may login anytime, just visit the link:<br>";
                    $message .= SITE_URL."/?page=login";
                    send_mail(
                        $email_address,
                        'Welcome to Vivace, '.$first_name, 
                        $message
                    );
        
                    // Display confirmation text
                    $ref_url = isset($_GET['ref']) ? '&ref='.$_GET['ref'] : '';
                    $html = '
        
                    <div class="container position-absolute top-50 start-50 translate-middle text-white">
                    <div class="row">
                        <div class="col-7 py-4 px-5 shadow-lg text-center" style="background-color: rgba(1, 1, 39, 0.8);">
                            '.SITE_LOGO.'
                            <p class="h4 text-center">Account created successfully</p>
                            <p class="text-center">An email has been sent to your inbox.</p>
                            <p class="text-center"><a class="bbtn btn-black rounded-0" href="?page=login'.$ref_url.'" role="button">Login</a></p>
                            </div>
                        </div>
                        </div>
                    </div>
        
                    ';
                    }
                    
                    // If not successful, show error
                    else {
                    $html = '
        
                    <div class="container position-absolute top-50 start-50 translate-middle text-white">
                        <div class="row">
                            <div class="col-7 py-4 px-5 shadow-lg text-center" style="background-color: rgba(1, 1, 39, 0.8);">
                            '.SITE_LOGO.'
                            <p class="text-center">'.'Error: ' . $user_data . '<br>' . $connect_db->error.' You may try again.</p>
                            <a class="btn btn-black rounded-0" href="?page=register" role="button">Try again</a>
                            </div>
                        </div>
                        </div>
                    </div>
        
                    ';
                    }
                }

                return $html;
            
            } else {
                $form = new Form();
                return $form->register_form();
                }        
        }
        
        // PAGE - REGISTRATION FORM
        // Upon page load, this is shown
        else {
        
            $form = new Form();
            return $form->register_form();
        
        }
    }
}