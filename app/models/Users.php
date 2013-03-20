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
        $_user = ModelUsers::findOne(Array(
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
            $_data['stats'] = Array(
                            'last_login' => false,
                            'last_activity' => false,
                            'logins' => 0
                            );
            $_data['locale'] = 'UTC';
            ModelUsers::insert($_data);
            return $_data;
        } else {
            return false;
        }
    }


    public static function login($_username = false, $_password = false)
    {
        ModelUsers::connect();

        $_find = Array(
            '$or' => Array(
                Array(
                    'email' => $_username,
                    'password' => $_password
                ),
                Array(
                    'username' => $_username,
                    'password' => $_password
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
            ModelUsers::save($_user);

            return true;
        } else {
            return false;
        }
    }

    public static function logout()
    {
        unset($_SESSION);
        session_destroy();
    }

}