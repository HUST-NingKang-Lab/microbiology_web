<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sample extends Model
{
    protected $table = 'sample';
    protected $hidden = ['id', 'updated_at', 'created_at'];

    protected $appends = ['runs'];

    public function getRunsAttribute()
    {
        $res = $this->hasMany('Microbiome\Sample_run','sample','SRA_Accession')->get();
        $runs = [];
        foreach ($res as $r){
            $runs[] = $r->run;
        }
        $runs = Run::whereIn('run_accession',$runs)->get();
        return $this->attributes['runs'] = $runs;
    }

    public function getBriefInfo(){
        $meta_info = json_decode($this->getAttribute('meta_info'))->describe;
        $accession = $this->getAttribute('SRA_Accession');
        return [
            'briefIntro'=>$meta_info,
            'SRA_Accession'=>$accession
        ];
    }
}

class Sample_run extends Model{
    protected $table = 'sample_run';
}