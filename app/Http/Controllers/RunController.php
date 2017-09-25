<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Microbiome\Project;
use Microbiome\Run;
use Psy\Exception\RuntimeException;
use Sunra\PhpSimple\HtmlDomParser;

class RunController extends Controller
{
    public function getRunList(Request $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $currentPage = $request->input('currentPage', 1);
        $runs = Run::offset($pageSize * ($currentPage - 1))
            ->limit($pageSize)
            ->get();
        $res['pageSize'] = $pageSize;
        $res['currentPage'] = $currentPage;
        $res['runs'] = $runs;
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }

    public function getRunInfo(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('run_accession', $request->input('Run_Accession'))->get();
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
        $run = Run::where('run_accession', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }

        $Run_Accession = $request->input('Run_Accession');
        $path = '/home/microbiome_web/analysis_output/run/' . $Run_Accession;
        $QC = $path . '/NGSQC_out/output_' . $Run_Accession . '.fastq.html';
        if (!file_exists($QC)){
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'No QC data available']);
        }
        $html = HtmlDomParser::file_get_html($QC);
        $table = $html->find('table', 4);
        $table_array = [];
        foreach ($table->children() as $c) {
            $table_array[$c->children(0)->plaintext] = $c->children(1)->plaintext;
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $table_array]);
    }

    public function getRunTaxonomy(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('run_accession', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }
        $Run_Accession = $request->input('Run_Accession');
        $path = '/home/microbiome_web/analysis_output/run/' . $Run_Accession . '/Result/';
        $taxa = $path . 'taxonomy.html';
        if (!file_exists($taxa)){
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'No taxa data available']);
        }
        return file_get_contents($taxa);
    }

    public function getRunGO(Request $request)
    {
        $request->validate([
            'Run_Accession' => 'required'
        ]);
        $run = Run::where('run_accession', $request->input('Run_Accession'))->get();
        if (count($run) !== 1) {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid Run_Accession']);
        }
        $Run_Accession = $request->input('Run_Accession');
        $path = '/home/microbiome_web/analysis_output/run/' . $Run_Accession . '/';
        $GO = $path.$Run_Accession.'_goslim_countgo_com.csv';
        if (!file_exists($GO)){
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'No GO data available']);
        }
//        $str = file_get_contents($GO);
        $file = fopen($GO,'r');
        $res = [];
        while (($line = fgets($file)) !== false){
            $line = trim($line);
            $line = str_replace('"','',$line);
            $items = explode(',',$line);
            $res[$items[2]][$items[1]] = (int)($items[3]);
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }

    public function getRunResults(Request $request)
    {
        $GO = $this->getRunGO($request)->original;
        $taxa = $this->getRunTaxonomy($request)->original;
        $QC = $this->getRunQC($request)->original;
        return ['GO'=>$GO,'QC'=>$QC,'Taxa'=>$taxa];
    }

    public function getTotalNumberOfRuns()
    {
        $res['totalNumberOfRuns'] = Run::all()->count();
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }
}
