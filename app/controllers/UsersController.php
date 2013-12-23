<?php

/**
 * Description of UsersController
 *
 * @author chris
 */
class UsersController extends BaseController {
    
    protected $layout = "layouts.main";
    
    
    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post'));
        $this->beforeFilter('auth', array('only'=>array('getDashboard')));
    }
    
    public function getDashboard() {
        $this->layout->content = View::make('users.dashboard');
    }
    
    public function getLogin() {
        $this->layout->content = View::make('users.login');
    }
    
    public function getLogout() {
        Auth::logout();
        return Redirect::to('users/login')->with('message', 'Your are now logged out!');
    }
    
    public function getRegister() {
        $this->layout->content = View::make('users.register');
    }
    
    
    /*
     * Register a new user
     */
    public function postCreate() {
        $validator = Validator::make(Input::all(), User::$rules);
        
        if($validator->passes()) {
            $user = new User;
            $user->firstname = Input::get('firstname');
            $user->lastname = Input::get('lastname');
            $user->email = Input::get('email');
            $user->password = Hash::make(Input::get('password'));
            $user->save(); // LEFT OFF HERE
            
            return Redirect::to('users/login')->with('message', 'You are now registered, thank you!');
            
        } else {
            
            return Redirect::to('users/register')->with('message', 'The following errors occured')->withErrors($validator)->withInput();
            
        }
        
    }
    
    /*
     * Login a user
     */
    public function postSignin() {
        if (Auth::attempt(array('email'=>Input::get('email'), 'password'=>Input::get('password')))) {
            return Redirect::to('users/dashboard')->with('message', 'You are now logged in!');
        } else {
            return Redirect::to('users/login')
                ->with('message', 'Your username/password combination was incorrect')
                ->withInput();
}
    }
}

?>
