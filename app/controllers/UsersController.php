<?php

/**
 * Description of UsersController
 *
 * @author chris
 */
class UsersController extends BaseController {
    
    protected $layout = "layouts.main";
    
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
            
        } else {
            // failed show em errors
        }
        
    }
    
}

?>
