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
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getIndex() {
        echo 'Welcome to botland';
    }
    
}

?>
