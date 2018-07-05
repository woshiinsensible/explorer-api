<?php

$app->group(['namespace' => 'App\Http\Controllers\Block'], function() use ($app)
{
    $app->get('listwitnesses', 'BlockController@ListWitnesses');

    $app->get('getvotersbywitness', 'BlockController@GetVotersByWitness');

    $app->get('getvotebyaddress', 'BlockController@GetVoteByAddress');

    $app->get('getwitnessshare', 'BlockController@GetWitnessshare');

    $app->get('setredis', 'BlockController@SetRedis');

    $app->get('getactive', 'BlockController@GetActive');

    $app->get('getstandby', 'BlockController@GetStandby');

    $app->get('getlistdelegates', 'BlockController@GetListDelegates');

    $app->get('search', 'BlockController@Search');

    $app->get('getaddressbalance', 'BlockController@GetAddressBalance');

    $app->get('getblockinfo', 'BlockController@GetBlockInfo');

    $app->get('gettxinfo', 'BlockController@GetTxInfo');

    $app->get('index', 'BlockController@Index');

    $app->get('setindex', 'BlockController@SetIndex');

    $app->get('getblockbyhash', 'BlockController@GetBlockInfoByHash');

    $app->get('gettxbyaddr', 'BlockController@GetTxByAddr');

    $app->get('setblockcount', 'BlockController@SetBlockCount');

    $app->get('getblockcount', 'BlockController@GetBlockCount');

    $app->get('newsetnode','BlockController@NewSetNode');

    $app->get('newgetnode','BlockController@NewGetNode');

});
