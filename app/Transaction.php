<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];


    public function account(){

        return $this->belongsTo(Account::class);
    }

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function reciever(){

        return $this->belongsTo(User::class, 'transfer_to');
    }

}
