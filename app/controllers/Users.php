<?php

class ControllerUsers extends Controllers
{

		public function indexAction()
		{
				
		}
		
		public static function signin()
		{
		    self::seoTitle('Usuários');
		    self::seoTitle('Entrar');
		    
		    Layout::mainHeader();
		    
		    self::render(
            'Users:signin',
            Array(
                'url'        => self::location(false, true, false)
            )
		    );
		    
		    Layout::mainFooter();
		}
		
		public static function newUser()
		{
		    $_error = false;
		    if (!empty($_POST) && count($_POST) === 4) {
		        if (empty($_POST['name']) || empty($_POST['email']) ) {
		            $_error = true;
		        }
		        
		        if (
		            empty($_POST['password']) || 
		            $_POST['password'] == '' ||
                empty($_POST['re_password']) ||
                $_POST['re_password'] == ''
		        ) {
		            
		        }
		        
		        if ($_POST['password'] != $_POST['re_password']) {
		            $_error = true;
		        }
		        
		        if ($_error == true) {
		            self::redirect(self::location('users/signin', true));
		        } else {
	            
		            $_user = ModelUsers::newUser();
		            if (is_array($_user)) {
		                ModelUsers::login($_user['email'], $_user['password']);
		                self::redirect(self::location('home', true));
		            } else {
		                self::redirect(self::location('users/signin/?error=true', true));
		            }		            
		        }
		    }
		}
		
		public static function doLogin()
		{
		    ModelUsers::login($_POST['username'], self::passwd($_POST['password']));
		    self::redirect(self::location('home', true));
		}
		
		public static function doLogout()
		{
		    ModelUsers::logout();
		    self::redirect(self::location(false, true));
		}
}