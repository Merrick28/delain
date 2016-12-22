<?php 
/* 
Originally 
By: Jon S. Stevens jon@clearink.com 
Copyright 1998 Jon S. Stevens, Clear Ink 
This code has all the normal disclaimers. 
It is free for any use, just keep the credits intact. 

Enhancements and modifications: 

           By:  Shane Y. Gibson  shane@tuna.org 
Organization:  The Unix Network Archives (http://www.tuna.org./) 
         Date:  November 16th, 1998 
      Changes:  Added **all** comments, as original code lacked them. 
                Added some return codes to include a bit more description 
                for useability. 
                 
          By : berber 
Organization : webdev.berber.co.il 
        Date : April 10th, 1999 
     Changes : The script now handls all kind of domains (not only @xxx.yyy) as before. 
               Added a debuging mode which also works as a verbose mode. 
                
          By : Pascal Chambenoit 
Organization : Techno-Prime, France 
        Date : June 16th, 1999 
     Changes : The script now execute a "negative" check of the email address. 
               As backup servers allways respond by a 250.. user known, 
               if a MX tell us that a user is not known, perhaps can we trust it. 
               Also added a \r to all fputs because some servers doesnt understand \n. 
               However, there are a few servers correctly configured on Earth... :-) 
*/ 

/*  This function takes in an email address (say 'shane@tuna.org') 
*  and tests to see if it's a valid email address. 
* 
*  An array with the results is passed back to the caller. 
* 
*  Possible result codes for the array items are: 
* 
*  Item 0:  [true|false]        true for valid email address 
*                    false for NON-valid email address 
* 
*  Item 1:  [SMTP Code]        if a valid MX mail server found, then 
*                    fill this array in with failed SMTP 
*                    reply codes 
* 
*  Item 2:  [true|false]        true for valid mail server found for 
*                    host/domain 
*                    false if no valid mail server found 
* 
*  Item 3:  [MX server]        if a valid MX host was found and 
*                    connected to then fill in this item 
*                    with the MX server hostname 
* 
*  EXAMPLE code for use is at the very end of this function. 
*/ 

function validateEmail ($email) 
{ 
    $debug_=0; 
    // used for SMTP HELO argument 
    global $SERVER_NAME;   

    // initialize our return array, populating with default values 
    $return=array(false,"","",""); 

    // assign our user part and domain parts respectively to seperate              
    // variables 
    $donnees = explode("@",$email);
    if (count($donnees) != 2)
    {
        $return[0] = false; 
        $return[1] = "Invalid email address (bad format)"; 
        $return[2] = false; 
        
        return $return;
    }
    
    list($user, $domain) = $donnees; 

    if($debug_==1) { 
        echo"user: $user<BR>";    
        echo"domain: $domain<BR>";    
    } 
    // split up the domain name into sub-parts 
    $arr=explode(".",$domain); 

    // figure out how many parts to the host/domain name portion there are 
    $count=count($arr); 

    // get our Top-Level Domain portion (i.e. foobar.org) 
    $tld=$arr[$count-2].".".$arr[$count-1]; 

    // check that an MX record exists for Top-Level Domain, and if so 
    // start our email address checking 
    if (checkdnsrr($domain,"MX")) 
    { 
         if($debug_==1) { 
          echo"Check DNS RR OK<BR>"; 
         } 
        // Okay...valid dns reverse record; test that MX record for 
        // host exists, and then fill the 'mxhosts' and 'weight' 
        // arrays with the correct information 
        // 
        if (getmxrr($domain,$mxhosts,$weight)) 
        { 
            if($debug_==1) { 
                echo "MX LOOKUP RESULTS :<BR>"; 
                for ( $i = 0; $i < count($mxhosts); $i++ ) { 
                    echo "??????o $weight[$i] $mxhosts[$i]<BR>"; 
                } 
                echo "<b>".count($mxhosts)." mail-servers found for this domain</b><BR>"; 

            } 

        // sift through the 'mxhosts' connecting to each host 
            for ( $i=0; $i < count($mxhosts); $i++) 
            { 
                // open socket on port 25 to mxhosts, setting                          
                // returned file pointer to the variable 'fp' 
                $fp = fsockopen ($mxhosts[$i], 25 ); 

                // if the 'fp' was set, then goto work 
                if ($fp) 
                { 
                    if($debug_==1) { 
                    echo"<BR><BR><h2>$mxhosts[$i]</h2>"; 
                    echo"Socket Opened successfully...<BR>"; 
                       } 
                // work variables 
                    $s = 0; 
                    $c = 0; 
                    $out = ""; 
                    // set our created socket for 'fp' to      
                    // non-blocking mode 
                    // so our fgets() calls will return 
                    // right away 
                    stream_set_blocking ( $fp, false ); 

                    // as long as our 'out' variable has a 
                    // null value ("") 
                    // keep looping (do) until we get 
                    // something 
                    // 
                    do 
                    { 
                        // output of the stream assigned 
                        // to 'out' variable 
                        $out = fgets ( $fp, 2500 ); 
                        if($debug_==1) { 
                           if($out != "")  echo"out: $out<BR>"; 
                        } 
                        // if we get an "220" code (service ready code (i.e greeting)) 
                        // increment our work (code (c)) variable, and null 
                        // out our output variable for a later loop test 
                        // 
                        if ( ereg ( "^220", $out ) ) 
                        { 
                            if($debug_==1) { 
                            echo"Service ready on recipient machine.<BR>"; 
                            } 
                            $s = 0; 
                            $out = ""; 
                            $c++; 
                            $return[2] = true; 
                            $return[3] = $mxhosts[$i]; 
                        } 
                        // elseif c is greater than 0 
                        // and 'out' is null (""), 
                        // we got a code back from some 
                        // server, and we've passed 
                        // through this loop at least 
                        // once 
                        // 
                        else if (($c > 0) && ($out == "")) 
                        { 
                               $return[2] = true; 
                            break; 
                        } 

                        // else increment our 's' 
                        // counter 
                        else 
                        { $s++;    } 
                     
                        // and if 's' is 9999, break, to 
                        // keep from looping 
                        // infinetly 
                        if ( $s == 9999 ) { 
                            if($debug_==1) { 
                                echo"Reached maximum 10000 loops, breaking.<BR>"; 
                            }     
                        break; 
                        } 
                     
                    } while ( $out == "" ); 

                    // reset our file pointer to blocking 
                    // mode, so we wait 
                    // for communication to finish before 
                    // moving on... 
                    stream_set_blocking ( $fp, true ); 

                    // talk to the MX mail server, 
                    // validating ourself (HELO) 
                    fputs ( $fp, "HELO ".$SERVER_NAME."\r\n"); 
                     if($debug_==1) { 
                        echo"<BR>HELO $SERVER_NAME<BR>"; 
                    } 
                    // get the mail servers reply, assign to 
                    // 'output' (ignored) 
                    $output = fgets ( $fp, 2000 ); 
                      if($debug_==1) { 
                        echo"output : $output<BR>"; 
                     } 
                      // give a bogus "MAIL FROM:" header to 
                    // the server 
                    fputs ($fp,"MAIL FROM: <info@".$domain.">\r\n"); 
                     if($debug_==1) { 
                        echo"MAIL FROM: <info@".$domain."><BR>"; 
                    } 
                    // get output again (ignored) 
                    $output = fgets ( $fp, 2000 ); 
                      if($debug_==1) { 
                        echo"output : $output<BR>"; 
                     } 
                    // give RCPT TO: header for the email 
                    // address we are testing 
                    fputs($fp,"RCPT TO: <".$email.">\r\n");                 
                    if($debug_==1) { 
                        echo"RCPT TO: <$email><BR>"; 
                    } 
                    // get final output for validity testing 
                    // (used) 
                    $output = fgets ($fp, 2000); 
                      if($debug_==1) { 
                        echo"output : $output<BR>"; 
                     } 
                    // test the reply code from the mail 
                    // server for the 550 (no recipient) code 
                    if (ereg("^550",$output)) 
                    { 
                      if($debug_==1) { 
                          echo"Recipient doesnt exist<BR>"; 
                        } 
                    // set our true/false(ness) 
                    // array item for testing 
                        $return[0] = false; 
                        $return[1] = $output; 

                    } 
                    else 
                    { 
                        // otherwise, the address is valid, 
                        // fillin the 2nd array item 
                        // with the mail servers reply 
                        // code for user to test if they 
                        // want 
                        $return[0] = true; 
                        $return[1] = $output; 
                        if($debug_==1) { 
                           echo"The recipient exists <BR>"; 
                        } 
                    } 
                 
                    // tell the mail server we are done 
                    // talking to it 
                    fputs ( $fp, "QUIT\r\n"); 
                     if($debug_==1) { 
                        echo"Quit"; 
                    } 
                    // close the file pointer 
                    fclose($fp); 

                    // if we got a good value break, 
                    // otherwise, we'll keep 
                    // trying MX records until we get a good 
                    // value, or we 
                    // exhaust our possible MX servers 
                    if ($return[0] == false) { 
                        if($debug_==1) { 
                            echo"Recipient doesnt exist... Breaking"; 
                        }     
                    break; 
                     } 
                } 
            } 
        } 
    } else { 
    // No MX record appears for the specified Top-Level Domain; possibly 
    // an invalid host/domain name was specified. 
        $return[0] = false; 
        $return[1] = "Invalid email address (bad domain name)"; 
        $return[2] = false; 
    } // end if checkdnsrr() 

    // return the array for the user to test against 
    return $return; 
}