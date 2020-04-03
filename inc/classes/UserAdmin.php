<?php
    class UserAdmin {
        public static function login_user() {
            $user = $_POST["user"];
            $pass = $_POST["pass"];

            try {
                $res = DB::query("SELECT ID FROM `Users` WHERE Username=:user AND `Password`=MD5(:pass);", array(":user" => $user, ":pass" => $pass));
            } catch (PDOException $e) {
                return false;
            }
            return $res[0]["ID"];
        }

        /* Creates an account and returns an array outlining the result of the process
         * Code key:
         * 0 - Success
         * 1 - Duplicate value in database, nature of duplicate in second element of array
         * 2 - SQL error, where the error occured in the process, not a fatal error
         * 3 - Invalid email or username reached the server
         */
        public static function create_account() {
            $email = $_POST["email"];
            $user  = $_POST["user"];
            $vercode = sha1(time());

            // Check if the email's valid
            $pattern = "/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/";
            if (!preg_match($pattern, $email)) {
                return array(3, "email");
            }
            // Check if username's valid
            $pattern = "/^[a-zA-Z0-9]{3,15}$/";
            if (!preg_match($pattern, $user)) {
                return array(3, "username");
            }
            // Check if email is already in use
            if (!DB::query("SELECT Email FROM `Users` WHERE Email=:email;", array(":email" => $email))) {
                if (!DB::query("SELECT Username FROM `Users` WHERE Username=:user", array(":user" => $user))) {
                    try {
                        DB::query("INSERT INTO `Users` (Email, Username, Vercode) VALUES (:email, :user, :ver);", array(":email" => $email, ":user" => $user, ":ver" => $vercode));
                    } catch (PDOException $e) {
                        return array(2, "Server error... Please try again.");
                    }
                } else {
                    return array(1, "Username in use!");
                }
            } else {
                return array(1, "Email already registered!");
            }

            // Send registration email
            $to = $email;
            $headers = <<<MESSAGE
FROM: George || <george@flatdragons.com>
Content-Type: text/plain;
MESSAGE;
            $subject = "Flat Dragons Registration";
            $msg =  <<<EMAIL
Hi $user,

Please follow this link to verify your account:
https://flatdragons.com/signup.php?user=$user&ver=$vercode

Kind regards,
George (FlatDragons)
EMAIL;
            mail($to, $subject, $msg, $headers);
            return array(0, null);
        }

        public static function verify_account() {
            $vercode = $_GET["ver"];
            $user = $_GET["user"];
            if (DB::query("SELECT Vercode FROM `Users` WHERE Vercode=:ver AND Username=:user", array(":ver" => $vercode, ":user" => $user))) {
                try {
                    DB::query("UPDATE `Users` SET Verified=1 WHERE Vercode=:ver AND Username=:user", array(":ver" => $vercode, ":user" => $user));
                } catch (PDOException $e) {
                    return 1;
                }
            } else {
                return 2;
            }
            return 0;
        }

        public static function add_password() {
            $pass = $_POST["pass"];
            $user = $_POST["user"];
            try {
                DB::query("UPDATE `Users` SET `Password`=MD5(:pass) WHERE Username=:user;", array(":pass" => $pass, ":user" => $user));
            } catch (PDOException $e) {
                return 1;
            }
            return 0;
        }

        /* Status codes:
         * 0 - What was requested was done, new link issued.
         * 1 - PDOException thrown, please try again.
         * 2 - Email not found in database, recommend signing up again.
         * 3 - Full account already exists.
         * 4 - Verified account exists but password has failed to be added.
         */
        public static function reissue_verification() {
            $email = $_POST["email"];
            // Fetch whether the email is verified information
            try {
                $request = DB::query("SELECT `Verified` FROM `Users` WHERE `Email`=:email;", array(":email" => $email));
            } catch (PDOException $e) {
                return 1;
            }

            // Check if the email is actually registered
            if ($request) {
                $verified_status = $request[0]["Verified"];
                // Determine whether the email is verified
                if ($verified_status == 1) {
                    // Check if users has a password
                    try {
                        $pass_request = DB::query("SELECT `Password` FROM `Users` WHERE `Email`=:email;", array(":email" => $email));
                    } catch (PDOException $e) {
                        return 1;
                    }
                    if ($pass_request) {
                        return 3;
                    } else {
                        return 4;
                    }
                } else {
                    // Gather required data 
                    try {
                        $username = DB::query("SELECT `Username` FROM `Users` WHERE `Email`=:email;", array(":email" => $email))[0]["Username"];
                    } catch (PDOException $e) {
                        return 1;
                    }
                    $vercode = sha1(time());
                    try {
                        DB::query("UPDATE `Users` SET Vercode=:ver WHERE Email=:email;", array(":ver" => $vercode, ":email" => $email));
                    } catch (PDOException $e) {
                        return 1;
                    }
                    $to = $email;
                    $headers = <<<MESSAGE
FROM: George || george@flatdragons.com
Content-Type: text/plain;
MESSAGE;
                    $subject = "Verification code re-issue";
                    $msg = <<<EMAIL
Hi $username,

You've requested for a new verification code to be issued!
Please follow this <a href='https://flatdragons.com/signup.php?user=$username&ver=$vercode'>link</a> to confirm your account with us :)

Kind regards,
George (FlatDragons)
EMAIL;
                    mail($to, $subject, $msg, $headers);
                    return 0;
                }
            } else {
                return 2;
            }
        }
    }
?>