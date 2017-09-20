<?php

namespace microbiome;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'project';
    protected $hidden = ['id','updated_at','created_at'];
//    protected $primaryKey = '';
}
