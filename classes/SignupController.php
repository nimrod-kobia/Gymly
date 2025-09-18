<?php
    class signUpController{
        private $fullname;
        private $username;
        private $email;
        private $password;
        private $cpassword;


        public function __construct($fullname, $username, $email, $password, $cpassword){
            $this->fullname = $fullname;
            $this->username = $username;
            $this->email = $email;
            $this->password = $password;
            $this->cpassword = $cpassword;
        }

    }