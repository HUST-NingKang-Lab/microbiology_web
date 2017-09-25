<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    protected $table = 'run';
    protected $visible = ['run_accession','experiment','model','release_date','sample','project'];
    protected $appends = ['sample','project'];

    public function getSampleAttribute()
    {
        $run_accession = $this->attributes['run_accession'];
        $sample = SampleRun::where('run',$run_accession)->get()[0]->sample;
        return $this->attributes['sample'] = $sample;
    }

    public function getProjectAttribute()
    {
        $sample = $this->attributes['sample'];
        $project = ProjectSample::where('sample',$sample)->get()[0]->project;
        $project = Project::where('NCBI_Accession',$project)->get()[0]->object_id;
        return $this->attributes['project'] = $project;
    }
}