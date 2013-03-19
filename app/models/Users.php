<?php
class ModelUsers extends Models
{
    private static $__indexes = Array(
      Array( Array( 'username' => 1 ) ),
      Array( Array( 'email'    => 1 ) ),
    );

    function __construct() {
      self::$__collectionName = 'Users';
    }

    public static function newUser()
    {
        ModelUsers::connect();
        
        // Preparing variables for insert
        $_POST['password'] = self::passwd($_POST['password']);
        $_POST['email'] = self::str_email($_POST['email']);
        $_POST['name']  = trim($_POST['name']);
        
        
        // Verify if user exists
        $_user = self::findOne(Array(
                        'email' => $_POST['email']
        ));        
        if (!is_array($_user)) {
            
            
            $_data = $_POST;
            unset($_data['re_password']);
            $_data['created']   = new MongoDate(time());
            $_data['updated']   = new MongoDate(time());
            $_data['active']    = true;
            $_data['confirmed'] = true;
            $_data['roles']     = Array(
                            'is_admin' => false,
                            'is_customer' => true,
                            'is_manager'  => false,
                            'is_developer' => false,
                            'create_task' => true,
                            'create_project' => false,
                            'create_ticket'  => true
            );
            $_data['locale'] = 'UTC';
            return self::save($_data);
        } else {
            return false;
        }
    }


    public static function login()
    {
        ModelUsers::connect();
        $_data = $_POST['form'];
        $_data['password'] = self::passwd($_data['password']);

        $_find = Array(
            '$or' => Array(
                Array(
                    'email' => $_data['username'],
                    'password' => $_data['password']
                ),
                Array(
                    'username' => $_data['username'],
                    'password' => $_data['password']
                ),
            )
        );
        $_user = ModelUsers::findOne($_find);

        if (is_array($_user)) {
            $_SESSION['online']       = true;
            $_SESSION['online_since'] = time();
            $_SESSION['uid']          = $_user['_id']->__toString();

            $_user['stats']['last_login']    = new MongoDate(time());
            $_user['stats']['last_activity'] = new MongoDate(time());
            $_user['stats']['logins']++;
            $_user['stats']['karma'] = $_user['stats']['karma']+0.001;
            ModelUsers::save($_user);

            self::redirect(self::location('wallpapers/my', true));
        } else {
            self::redirect(self::location('users/login', true));
        }
    }

    public static function logout()
    {
        unset($_SESSION);
        session_destroy();
        self::redirect(self::location(false, true));        
    }

}