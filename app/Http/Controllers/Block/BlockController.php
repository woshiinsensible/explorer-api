<?php

namespace App\Http\Controllers\Block;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Http\Commons\CurlOperate;
use App\Http\Commons\RedisOperate;

class BlockController extends BaseController
{
    //GetAddressBalance
    public function GetAddressBalance(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Addr Empty!'));
        }

        $param = trim($request->input('param'));
        $addrLen = strlen($param);

        if($addrLen < 26 || $addrLen > 34){
            return json_encode(array('error'=>1,'msg'=>'Addr Format Error!'));
        }

        $curlOperate = new CurlOperate();

        $curl = $curlOperate->GetAddressBalance($param);

        $arrRes = json_decode($curl,1);

        if(array_key_exists('msg',$arrRes)){
            return json_encode(array('error'=>1,'msg'=>$arrRes['msg']));
        }

        $arrRes['addr'] = $param;
        $response = json_encode($arrRes);
        return $response;
    }


    //GetBlockInfo
    public function GetBlockInfo(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $page = $request->input('page',1);
        $count = 20;

        if($page < 1){
            $page = 1;
        }

        if($page > 5){
            $page = 5;
        }

        $num = 100;

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Addr Empty!'));
        }

        $param = trim($request->input('param'));
        if(!is_numeric($param)){
            return json_encode(array('error'=>1,'msg'=>'Block Not Num!'));
        }

        if($param < 1){
            return json_encode(array('error'=>1,'msg'=>'Block Too Small!'));
        }

        //RedisExist
        $redisParam = $param.'-'.$page;
        $redis = new RedisOperate();
        $redisExist = $redis->RedisExist($redisParam);

        if($redisExist){
            return $redis->RedisGet($redisParam);
        }else{
            $param = intval($param);

            $curlOperate = new CurlOperate();

            $curl = $curlOperate->GetBlockHash($param);

            $resBlockHash = json_decode($curl,1);

            if(array_key_exists('msg',$resBlockHash)){
                return json_encode(array('error'=>1,'msg'=>$resBlockHash['msg']));
            }

            $resHash = $resBlockHash["result"];

            //final res
            $zRes = [];

            $blockInfo = $curlOperate->GetBlock($resHash);

            $blockInfoRes = json_decode($blockInfo,1);

            if(array_key_exists('msg',$blockInfoRes)){
                return json_encode(array('error'=>1,'msg'=>$blockInfoRes['msg']));
            }

            $txArray = $blockInfoRes['result']['tx'];

            //back 100tx
            $txArray = array_slice($txArray,0,$num);

            $pageCount = ceil(count($txArray) / $count);
            $pageCount = $pageCount < 1 ? 1 : $pageCount;

            if ($page > $pageCount) {
                return json_encode(array('error' => 1, 'msg' => 'Page Too More!'));
            }

            $offset = $count * ($page - 1);

            $txArray = array_slice($txArray,$offset,$count);

            $txResArray = array();
            foreach ($txArray as $tx){
                $txInfo = $curlOperate->GetTransactionNew($tx);

                $txInfoArray = json_decode($txInfo,1);

                if(array_key_exists('msg',$txInfoArray)){
                    return json_encode(array('error'=>1,'msg'=>$txInfoArray['msg']));
                }

                $txVin = $txInfoArray['result']['vin'];

                foreach ($txVin as $txkey => $txval){
                    if (!array_key_exists('coinbase',$txval)){
                        $vinHash = $txval['txid'];

                        $voutN = $txval['vout'];

                        $txVout = $curlOperate->GetTransactionNew($vinHash);

                        $txInfoArray2 = json_decode($txVout,1);

                        if(array_key_exists('msg',$txInfoArray2)){
                            return json_encode(array('error'=>1,'msg'=>$txInfoArray2['msg']));
                        }

                        $txVoutArray = $txInfoArray2['result']['vout'];
                        foreach ($txVoutArray as $txOutVal) {
                            if(array_key_exists("addresses", $txOutVal["scriptPubKey"])){
                                if($txOutVal['n'] === $voutN){
                                    $value = $txOutVal['value'];
                                    $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                    $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                    $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                                }
                            }
                        }
                    }
                }
                array_push($txResArray,$txInfoArray);
            }
            array_push($zRes,$blockInfoRes,$txResArray);

            $zRes = json_encode($zRes);
            $redis->RedisSet($redisParam,$zRes);
            return $zRes;
        }
    }


    //GetBlockInfoByHash
    public function GetBlockInfoByHash(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $page = $request->input('page',1);
        $count = 20;

        if($page < 1){
            $page = 1;
        }

        if($page > 5){
            $page = 5;
        }

        $num = 100;

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input BlockHash Empty!'));
        }

        $param = trim($request->input('param'));
        $len = strlen($param);
        if($len != 64){
            return json_encode(array('error'=>1,'msg'=>'Block Hash Error!'));
        }

        //RedisExist
        $redisParam = $param.'-'.$page;
        $redis = new RedisOperate();
        $redisExist = $redis->RedisExist($redisParam);
        $zRes = array();

        if($redisExist){
            return $redis->RedisGet($redisParam);
        }else{
            $curlOperate = new CurlOperate();

            $blockInfo = $curlOperate->GetBlock($param);

            $blockInfoRes = json_decode($blockInfo,1);

            if(array_key_exists('msg',$blockInfoRes)){
                return json_encode(array('error'=>1,'msg'=>$blockInfoRes['msg']));
            }

            $txArray = $blockInfoRes['result']['tx'];

            //back 100 txs
            $txArray = array_slice($txArray,0,$num);

            $pageCount = ceil(count($txArray) / $count);
            $pageCount = $pageCount < 1 ? 1 : $pageCount;


            if ($page > $pageCount) {
                return json_encode(array('error' => 1, 'msg' => 'Page Too More'));
            }

            $offset = $count * ($page - 1);

            $txArray = array_slice($txArray,$offset,$count);

            $txResArray = array();
            foreach ($txArray as $tx){
                $txInfo = $curlOperate->GetTransactionNew($tx);
                $txInfoArray = json_decode($txInfo,1);

                if(array_key_exists('msg',$txInfoArray)){
                    return json_encode(array('error'=>1,'msg'=>$txInfoArray['msg']));
                }

                $txVin = $txInfoArray['result']['vin'];

                foreach ($txVin as $txkey => $txval){
                    if (!array_key_exists('coinbase',$txval)){
                        $vinHash = $txval['txid'];

                        $voutN = $txval['vout'];

                        $txVout = $curlOperate->GetTransactionNew($vinHash);

                        $txInfoArray2 = json_decode($txVout,1);

                        if(array_key_exists('msg',$txVout)){
                            return json_encode(array('error'=>1,'msg'=>$txVout['msg']));
                        }

                        $txVoutArray = $txInfoArray2['result']['vout'];
                        foreach ($txVoutArray as $txOutVal) {
                            if(array_key_exists("addresses", $txOutVal["scriptPubKey"])){
                                if($txOutVal['n'] === $voutN){
                                    $value = $txOutVal['value'];
                                    $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                    $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                    $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                                }
                            }
                        }
                    }
                }
                array_push($txResArray,$txInfoArray);
            }
            array_push($zRes,$blockInfoRes,$txResArray);

            $zRes = json_encode($zRes);
            $redis->RedisSet($redisParam,$zRes);
            return $zRes;
        }
    }


    //GetTxInfo
    public function GetTxInfo(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))) {
            return json_encode(array('error' => 1, 'msg' => 'Input TX Empty!'));
        }

        $param = trim($request->input('param'));
        $len = strlen($param);
        if ($len != 64) {
            return json_encode(array('error' => 1, 'msg' => 'TX Format Error'));
        }

        //RedisExist
        $redisParam = $param;
        $redis = new RedisOperate();
        $redisExist = $redis->RedisExist($redisParam);

        if($redisExist){
            return $redis->RedisGet($redisParam);
        }else{
            $curlOperate = new CurlOperate();

            $txInfo = $curlOperate->GetTransactionNew($redisParam);

            $txInfoArray = json_decode($txInfo,1);

            if(array_key_exists('msg',$txInfoArray)){
                return json_encode(array('error'=>1,'msg'=>$txInfoArray['msg']));
            }

            $txResult = $txInfoArray['result'];
            if(array_key_exists('confirmations',$txResult)){
                $confirmations = $txResult['confirmations'];

                $flagSum = 50;
                $expire = 30;

                if($confirmations >= $flagSum){
                    $txTemp = $txInfoArray['result']['vin'];
                    foreach ($txTemp as $txkey => $txval) {
                        if (!array_key_exists('coinbase', $txval)) {
                            $vinHash = $txval['txid'];

                            $voutN = $txval['vout'];

                            $txVout = $curlOperate->GetTransactionNew($vinHash);

                            $txVoutArray = json_decode($txVout, 1);

                            if(array_key_exists('msg',$txVoutArray)){
                                return json_encode(array('error'=>1,'msg'=>$txVoutArray['msg']));
                            }

                            $txVoutResArray = $txVoutArray['result']['vout'];

                            foreach ($txVoutResArray as $k=>$txOutVal) {
                                if($txOutVal['n'] === $voutN){
                                    $value = $txOutVal['value'];
                                    $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                    $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                    $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                                }
                            }
                        }

                    }

                    $txInfo = json_encode($txInfoArray);
                    $redis->RedisSet($redisParam,$txInfo);
                    return $txInfo;
                }

                $txTemp = $txInfoArray['result']['vin'];
                foreach ($txTemp as $txkey => $txval) {
                    if (!array_key_exists('coinbase', $txval)) {
                        $vinHash = $txval['txid'];

                        $voutN = $txval['vout'];

                        $txVout = $curlOperate->GetTransactionNew($vinHash);

                        $txVoutArray = json_decode($txVout, 1);

                        if(array_key_exists('msg',$txVoutArray)){
                            return json_encode(array('error'=>1,'msg'=>$txVoutArray['msg']));
                        }

                        $txVoutResArray = $txVoutArray['result']['vout'];

                        foreach ($txVoutResArray as $k=>$txOutVal) {
                            if($txOutVal['n'] === $voutN){
                                $value = $txOutVal['value'];
                                $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                            }
                        }
                    }

                }

                $txInfo = json_encode($txInfoArray);
                $redis->RedisSet($redisParam,$txInfo,$expire);
                return $txInfo;
            }else{
                $txTemp = $txInfoArray['result']['vin'];
                foreach ($txTemp as $txkey => $txval) {
                    if (!array_key_exists('coinbase', $txval)) {
                        $vinHash = $txval['txid'];

                        $voutN = $txval['vout'];

                        $txVout = $curlOperate->GetTransactionNew($vinHash);

                        $txVoutArray = json_decode($txVout, 1);

                        if(array_key_exists('msg',$txVoutArray)){
                            return json_encode(array('error'=>1,'msg'=>$txVoutArray['msg']));
                        }

                        $txVoutResArray = $txVoutArray['result']['vout'];

                        foreach ($txVoutResArray as $k=>$txOutVal) {
                            if($txOutVal['n'] === $voutN){
                                $value = $txOutVal['value'];
                                $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                            }
                        }
                    }
                }
                $txInfo = json_encode($txInfoArray);
                return $txInfo;
            }
        }

    }


    //Search
    public function Search(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1, 'msg'  =>'Input Empty!'));
        }

        $param = trim($request->input('param'));

        //blockcount
        if (is_numeric($param)){
            $page = $request->input('page',1);
            $count = 20;

            if($page < 1){
                $page = 1;
            }

            if($page > 5){
                $page = 5;
            }

            $num = 100;

            if($param < 1){
                return json_encode(array('error'=>1,'msg'=>'Block Too Small!'));
            }

            //RedisExist
            $redisParam = $param.'-'.$page;
            $redis = new RedisOperate();
            $redisExist = $redis->RedisExist($redisParam);

            if($redisExist){
                return $redis->RedisGet($redisParam);
            }else{
                $param = intval($param);

                $curlOperate = new CurlOperate();

                $curl = $curlOperate->GetBlockHash($param);

                $resBlockHash = json_decode($curl,1);

                if(array_key_exists('msg',$resBlockHash)){
                    return json_encode(array('error'=>1,'msg'=>$resBlockHash['msg']));
                }

                $resHash = $resBlockHash["result"];

                $zRes = [];

                $blockInfo = $curlOperate->GetBlock($resHash);

                $blockInfoRes = json_decode($blockInfo,1);

                if(array_key_exists('msg',$blockInfoRes)){
                    return json_encode(array('error'=>1,'msg'=>$blockInfoRes['msg']));
                }

                $txArray = $blockInfoRes['result']['tx'];

                $txArray = array_slice($txArray,0,$num);

                $pageCount = ceil(count($txArray) / $count);
                $pageCount = $pageCount < 1 ? 1 : $pageCount;

                if ($page > $pageCount) {
                    return json_encode(array('error' => 1, 'msg' => 'Page Too More!'));
                }

                $offset = $count * ($page - 1);

                $txArray = array_slice($txArray,$offset,$count);

                $txResArray = array();
                foreach ($txArray as $tx){
                    $txInfo = $curlOperate->GetTransactionNew($tx);

                    $txInfoArray = json_decode($txInfo,1);

                    if(array_key_exists('msg',$txInfoArray)){
                        return json_encode(array('error'=>1,'msg'=>$txInfoArray['msg']));
                    }

                    $txVin = $txInfoArray['result']['vin'];

                    foreach ($txVin as $txkey => $txval){
                        if (!array_key_exists('coinbase',$txval)){
                            $vinHash = $txval['txid'];

                            $voutN = $txval['vout'];

                            $txVout = $curlOperate->GetTransactionNew($vinHash);

                            $txInfoArray2 = json_decode($txVout,1);

                            if(array_key_exists('msg',$txInfoArray2)){
                                return json_encode(array('error'=>1,'msg'=>$txInfoArray2['msg']));
                            }

                            $txVoutArray = $txInfoArray2['result']['vout'];
                            foreach ($txVoutArray as $txOutVal) {
                                if(array_key_exists("addresses", $txOutVal["scriptPubKey"])){
                                    if($txOutVal['n'] === $voutN){
                                        $value = $txOutVal['value'];
                                        $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                        $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                        $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                                    }
                                }
                            }
                        }
                    }
                    array_push($txResArray,$txInfoArray);
                }

                array_push($zRes,$blockInfoRes,$txResArray);

                $zRes = json_encode($zRes);
                $redis->RedisSet($redisParam,$zRes);
                return $zRes;
            }
        }


        //search hash
        if (strlen($param) == 64 ) {
            $redisParam = $param;
            $redis = new RedisOperate();
            $redisExist = $redis->RedisExist($redisParam);

            if ($redisExist) {
                return $redis->RedisGet($redisParam);
            } else {
                $curlOperate = new CurlOperate();

                $txInfo = $curlOperate->SearchHash($redisParam);

                $txInfoArray = json_decode($txInfo, 1);

                if (array_key_exists('msg', $txInfoArray)) {
                    return json_encode(array('error' => 1, 'msg' => $txInfoArray['msg']));
                }

                $backType = $txInfoArray['type'];

                if($backType == 'TXHash'){
                    $txResultTemp = $txInfoArray['data'];
                    $txResultArray = json_decode($txResultTemp,1);
                    $txResult = $txResultArray['result'];

                    if (array_key_exists('confirmations', $txResult)) {
                        $confirmations = $txResult['confirmations'];
                        $flagSum = 50;
                        $expire = 30;

                        if ($confirmations >= $flagSum) {
                            $txTemp = $txResultArray['result']['vin'];
                            foreach ($txTemp as $txkey => $txval) {
                                if (!array_key_exists('coinbase', $txval)) {
                                    $vinHash = $txval['txid'];

                                    $voutN = $txval['vout'];

                                    $txVout = $curlOperate->GetTransactionNew($vinHash);

                                    $txVoutArray = json_decode($txVout, 1);

                                    if (array_key_exists('msg', $txVoutArray)) {
                                        return json_encode(array('error' => 1, 'msg' => $txVoutArray['msg']));
                                    }

                                    $txVoutResArray = $txVoutArray['result']['vout'];

                                    foreach ($txVoutResArray as $k => $txOutVal) {
                                        if ($txOutVal['n'] === $voutN) {
                                            $value = $txOutVal['value'];
                                            $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                            $txResultArray['result']['vin'][$txkey]['value'] = $value;
                                            $txResultArray['result']['vin'][$txkey]['addr'] = $addr;
                                        }
                                    }
                                }
                            }

                            $txInfo = json_encode($txResultArray);
                            $redis->RedisSet($redisParam, $txInfo);
                            return $txInfo;
                        }
                        $txTemp = $txResultArray['result']['vin'];
                        foreach ($txTemp as $txkey => $txval) {
                            if (!array_key_exists('coinbase', $txval)) {
                                $vinHash = $txval['txid'];

                                $voutN = $txval['vout'];

                                $txVout = $curlOperate->GetTransactionNew($vinHash);

                                $txVoutArray = json_decode($txVout, 1);

                                if (array_key_exists('msg', $txVoutArray)) {
                                    return json_encode(array('error' => 1, 'msg' => $txVoutArray['msg']));
                                }

                                $txVoutResArray = $txVoutArray['result']['vout'];

                                foreach ($txVoutResArray as $k => $txOutVal) {
                                    if ($txOutVal['n'] === $voutN) {
                                        $value = $txOutVal['value'];
                                        $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                        $txResultArray['result']['vin'][$txkey]['value'] = $value;
                                        $txResultArray['result']['vin'][$txkey]['addr'] = $addr;
                                    }
                                }
                            }
                        }
                        $txInfo = json_encode($txResultArray);
                        $redis->RedisSet($redisParam, $txInfo, $expire);
                        return $txInfo;
                    }
                }
            }

                if($backType == 'BlockHash'){
                $page = $request->input('page',1);
                $count = 20;

                if($page < 1){
                    $page = 1;
                }

                if($page > 5){
                    $page = 5;
                }

                $num = 100;

                if (empty($request->input('param'))){
                    return json_encode(array('error'=>1,'msg'=>'Input BlockHash Empty!'));
                }

                $param = trim($request->input('param'));
                $len = strlen($param);
                if($len != 64){
                    return json_encode(array('error'=>1,'msg'=>'Block Hash Error!'));
                }

                $param = json_decode($txInfoArray['data'],1)['result']['hash'];

                $redisParam = $param.'-'.$page;
                $redisExist = $redis->RedisExist($redisParam);
                $zRes = array();

                if($redisExist){
                    return $redis->RedisGet($redisParam);
                }else{
                    $blockInfo = $curlOperate->GetBlock($param);

                    $blockInfoRes = json_decode($blockInfo,1);

                    if(array_key_exists('msg',$blockInfoRes)){
                        return json_encode(array('error'=>1,'msg'=>$blockInfoRes['msg']));
                    }

                    $txArray = $blockInfoRes['result']['tx'];

                    $txArray = array_slice($txArray,0,$num);

                    $pageCount = ceil(count($txArray) / $count);
                    $pageCount = $pageCount < 1 ? 1 : $pageCount;

                    if ($page > $pageCount) {
                        return json_encode(array('error' => 1, 'msg' => 'Page Too More'));
                    }

                    $offset = $count * ($page - 1);
                    $txArray = array_slice($txArray,$offset,$count);

                    $txResArray = array();
                    foreach ($txArray as $tx){
                        $txInfo = $curlOperate->GetTransactionNew($tx);

                        $txInfoArray = json_decode($txInfo,1);

                        if(array_key_exists('msg',$txInfoArray)){
                            return json_encode(array('error'=>1,'msg'=>$txInfoArray['msg']));
                        }

                        $txVin = $txInfoArray['result']['vin'];

                        foreach ($txVin as $txkey => $txval){
                            if (!array_key_exists('coinbase',$txval)){
                                $vinHash = $txval['txid'];

                                $voutN = $txval['vout'];

                                $txVout = $curlOperate->GetTransactionNew($vinHash);

                                $txInfoArray2 = json_decode($txVout,1);

                                if(array_key_exists('msg',$txInfoArray2)){
                                    return json_encode(array('error'=>1,'msg'=>$txVout['msg']));
                                }

                                $txVoutArray = $txInfoArray2['result']['vout'];
                                foreach ($txVoutArray as $txOutVal) {
                                    if(array_key_exists("addresses", $txOutVal["scriptPubKey"])){
                                        if($txOutVal['n'] === $voutN){
                                            $value = $txOutVal['value'];
                                            $addr = $txOutVal["scriptPubKey"]["addresses"][0];
                                            $txInfoArray['result']['vin'][$txkey]['value'] = $value;
                                            $txInfoArray['result']['vin'][$txkey]['addr'] = $addr;
                                        }
                                    }
                                }
                            }
                        }
                        array_push($txResArray,$txInfoArray);
                    }
                    array_push($zRes,$blockInfoRes,$txResArray);
                    $zRes = json_encode($zRes);
                    $redis->RedisSet($redisParam,$zRes);
                    return $zRes;
                }
            }
        }

        //search address
        if(strlen($param) >= 26 && strlen($param) <= 34){
            $curlOperate = new CurlOperate();

            $curl = $curlOperate->GetAddressBalance($param);

            $arrRes = json_decode($curl,1);

            if(array_key_exists('msg',$arrRes)){
                return json_encode(array('error'=>1,'msg'=>$arrRes['msg']));
            }

            $arrRes['addr'] = $param;
            $response = json_encode($arrRes);
            return $response;
        }

        $redis = new RedisOperate();
        $lbtcname = 'lbtc-'.$param;
        $address = $redis->RedisGet($lbtcname);

        if($address){
            $curlOperate = new CurlOperate();
            $curl = $curlOperate->GetAddressBalance($address);
            $arrRes = json_decode($curl,1);

            if(array_key_exists('msg',$arrRes)){
                return json_encode(array('error'=>1,'msg'=>$arrRes['msg']));
            }

            $arrRes['addr'] = $address;
            $response = json_encode($arrRes);
            return $response;
        }
        return json_encode(array('error'=>1,'msg'=>'Input Error'));
    }


    //Index
    public function Index()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $redis = new RedisOperate();
        $indexInfo = $redis->RedisGet('index');
        return $indexInfo;
    }


    //SetIndex
    public function SetIndex()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $curlOperate = new CurlOperate();

        $height = $curlOperate->GetBlockCount();

        $heightArray = json_decode($height, 1);

        if(array_key_exists('msg',$heightArray)){
            return json_encode(array('error'=>1,'msg'=>$heightArray['msg']));
        }

        $height = intval($heightArray['result']);

        $count = 20;
        $heightArray = array();

        for ($i = $height; $i > $height - $count; $i--) {
            $blockHash = $curlOperate->GetBlockHash($i);

            $blockHashArray = json_decode($blockHash, 1);

            if(array_key_exists('msg',$blockHashArray)){
                return json_encode(array('error'=>1,'msg'=>$blockHashArray['msg']));
            }

            $blockHash = $blockHashArray['result'];

            $blockInfo = $curlOperate->GetBlock($blockHash);

            $blockInfoArray = json_decode($blockInfo, 1);

            if(array_key_exists('msg',$blockInfoArray)){
                return json_encode(array('error'=>1,'msg'=>$blockInfoArray['msg']));
            }
            array_push($heightArray,$blockInfoArray);
        }

        $indexStr =  json_encode($heightArray);
        $redis = new RedisOperate();
        $redis->RedisSet('index',$indexStr);
    }


    //ListWitnesses
    public function ListWitnesses()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $curlOperate = new CurlOperate();

        $listDelegates = $curlOperate->ListDelegates();

        $listDelegatesArray = json_decode($listDelegates, 1);

        if(array_key_exists('msg',$listDelegatesArray)){
            return json_encode(array('error'=>1,'msg'=>$listDelegatesArray['msg']));
        }

        $redis = new RedisOperate();

        $resListDelegates = $listDelegatesArray['result'];

        foreach ($resListDelegates as $delegatesVal){
            $redis->RedisSet($delegatesVal['address'],$delegatesVal['name']);
            $redis->RedisSet('lbtc-'.$delegatesVal['name'],$delegatesVal['address']);
        }
    }


    //GetVotersByWitness
    public function GetVotersByWitness(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Empty!'));
        }

        $param = trim($request->input('param'));
        $addrLen = strlen($param);
        if($addrLen < 26 || $addrLen > 34){
            $resParam = $param;
        }else{
            $redis = new RedisOperate();
            $resParam = $redis->RedisGet($param);
            if(empty($resParam)){
                return json_encode(array('error'=>1,'msg'=>'Input information Error!'));
            }
        }
        $curlOperate = new CurlOperate();

        $listReceivedVotes = $curlOperate->ListReceivedVotes($resParam);

        $listReceivedVotesArray = json_decode($listReceivedVotes,1);

        if(array_key_exists('msg',$listReceivedVotesArray)){
            return json_encode(array('error'=>1,'msg'=>$listReceivedVotesArray['msg']));
        }
        return $listReceivedVotes;
    }


    //GetVoteByAddress
    public function GetVoteByAddress(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Empty!'));
        }

        $param = trim($request->input('param'));
        $addrLen = strlen($param);
        if($addrLen < 26 || $addrLen > 34){
            $redis = new RedisOperate();
            $resParam = $redis->RedisGet('lbtc-'.$param);
            if(empty($resParam)){
                return json_encode(array('error'=>1,'msg'=>'Input information Error!'));
            }
        }else{
            $resParam = $param;
        }

        $curlOperate = new CurlOperate();

        $response = $curlOperate->ListVotedDelegates($resParam);

        $responseArray = json_decode($response,1);

        if(array_key_exists('msg',$responseArray)){
            return json_encode(array('error'=>1,'msg'=>$responseArray['msg']));
        }

        return $response;
    }


    //GetWitnessShare
    public function GetWitnessShare(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Empty!'));
        }

        $param = $request->input('param');

        $curlOperate = new CurlOperate();

        $response = $curlOperate->GetDelegateVotes($param);

        $responseArray = json_decode($response,1);

        if(array_key_exists('msg',$responseArray)){
            return json_encode(array('error'=>1,'msg'=>$responseArray['msg']));
        }
        return $response;
    }


    //SetRedis
    public function SetRedis()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $curlOperate = new CurlOperate();

        $response = $curlOperate->ListDelegates();

        $responseArray = json_decode($response,1);

        if(array_key_exists('msg',$responseArray)){
            return json_encode(array('error'=>1,'msg'=>$responseArray['msg']));
        }

        $delegatesArray = $responseArray['result'];

        $redis = new RedisOperate();

        foreach ($delegatesArray as $key => $val){

            $response = $curlOperate->GetDelegateVotes($val['name']);

            $responseArray = json_decode($response,1);

            if(array_key_exists('msg',$responseArray)){
                return json_encode(array('error'=>1,'msg'=>$responseArray['msg']));
            }

            $delegatesArray[$key]['count'] = $responseArray['result'];
        }

        if($delegatesArray){
            $delegatesJson = json_encode($delegatesArray);
            $redis->RedisSet('nodesort',$delegatesJson);
        }
    }


    //GetActive
    public function GetActive()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $redis = new RedisOperate();
        $redisExist = $redis->RedisExist('nodesort');

        if($redisExist){
            $nodeSort = $redis->RedisGet('nodesort');
            $nodeSortArray = json_decode($nodeSort,1);
        }

        foreach ($nodeSortArray as $val){
            $result[] = $val;
        }

        array_multisort(array_column($result,'count'),SORT_DESC,$result);

        return json_encode($result);
    }


    //GetListDelegates
    public function GetListDelegates()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $redis = new RedisOperate();
        $redisExist = $redis->RedisExist('nodesort');

        if($redisExist){
            $redisJson = $redis->RedisGet('nodesort');
            return $redisJson;
        }
        return json_encode(array('error'=>1,'msg'=>'Key NoExist'));
    }


    //SetBlockCount
    public function SetBlockCount()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $curlOperate = new CurlOperate();

        $response = $curlOperate->GetBlockCount();

        $responseArray = json_decode($response,1);

        if(array_key_exists('msg',$responseArray)){
            return json_encode(array('error'=>1,'msg'=>$responseArray['msg']));
        }

        $blockInt = $responseArray['result'];

        $blcokHashJson = $curlOperate->GetBlockHash($blockInt);

        $blcokHashArray = json_decode($blcokHashJson,1);

        if(array_key_exists('msg',$blcokHashArray)){
            return json_encode(array('error'=>1,'msg'=>$blcokHashArray['msg']));
        }

        $blockHash = $blcokHashArray['result'];

        $blockInfo = json_encode(array('blockcount' => $blockInt,'blockhash' =>$blockHash));

        $redis = new RedisOperate();

        $redis->RedisSet('blockcount',$blockInfo);
    }


    //NewSetNode
    public function NewSetNode()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $curlOperate = new CurlOperate();
        $redis = new RedisOperate();

        $response = $curlOperate->GetBlockCount();

        $blockCount = json_decode($response,1);

        if(array_key_exists('msg',$blockCount)){
            return json_encode(array('error'=>1,'msg'=>$blockCount['msg']));
        }

        $blockInt = $blockCount['result'];

        $blockRedis = $redis->RedisGet('blockcountnew2');

        if($blockRedis){
            if($blockInt > $blockRedis){
                $redis->RedisSet('blockcountnew2',$blockInt);

                $blockInt = intval($blockInt);

                $blockHash = $curlOperate->GetBlockHash($blockInt);

                $blockHashArray = json_decode($blockHash, 1);

                if (array_key_exists('msg', $blockHashArray)) {
                    return json_encode(array('error' => 1, 'msg' => $blockHashArray['msg']));
                }

                $blcokHash = $blockHashArray['result'];

                $blockInfo = $curlOperate->GetBlock($blcokHash);

                $blockInfoArray = json_decode($blockInfo, 1);

                if (array_key_exists('msg', $blockInfoArray)) {
                    return json_encode(array('error' => 1, 'msg' => $blockInfoArray['msg']));
                }

                $blockInfo = $blockInfoArray['result'];

                $tx = $blockInfo['tx'][0];

                $txs = $curlOperate->GetTransactionNew($tx);

                $txArray = json_decode($txs, 1);

                if (array_key_exists('msg', $txArray)) {
                    return json_encode(array('error' => 1, 'msg' => $txArray['msg']));
                }

                $nodeDelegates = $txArray['result']['vout'][1];

                $coinbaseAddr = $txArray['result']['vout'][0]["scriptPubKey"]["addresses"][0];

                if (array_key_exists('type', $nodeDelegates) && $nodeDelegates['type'] == 'CoinbaseDelegateInfo') {
                    $nodeDelegatesArray = $nodeDelegates['delegates'];

                    $coinbaseKey = array_keys($nodeDelegatesArray,$coinbaseAddr)[0];

                    $res = $redis->RedisHGetAll('1');

                    if($res){
                        $tempArray = unserialize($redis->RedisGet('nodetemp2'));
                        $addr = $redis->RedisGet('nodenow2');
                        $number = $tempArray[$addr];
                        $arrayCount = count($nodeDelegatesArray);

                        if($number < $arrayCount){
                            for($i = $number+1;$i<=$arrayCount;$i++){
                                $iSum = $redis->RedisHGet($i,'sum');
                                $redis->RedisHSet($i,'status',-1);
                                $redis->RedisHSet($i,'sum',$iSum+1);
                            }
                        }

                        $redisArray = array();

                        foreach ($nodeDelegatesArray as $nKey => $nVal){
                            $redisKey = $nKey + 1;
                            $redisArray[$nVal] = $redisKey;
                            $resTemp = $redis->RedisHVals($redisKey);
                            $addrTemp = $resTemp[0];

                            $res1 = DB::table('nodeinfosave')
                                ->select('s_id')
                                ->where('s_addr', $addrTemp)
                                ->get();

                            if($res1){
                                $res1 = $res1[0];
                                $s_id = $res1->s_id;
                                DB::table('nodeinfosave')
                                    ->where('s_id', $s_id)
                                    ->update(['s_status' => $resTemp[1],'s_count' => $resTemp[3],'s_sum' => $resTemp[4]]);
                            }else{
                                DB::table('nodeinfosave')
                                    ->insert(['s_addr' => $addrTemp,'s_status' => $resTemp[1],'s_count' => $resTemp[3],'s_sum' => $resTemp[4]]);
                            }

                            $redis->RedisDel($redisKey);

                            $res2 = DB::table('nodeinfosave')
                                ->select('s_addr','s_status','s_count','s_sum')
                                ->where('s_addr', $nVal)
                                ->get();

                            if($res2){
                                $res2 = $res2[0];
                                if($nKey < $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',-1);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum+1);
                                }elseif ($nKey == $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',1);
                                    $redis->RedisHSet($redisKey,'now',1);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count+1);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum+1);
                                }else{
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',$res2->s_status);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum);
                                }
                            }else{
                                if($nKey < $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',-1);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',0);
                                    $redis->RedisHSet($redisKey,'sum',1);
                                }elseif ($nKey == $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',1);
                                    $redis->RedisHSet($redisKey,'now',1);
                                    $redis->RedisHSet($redisKey,'count',1);
                                    $redis->RedisHSet($redisKey,'sum',1);
                                }else{
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',0);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',0);
                                    $redis->RedisHSet($redisKey,'sum',0);
                                }
                            }
                        }
                        $redis->RedisSet('nodetemp2',serialize($redisArray));
                        $redis->RedisSet('nodenow2', $coinbaseAddr);
                    }else{
                        $redisArray = array();

                        $coinbaseKey = array_keys($nodeDelegatesArray,$coinbaseAddr)[0];

                        foreach ($nodeDelegatesArray as $nKey => $nVal){
                            $redisKey = $nKey + 1;
                            $redisArray[$nVal] = $redisKey;

                            $res2 = DB::table('nodeinfosave')
                                ->select('s_addr','s_status','s_count','s_sum')
                                ->where('s_addr', $nVal)
                                ->get();

                            if($res2){
                                $res2 = $res2[0];
                                if($nKey < $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',-1);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum+1);
                                }elseif ($nKey == $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',1);
                                    $redis->RedisHSet($redisKey,'now',1);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count+1);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum+1);
                                }else{
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',$res2->s_status);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',$res2->s_count);
                                    $redis->RedisHSet($redisKey,'sum',$res2->s_sum);
                                }
                            }else{
                                if($nKey < $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',-1);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',0);
                                    $redis->RedisHSet($redisKey,'sum',1);
                                }elseif ($nKey == $coinbaseKey){
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',1);
                                    $redis->RedisHSet($redisKey,'now',1);
                                    $redis->RedisHSet($redisKey,'count',1);
                                    $redis->RedisHSet($redisKey,'sum',1);
                                }else{
                                    $redis->RedisHSet($redisKey,'addr',$nVal);
                                    $redis->RedisHSet($redisKey,'status',0);
                                    $redis->RedisHSet($redisKey,'now',0);
                                    $redis->RedisHSet($redisKey,'count',0);
                                    $redis->RedisHSet($redisKey,'sum',0);
                                }
                            }
                        }
                        $redis->RedisSet('nodetemp2',serialize($redisArray));
                        $redis->RedisSet('nodenow2', $coinbaseAddr);
                    }
                }
                else {
                    $tempArray = unserialize($redis->RedisGet('nodetemp2'));
                    $addr1 = $redis->RedisGet('nodenow2');

                    $redis->RedisSet('nodenow2',$coinbaseAddr);

                    $first = $tempArray[$addr1];
                    $second = $tempArray[$coinbaseAddr];
                    $flag = $second - $first;

                    $count = $redis->RedisHGet($second,'count');
                    $sum = $redis->RedisHGet($second,'sum');

                    if($flag == 1){
                        $redis->RedisHSet($first,'now',0);
                        $redis->RedisHSet($second,'now',1);
                        $redis->RedisHSet($second,'status',1);
                        $redis->RedisHSet($second,'count',$count+1);
                        $redis->RedisHSet($second,'sum',$sum+1);
                    }else{
                        for($i=$first+1;$i<$second;$i++){
                            $iSum = $redis->RedisHGet($i,'sum');
                            $redis->RedisHSet($i,'status',-1);
                            $redis->RedisHSet($i,'sum',$iSum+1);
                        }
                        $redis->RedisHSet($first,'now',0);
                        $redis->RedisHSet($second,'now',1);
                        $redis->RedisHSet($second,'status',1);
                        $redis->RedisHSet($second,'count',$count+1);
                        $redis->RedisHSet($second,'sum',$sum+1);
                    }
                }
            }else{
                return json_encode(array('error'=>1,'msg'=>'block waiting index...'));
            }
        }else{
            $redis->RedisSet('blockcountnew',$blockInt);
            return json_encode(array('error'=>1,'msg'=>'redis adding...'));
        }
    }


    //NewGetNode
    public function NewGetNode()
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        $superNode = '166D9UoFdPcDEGFngswE226zigS8uBnm3C';

        $redis = new RedisOperate();

        $voteInfo = file_get_contents('http://172.31.239.84/getactive');
        $voteInfoArray = json_decode($voteInfo,1);

        $temp1 = array();
        foreach ($voteInfoArray as $tKey => $tVal){
            $temp1[$tVal['address']]['address'] =  $tVal['address'];
            $temp1[$tVal['address']]['name'] =  $tVal['name'];
            $temp1[$tVal['address']]['count'] =  $tVal['count'];
        }

        $mysqlRes = DB::table('nodeinfosave')
            ->select('s_addr','s_count','s_sum')
            ->get();

        foreach ($mysqlRes as $mVal){
            $mAddr = $mVal->s_addr;
            $temp1[$mAddr]['ratio'] = round($mVal->s_count/$mVal->s_sum,4);
        }

        $temp1[$superNode]['address'] = $superNode;
        $temp1[$superNode]['name'] = 'LBTCSuperNode';
        $temp1[$superNode]['count'] = 2100000000000000;

        $tempArray = unserialize($redis->RedisGet('nodetemp2'));

        $temp2 = array();
        $addrArray = array();
        foreach ($tempArray as $val){
            $res = $redis->RedisHVals($val);
            $addr = $res[0];
            $temp2[$addr]['address'] = $addr;
            $temp2[$addr]['status'] = $res[1];
            $temp2[$addr]['now'] = $res[2];
            $temp2[$addr]['ratio'] = round($res[3]/$res[4],4);
            $lastAddr = $addr;
            array_push($addrArray,$addr);
        }


        foreach ($addrArray as $aKey => $aVal){
            if($temp2[$aVal]['now'] === '1' && $temp2[$aVal]['address'] != $lastAddr){
                $temp2[$aVal]['now'] = 0;
                $temp2[$addrArray[$aKey+1]]['now'] = 1;
            }
            $temp2[$aVal]['name'] = $temp1[$aVal]['name'];
            $temp2[$aVal]['count'] = $temp1[$aVal]['count'];
            unset($temp1[$aVal]);
        }
        $resArray = array_merge($temp2,$temp1);
        return json_encode(array('error' => 0,'msg' => array_values($resArray)));
    }

    
    //GetTxByAddr
    public function GetTxByAddr(Request $request)
    {
        header("Access-Control-Allow-Origin:*");
        header("Access-Control-Allow-Methods:GET,POST");

        if (empty($request->input('param'))){
            return json_encode(array('error'=>1,'msg'=>'Input Addr Empty!'));
        }

        $param = trim($request->input('param'));
        $addrLen = strlen($param);
        if($addrLen < 26 || $addrLen > 34){
            return json_encode(array('error'=>1,'msg'=>'Addr Format Error!'));
        }

        $curlOperate = new CurlOperate();
        $redis = new RedisOperate();

        $timeOut = 15;

        $redisParam = 'tx-'.$param;
        $redisTx = $redis->RedisGet($redisParam);

        if($redisTx){
            $txArray = json_decode($redisTx,1);
            return json_encode(array('error'=>0,'msg'=>$txArray));
        }else{
            $response = $curlOperate->GetTxByAddr($param);
            $txRes = json_decode($response,1);

            if(array_key_exists('msg',$txRes)){
                return json_encode(array('error'=>1,'msg'=>$txRes['msg']));
            }

            $txArray = $txRes['result'];

            if(empty($txArray)){
                return json_encode(array('error'=>1,'msg'=>'Transaction information can not be found!'));
            }

            $txJson = json_encode($txArray);
            $redis->RedisSet($redisParam,$txJson,$timeOut);
            return json_encode(array('error'=>0,'msg'=>$txArray));
        }
    }
}
