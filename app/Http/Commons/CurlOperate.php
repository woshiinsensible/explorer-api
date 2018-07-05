<?php
namespace App\Http\Commons;

class CurlOperate
{
    const rpc_url = '*';
    const rpc_user = '*';
    const rpc_pwd  = '*';

   public function GetAddressBalance($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"getaddressbalance","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }

   }

   public function GetBlockHash($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"getblockhash","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function GetBlock($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"getblock","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function GetTransactionNew($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"gettransactionnew","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function GetBlockCount()
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"getblockcount","params"=>[],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function ListDelegates()
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"listdelegates","params"=>[],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function ListReceivedVotes($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"listreceivedvotes","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function ListVotedDelegates($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"listvoteddelegates","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function GetDelegateVotes($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"getdelegatevotes","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               return $response;
           }
       }
   }

   public function SearchHash($params)
   {
       $url  = self::rpc_url;
       $user = self::rpc_user;
       $pwd  = self::rpc_pwd;
       $Author = $user.':'.$pwd;

       $backStatus = 888;

       $flag = 1;

       while($backStatus != 200){
           $jsonArr = ["method"=>"gettransactionnew","params"=>[$params],"id"=>1];
           $jsonStr = json_encode($jsonArr);
           $Authorization = base64_encode($Author);

           $ch = curl_init();
           curl_setopt($ch, CURLOPT_POST, 1);
           curl_setopt($ch, CURLOPT_URL, $url);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
           curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json; charset=utf-8',
                   'Content-Length: ' . strlen($jsonStr),
                   'Authorization:Basic '.$Authorization
               )
           );

           $response = curl_exec($ch);

           $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

           if($backStatusTemp != 200 && $flag >2){

               $backStatus1 = 888;

               $flag1 = 1;

               while($backStatus1 != 200){
                   $jsonArr1 = ["method"=>"getblock","params"=>[$params],"id"=>1];
                   $jsonStr1 = json_encode($jsonArr1);
                   $Authorization = base64_encode($Author);

                   $ch1 = curl_init();
                   curl_setopt($ch1, CURLOPT_POST, 1);
                   curl_setopt($ch1, CURLOPT_URL, $url);
                   curl_setopt($ch1, CURLOPT_POSTFIELDS, $jsonStr1);
                   curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                   curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
                           'Content-Type: application/json; charset=utf-8',
                           'Content-Length: ' . strlen($jsonStr1),
                           'Authorization:Basic '.$Authorization
                       )
                   );

                   $response1 = curl_exec($ch1);

                   $backStatusTemp1= curl_getinfo($ch1,CURLINFO_HTTP_CODE);

                   $flag1++;

                   if($flag1 > 3){
                       $errorArray1 = [
                           'msg'   => 'Request data does not exist!'
                       ];
                       curl_close($ch1);
                       return json_encode($errorArray1);
                   }

                   if($backStatusTemp1 == 200){
                       curl_close($ch1);
                       $resArray1 = array(
                           'type' => 'BlockHash',
                           'data' => $response1
                       );
                       return json_encode($resArray1);
                   }
               }
           }

           $flag++;

           if($flag > 3){
               $errorArray = [
                   'msg'   => 'Request data does not exist!'
               ];
               curl_close($ch);
               return json_encode($errorArray);
           }

           if($backStatusTemp == 200){
               curl_close($ch);
               $resArray = array(
                   'type' => 'TXHash',
                   'data' => $response
               );
               return json_encode($resArray);
           }
       }
   }

   public function GetTxByAddr($params)
    {
        $url  = self::rpc_url;
        $user = self::rpc_user;
        $pwd  = self::rpc_pwd;
        $Author = $user.':'.$pwd;

        $backStatus = 888;

        $flag = 1;

        while($backStatus != 200){
            $jsonArr = ["method"=>"getaddresstxids","params"=>[$params],"id"=>1];
            $jsonStr = json_encode($jsonArr);
            $Authorization = base64_encode($Author);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Content-Length: ' . strlen($jsonStr),
                    'Authorization:Basic '.$Authorization
                )
            );

            $response = curl_exec($ch);

            $backStatusTemp= curl_getinfo($ch,CURLINFO_HTTP_CODE);

            $flag++;

            if($flag > 3){
                $errorArray = [
                    'msg'   => 'Request data does not exist!'
                ];
                curl_close($ch);
                return json_encode($errorArray);
            }

            if($backStatusTemp == 200){
                curl_close($ch);
                return $response;
            }
        }
    }

}