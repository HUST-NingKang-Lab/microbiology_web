<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sample extends Model
{
    protected $table = 'sample';
    protected $hidden = ['id', 'updated_at', 'created_at'];

    protected $appends = ['runs','project'];

    public function getRunsAttribute()
    {
        $res = $this->hasMany('Microbiome\SampleRun','sample','SRA_Accession')->get();
        $runs = [];
        foreach ($res as $r){
            $runs[] = $r->run;
        }
        $runs = Run::whereIn('run_accession',$runs)->get();
        return $this->attributes['runs'] = $runs;
    }

    public function getBriefInfo(){
        $meta_info = json_decode($this->getAttribute('meta_info'));
        if (empty($meta_info->describe)){
            $meta_info = 'sample description';
        }else{
            $meta_info = $meta_info->describe;
        }
        $accession = $this->getAttribute('SRA_Accession');
        return [
            'briefIntro'=>$meta_info,
            'SRA_Accession'=>$accession
        ];
    }

    public function getProjectAttribute()
    {
        $sample = $this->attributes['SRA_Accession'];
        $project = ProjectSample::where('sample',$sample)->get()[0]->project;
        $project = Project::where('NCBI_Accession',$project)->get()[0]->object_id;
        return $this->attributes['project'] = $project;
    }
}