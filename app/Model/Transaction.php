<?php
namespace  App\Model;

use Illuminate\Database\Eloquent\Model;


class Transaction extends  Model
{
    protected  $table = 'tx';

    protected  $primaryKey = 'tx_id';

    public $timestamps = false;

}