<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Giaitrinh extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chamcong()
    {
        return $this->belongsTo(Chamcong::class);
    }
}
