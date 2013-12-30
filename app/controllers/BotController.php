<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BotController
 *
 * @author chris
 */
class BotController extends BaseController {
    
    protected $layout = "layouts.main";
    
    public function getIndex() {
        $this->layout->content = View::make('users.dashboard');
    }
    
}

?>
