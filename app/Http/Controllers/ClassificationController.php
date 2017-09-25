<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\Request;
use Microbiome\Classification;
use Microbiome\Project;

class ClassificationController extends Controller
{
    public function getBiomes()
    {
        $biomes = Classification::all();
        $add = [];
        foreach ($biomes as &$b){
            $b->projects = Project::where('classification_id',$b->id)->count();
            $pid = $b->pid;
            while ($pid !== 0){
                if (empty($add["$pid"])) {
                    $add["$pid"] = 0;
                }
                $add["$pid"] += $b->projects;
                $pid = Classification::where('id',$pid)->get();
                if (count($pid)>0){
                    $pid = $pid[0]->pid;
//                    return $pid;
                }else{
                    $pid = 0;
                }
            }
        }
        foreach ($biomes as &$b){
//            $pid =
            if (!empty($add["$b->pid"])){
                $b->projects += $add["$b->pid"];
            }
        }
        return $biomes;
    }
}
