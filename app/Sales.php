<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    public function saleDetails() {
        return $this -> hasMany(SalesDetail::class);
    }
}
