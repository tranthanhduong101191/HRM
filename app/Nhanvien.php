<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Nhanvien extends Model
{
    protected $connection   =   'sqlsrv';
    protected $table    =   'NHANVIEN';
    public $timestamps    =   false;
}
