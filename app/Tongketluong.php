<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tongketluong extends Model
{
    protected $table = 'tongketluong';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
