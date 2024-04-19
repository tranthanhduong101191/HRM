<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkinout extends Model
{
    protected $connection   =   'sqlsrv';
    protected $table    =   'CheckInOut';
    public $timestamps    =   false;

    public function user()
    {
        return $this->belongsTo(User::class, 'MaChamCong', 'uid');
    }

    public function maychamcong()
    {
        return $this->belongsTo(Maychamcong::class, 'MaSoMay', 'IDMCC');
    }
}
