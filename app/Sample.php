<?php

namespace microbiome;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $table = 'sample';
    protected $hidden = ['id','updated_at','created_at'];

}
