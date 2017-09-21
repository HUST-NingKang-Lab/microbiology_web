<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    protected $table = 'project';
    protected $hidden = ['id','updated_at','created_at','classification_id'];
    protected $appends = ['sample_num','biome','samples'];
//    protected $primaryKey = '';

    public function getSamplesAttribute()
    {
        $samples = $this->hasMany('Microbiome\project_sample','project','NCBI_Accession')->get();
        foreach ($samples as $s)
        {
            $this->attributes['samples'][] = $s->sample;
        }
        return $this->attributes['samples'];
    }

    public function getSampleNumAttribute()
    {
        $samples = $this->hasMany('Microbiome\project_sample','project','NCBI_Accession')->get();
        return $this->attributes['sample_num'] = count($samples);
    }

    public function classification()
    {
        return $this->hasOne('Microbiome\Classification','id','classification_id');
    }

    public function getBiomeAttribute()
    {
        $c = $this->hasOne('Microbiome\Classification','id','classification_id')->get();
        return $this->attributes['classification'] = $c;
    }

    public static function getList($currentPage=1, $pageSize=20)
    {
//        $projects = Project::offset($pageSize * ($currentPage - 1))
//            ->limit($pageSize)
//            join('project_sample', 'project.NCBI_Accession', '=', 'project_sample.project')
//            ->get();

//        foreach ($projects as $p){
//            echo $p->sample_num;
//        }
//        return $projects;
    }




}

class Project_Sample extends Model
{
    protected $table = 'project_sample';
}