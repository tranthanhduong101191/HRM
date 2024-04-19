<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chamcong extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function giaitrinh()
    {
        return $this->hasMany(Giaitrinh::class);
    }
}
