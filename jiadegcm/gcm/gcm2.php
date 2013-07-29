<?php
               // Replace with real server API key from Google APIs  
                $apiKey = "AIzaSyDmtrm8aTDVQkWRQXos_7jbuV1uTjaGWdo";    

                  // Replace with real client registration IDs
               $registrationIDs = array( "APA91bFyOybovx0Zi3hF2pSpIIP-WQ6vbHIBAWzcLhTQPUaAjeKAzsteL6bSdWd1SyO_KV4o6aOZ94CAQTvHQ_s6MOqaGndes5ijEsZ1AsonGozYA2BG4S-f3DhMU8rMyVSosVfM180qyQQ1Wo4CwO2Qk29rPPsEww");

              // Message to be sent
             $message = "{\"Id\":3, \"Expiry\":\"05-27-2013 00:00:00\", \"Content\":\"This is a test.\", \"Picture\":\"http://www.naturalhealth365.com/images/apple.jpg\", \"PictureLink\":\"http://google.com\"}";

             // Set POST variables
            $url = 'https://android.googleapis.com/gcm/send';

           $fields = array(
           'registration_ids' => $registrationIDs,
             'data' => array( "message" => $message ),
            );
         $headers = array(
          'Authorization: key=' . $apiKey,
         'Content-Type: application/json'
          );

         // Open connection
              $ch = curl_init();

            // Set the url, number of POST vars, POST data
            curl_setopt( $ch, CURLOPT_URL, $url );
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
            //curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         //     curl_setopt($ch, CURLOPT_POST, true);
           //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields ));

                // Execute post
             $result = curl_exec($ch);

            // Close connection
               curl_close($ch);
             echo $result;
              //print_r($result);
               //var_dump($result);
           ?>