<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Microbiome\Run;
use Psy\Exception\RuntimeException;
use Sunra\PhpSimple\HtmlDomParser;

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

    public function getRunQC(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('Run', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }

        $Run_Accession = $request->input('Run_Accession');
        $path = '/home/microbiome_web/analysis_output/run/'.$Run_Accession;
        $QC = $path.'/NGSQC_out/output_'.$Run_Accession.'.fastq.html';
        $html = HtmlDomParser::file_get_html($QC);
        $table = $html->find('table',4);
        $table_array = [];
        foreach ($table->children() as $c){
            $table_array[$c->children(0)->plaintext] = $c->children(1)->plaintext;
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $table_array]);
    }

    public function getRunTaxonomy(){

    }

    public function getGO()
    {

    }
}
