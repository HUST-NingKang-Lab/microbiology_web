<?php

namespace microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use microbiome\Run;
use Psy\Exception\RuntimeException;

class RunController extends Controller
{
    public function getRunList(Request $request)
    {
        $request->validate([
            'SRA_Accession' => 'required'
        ]);
        $res = DB::table('sample_run')
            ->where('sample', $request->input('SRA_Accession'))
            ->get();
        $res_array['Run_Accessions'] = [];
        foreach ($res as $r) {
            $res_array['Run_Accessions'][] = $r->run;
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $res_array]);
    }

    public function getRunInfo(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('Run', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $run[0]]);
    }

    public function getRunAnalysis(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('Run', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }
    }
}
