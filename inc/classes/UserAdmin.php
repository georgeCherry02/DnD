<?php
    class UserAdmin {
        public static function login_user() {
            $user = $_POST["user"];
            $pass = $_POST["pass"];

            $res = DB::query("SELECT COUNT(UserID) AS duplicate, UserID FROM `Users` WHERE Username=:user AND `Password`=MD5(:pass);", array(":user" => $user, ":pass" => $pass));
            return array($res[0]["duplicate"], $res[0]["UserID"]);
        }
        /* Creates an account and returns an array outlining the result of the process
         * Code key:
         * 0 - Success
         * 1 - Duplicate value in database, nature of duplicate in second element of array
         * 2 - SQL error, where the error occured in the process, not a fatal error
         */
        public static function create_account() {
            $email = $_POST["email"];
            $user  = $_POST["user"];
            $vercode = sha1(time());

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
    }
?>