<?php

    $function = $_POST['function'];

    $log = array();

    switch ($function) {
        #Evaluate getStateOfChat()
        case('getState'):             #If the chat log already exists, return the number of lines in the log.             #Otherwise, return array init (zero)
            if (file_exists('chat.txt')) {
                $lines = file('chat.txt');
            }
            $log['state'] = count($lines);
            break;

        case('update'): #Evaluate updateChat()
            $state = $_POST['state'];
            if (file_exists('chat.txt')) {
                $lines = file('chat.txt');
            }
            $count = count($lines);
            if ($state == $count) {
                #No change since last update.
                #Return no text.
                $log['state'] = $state;
                $log['text'] = false;
            } else {                 #New text. Feed it to the POST.
                $text = array();
                $log['state'] = $state + count($lines) - $state; #Return updated line count in passed datatype.
                foreach ($lines as $line_num => $line) {
                    #Jump to the end of the passed local log and start reading lines.
                    if ($line_num >= $state) {
                        $text[] =  $line = str_replace("\n", "", $line);
                        #Sanitization.                        #TODO Sanitize more thoroughly.                        #TODO Handle exotic characters?
                    }
                }
                $log['text'] = $text;
            }
            break;

        #Evaluate sendChat()
        case('send'):
            $nickname = htmlentities(strip_tags($_POST['nickname']));#Convert URLs to links.#TODO Handle colors.#TODO Handle server commands - /nick, /whois#TODO Make link replacement, <b>/<i> stripping toggleable (for command chat)
             $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
            $message = htmlentities(strip_tags($_POST['message']));#, '<i><b>'));
            if (($message) != "\n") {

                if (preg_match($reg_exUrl, $message, $url)) {
                    $message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>' , $message);
                }   

                
                fwrite(fopen('chat.txt', 'a'), "<span id='chat-message'>". $nickname . "</span>" . $message = str_replace("\n", " ", $message) . "\n");
            }
           break;

        default:
            trigger_error("Error WTF: Impossible request, check Kaguya's nightstand.");
            break;
    }
    echo json_encode($log);

?>