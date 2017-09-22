<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    protected $table = 'run';
    protected $visible = ['run_accession','experiment','model','release_date'];
}
