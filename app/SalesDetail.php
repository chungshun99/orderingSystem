<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    public function sales() {
        return $this -> belongsTo(Sales::class);
    }
}
