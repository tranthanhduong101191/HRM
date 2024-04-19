<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thuongphat extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function issue_user()
    {
        return $this->belongsTo(User::class, 'issue_user_id');
    }
}
