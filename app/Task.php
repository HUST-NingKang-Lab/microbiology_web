<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $table = 'task';
    protected $hidden = ['id','input_file','output_file'];
}
