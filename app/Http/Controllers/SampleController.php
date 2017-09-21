<?php

namespace microbiome\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use microbiome\Project;
use microbiome\Sample;


class SampleController extends Controller
{
    public function getSampleList(Request $request)
    {
        $request->validate([
            'project_id'=>'required'
        ]);
        $project = Project::where('object_id',$request->input('project_id'))->get();
        if (count($project) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid id for project']);
        }
        $NCBI_Accession = $project[0]->NCBI_Accession;

        $res = DB::table('project_sample')
            ->where('project',$NCBI_Accession)
            ->select(DB::raw('sample as SRA_Accession'))
            ->get();
        $res_array['SRA_Accessions'] = [];
        foreach ($res as $r){
            $res_array['SRA_Accessions'][] = $r->SRA_Accession;
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $res_array]);

    }

    public function getSampleInfo(Request $request)
    {
        $request->validate([
            'SRA_Accession'=>'required'
        ]);
        $sample = Sample::where('SRA_Accession',$request->input('SRA_Accession'))->get();
        if (count($sample) !== 1){
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid SRA_Accession for sample']);
        }
        $sample[0]->meta_info = json_decode($sample[0]->meta_info);
        return JsonResponse::create(['error_code' => 0, 'data' => $sample[0]]);
    }
}
