<?php
    class signUpController{
        private $fullname;
        private $username;
        private $email;
        private $password;
        private $cpassword;
        private $errors=[];


        public function __construct($fullname, $username, $email, $password, $cpassword){
            $this->fullname = trim($fullname);
            $this->username = trim($username);
            $this->email = trim($email);
            $this->password = $password;
            $this->cpassword = $cpassword;
        }
        // Getters
        public function getFullname(){
            return $this->fullname;
        }
        public function getUsername(){
            return $this->username;
        }
        public function getEmail(){
            return $this->email;
        }
        public function getErrors(){
            return $this->errors;
        }
        //validation methods
        public function validateInputs(){
            $this->validateFullname();
            $this->validateUsername();
            $this->validateEmail();
            $this->validatePassword();
            return empty($this->errors);
        }
        private function validateFullname(){
            if(empty($this->fullname)){
                $this->errors['fullname'] = "Full name is required.";
            } elseif(strlen( $this->fullname)<2){
                $this->errors['fullname'] = "Full name must contain at least 2 characters.";
            }
        }
        private function validateUsername(){
            if(empty($this->username)){
                $this->errors['username'] = "Username is required.";
            } elseif(strlen( $this->username)<3){
                $this->errors['username'] = "Username can only contain at least 3 characters.";
            } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', $this->username)){
                $this->errors['username'] = "Username can only contain letters, numbers, and underscores.";
            }
        }
        private function validateEmail(){
            if(empty($this->email)){
                $this->errors['email'] = "Email is required.";
            } elseif(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
                $this->errors['email'] = "Invalid email format.";
            }
        }
        private function validatePassword(){
            if(empty($this->password)){
                $this->errors['password'] = "Password is required.";
            } elseif(strlen($this->password)<8){
                $this->errors['password'] = "Password must be at least 8 characters long.";
            } elseif($this->password !== $this->cpassword){
                $this->errors['cpassword'] = "Passwords do not match.";
            }
        }

    }