<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    //
    protected $guarded = [];
    public function account(){

        return $this->belongsTo(Account::class);
    }

    public function user(){
        
        return $this->belongsTo(User::class);
    }
}
