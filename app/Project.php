<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Microbiome\Run;
use Microbiome\Sample;

class Project extends Model
{
    protected $table = 'project';
    protected $hidden = ['id', 'updated_at', 'created_at', 'classification_id'];
    protected $appends = ['sample_num', 'biome', 'samples', 'runs'];

//    protected $primaryKey = '';

    public function getSamplesAttribute()
    {
        $samples = $this->hasMany('Microbiome\project_sample', 'project', 'NCBI_Accession')->get();
        foreach ($samples as $s) {
            $this->attributes['samples'][] = $s->sample;
        }
        return $this->attributes['samples'];
    }

    public function getSampleNumAttribute()
    {
        $samples = $this->hasMany('Microbiome\project_sample', 'project', 'NCBI_Accession')->get();
        return $this->attributes['sample_num'] = count($samples);
    }

    public function classification()
    {
        return $this->hasOne('Microbiome\Classification', 'id', 'classification_id');
    }

    public function getBiomeAttribute()
    {
        $c = $this->hasOne('Microbiome\Classification', 'id', 'classification_id')->get();
        return $this->attributes['classification'] = $c;
    }


    public function getRunsAttribute()
    {
        $samples = $this->attributes['samples'];
        $res = DB::table('sample_run')
            ->whereIn('sample', $samples)
            ->select('run')
            ->get();
        $runs = [];
        foreach ($res as $r) {
            $runs[] = $r->run;
        }
        $us = Run::whereIn('run_accession',$runs)
            ->get();
        return $this->attributes['runs'] = $us;
    }

}

class Project_Sample extends Model
{
    protected $table = 'project_sample';
}