<?php

/**
 * Simple PHP IRC Bot
 *
 * PHP Version 5
 *
 * LICENSE: This source file is subject to Creative Commons Attribution
 * 3.0 License that is available through the world-wide-web at the following URI:
 * http://creativecommons.org/licenses/by/3.0/.  Basically you are free to adapt 
 * and use this script commercially/non-commercially. My only requirement is that
 * you keep this header as an attribution to my work. Enjoy! 
 *
 * @category   Chat Room Scipt
 * @package    Simple PHP IRC Bot 
 * @author     Super3boy <admin@wildphp.com>
 * @copyright  2010, The Nystic Network
 * @license    http://creativecommons.org/licenses/by/3.0/
 * @link       http://wildphp.com (Visit for updated versions and more free scripts!)
 * @version    1.0.0 (Last updated 03-20-2010)
 *
 */

//So the bot doesnt stop.
set_time_limit(0);
ini_set('display_errors', 'on');

//Sample connection data.
$config = array( 
        'server' => 'irc.fef.net', 
        'port'   => 6667, 
        'channel' => '#wildrunners die',
        'name'   => 'TheSeer', 
        'nick'   => 'TheSeer', 
        'pass'   => '', 
);

/*
//Set your connection data.
$config = array( 
        'server' => 'example.com', 
        'port'   => 6667, 
        'channel' => '#channel',
        'name'   => 'real name', 
        'nick'   => 'user', 
        'pass'   => 'pass',
);
*/
                                 
class IRCBot {

        //This is going to hold our TCP/IP connection
        public $socket;

        //This is going to hold all of the messages both server and client
        public $ex = array();
        
        public $lores = array(); 
        
        /*
        
         Construct item, opens the server connection, logs the bot in
         @param array

        */

        function __construct($config)

        {
                $this->socket = fsockopen($config['server'], $config['port']);
                $this->login($config);
                $this->main($config);
        }



        /*

         Logs the bot in on the server
         @param array

        */

        function login($config)
        {
                $this->send_data('USER', $config['nick'].' wildphp.com '.$config['nick'].' :'.$config['name']);
                $this->send_data('NICK', $config['nick']);
		        $this->join_channel($config['channel']);
        }



        /*

         This is the workhorse function, grabs the data from the server and displays on the browser

        */

        function main($config)
        {             
                $data = fgets($this->socket, 256);
                
                echo nl2br($data);
				
                flush();

                $this->ex = explode(' ', $data);


                if($this->ex[0] == 'PING')
                {
                        $this->send_data('PONG', $this->ex[1]); //Plays ping-pong with the server to stay connected.
                }

                $command = str_replace(array(chr(10), chr(13)), '', $this->ex[3]);

                switch($command) //List of commands the bot responds to from a user.
                {                      
                        case ':!join':
                                $this->join_channel($this->ex[4]);
                                break;                     
                        case ':!part':
                                $this->send_data('PART '.$this->ex[4].' :', 'Wildphp.com Free IRC Bot Script');
                                break;   
                                                                 
                        case ':!say':
                                $message = "";
                                
                                for($i=0; $i <= (count($this->ex)); $i++)
                                {
                                        $message .= $this->ex[$i]." ";
                                }
                                
                                $this->send_data('PRIVMSG '.$this->ex[4].' :', $message);
                                break;                        		
                        
                        case ':!restart':
                                echo "<meta http-equiv=\"refresh\" content=\"5\">";
                                exit;
                        case ':!shutdown':
                        		$this->send_data('QUIT', 'Wildphp.com Free IRC Bot Script');
                                exit;
                        case ':!help':
                                $options = array(
                                  'Add a highlight to the system using the syntax of: !highlight character clan class',
                                  'List current highlights for a clan using the syntax of: !rise, !sth, !bsp etc etc',
                                );
                                $x = 0;
                                for($x = 0; $x < count($options); $x++) {
                                    $this->send_data('PRIVMSG '.$this->ex[2].' :', $options[$x]);
                                }
                                break;
                        case ':!lore':
                                echo "<pre>" . print_r($this->ex, true) . "</pre>"; 
                                // Use $this->ex[4] as the search term
                                $message = 'Lore functionality coming soon!';
                                $this->send_data('PRIVMSG '.$this->ex[2].' :', $message);
                                break; 
                        case ':!highlight':
                                echo "<pre>" . print_r($this->ex, true) . "</pre>";
                                // 4 character name
                                $person = strtolower($this->ex[4]);
                                // 5 clan
                                $clan = strtolower($this->ex[5]);
                                // 6 class
                                $class = strtolower($this->ex[6]);
                                
                                // Room
                                $room_name = $this->ex[2];
                                
                                $con=mysqli_connect("localhost","root","","wildbot");
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                $sql="INSERT INTO highlights (person, clan, class) VALUES ('$person','$clan', '$class')";

                                if (!mysqli_query($con,$sql))
                                {
                                    die('Error: ' . mysqli_error($con));
                                }
                                    echo "1 record added";

                                mysqli_close($con);
                                
                                $message = "I have now added $person as a member of $clan.  They are a $class";
                                
                                $this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                break;
                        case ':!bsp':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'bsp' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!bot':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'bot' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!god':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'god' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!maza':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'maza' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!rgb':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'rgb' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!sth':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'sth' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!rise':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                //$clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $result = mysql_query("SELECT person, class FROM highlights WHERE clan = 'rise' ORDER BY class")or die(mysql_error());
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        case ':!clan':
                                $con = mysql_connect('localhost','root','');
                                mysql_select_db('wildbot');
                                // Check connection
                                if (mysqli_connect_errno())
                                {
                                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                                }
                                
                                $clan = $this->ex[4];
                                
                                $room_name = $this->ex[2];
                                $first_split = split('!',$this->ex[0]);
                                $second_split = split(':',$first_split[1]);
                                $third_split = split('@', $second_split[0]);
                                
                                $user_name = $third_split[0];
                                //$query = "SELECT * FROM highlight WHERE clan = '.$clan.'";
                                
                                $clan = $this->ex[4];
                                echo "Clan is: $clan";
                                $monkeyfarts = "$clan";
                                $result = mysql_query("SELECT * FROM highlights WHERE clan = '".mysql_real_escape_string($monkeyfarts)."'");
                                echo $monkeyfarts;
                                //$result = mysql_query("SELECT person, class FROM highlights WHERE clan = '".mysql_real_escape_string($clan)."' ORDER BY class");
                                
                                $message = '';
                                
                                while($row = mysql_fetch_array($result)){
                                    $this->send_data('PRIVMSG '.$user_name.' :', '<<Name>>: ' . $row['person'] . " " . '<<Class:>> ' .$row['class']);
                                }
                                
                                //$this->send_data('PRIVMSG '.$room_name.' :', $message);
                                
                                mysql_close($con);
                                break;
                        
                }

                $this->main($config);
        }



        function send_data($cmd, $msg = null) //displays stuff to the broswer and sends data to the server.
        {
                if($msg == null)
                {
                        fputs($this->socket, $cmd."\r\n");
                        echo '<strong>'.$cmd.'</strong><br />';
                } else {

                        fputs($this->socket, $cmd.' '.$msg."\r\n");
                        echo '<strong>'.$cmd.' '.$msg.'</strong><br />';
                }

        }



        function join_channel($channel) //Joins a channel, used in the join function.
        {

                $key = 'die';
                if(is_array($channel))
                {
                        foreach($channel as $chan)
                        {
                                $this->send_data('JOIN', $chan);
                        }

                } else {
                        $this->send_data('JOIN', $channel . ' '.$key);
                }
        }     
}

//Start the bot
$bot = new IRCBot($config);
?>
