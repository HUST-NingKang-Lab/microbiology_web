<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Microbiome\Project;
use Microbiome\Run;
use Psy\Exception\RuntimeException;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'No GO data available'.$GO]);
        }
//        $str = file_get_contents($GO);
        $file = fopen($GO,'r');
        $res = [];
        while (($line = fgets($file)) !== false){
            $line = trim($line);
            $line = str_replace('"','',$line);
            $items = explode(',',$line);
//            if (in_array($items[1],[''])){
//                continue;
//            }
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

    public function getGOHeatMap(Request $request)
    {
        $request->validate([
            'runs'=>'required'
        ]);
        $runs = $request->input('runs');
        foreach ($runs as $run)
        {
            $path = '/home/microbiome_web/analysis_output/run/' . $run . '/';
            $GO = $path.$run.'_goslim_countgo_com.csv';
            if (!file_exists($GO)){
                return JsonResponse::create(['error_code' => 1, 'error_message' => 'No GO data available for '.$run]);
            }
        }
        //合并GO数据
        $res = [];
        $GOs = '';
        foreach ($runs as $run){
            $path = '/home/microbiome_web/analysis_output/run/' . $run . '/';
            $GO = $path.$run.'_goslim_countgo_com.csv';
            $file = fopen($GO,'r');
            while (($line = fgets($file)) !== false){
                $line = trim($line);
                $line = str_replace('"','',$line);
                $items = explode(',',$line);
                $res[$run][$items[2]][$items[1]] = (int)($items[3]);
            }
            $GOs .= $run;
        }

        $biological_process = [];
        $biological_process_terms = [];
        $molecular_function = [];
        $molecular_function_terms = [];
        $cellular_component = [];
        $cellular_component_terms = [];

        foreach ($res as $k=>$v){
            $run = $k;
            foreach ($res[$run] as $GO_root_term=>$GO_term){
//                echo $GO_root_term;
                if ($GO_root_term === 'biological_process'){
                    $biological_process[$run] = $res[$run]['biological_process'];
                    $biological_process_terms = array_merge($biological_process_terms,array_keys($res[$run]['biological_process']));
                }
                if ($GO_root_term === 'molecular_function'){
                    $molecular_function[$run] = $res[$run]['molecular_function'];
                    $molecular_function_terms = array_merge($molecular_function_terms,array_keys($res[$run]['molecular_function']));
                }
                if ($GO_root_term === 'cellular_component'){
                    $cellular_component[$run] = $res[$run]['cellular_component'];
                    $cellular_component_terms = array_merge($cellular_component_terms,array_keys($res[$run]['cellular_component']));
                }
            }
        }
        //写入文件
        $biological_process_terms = array_unique($biological_process_terms);
        $molecular_function_terms = array_unique($molecular_function_terms);
        $cellular_component_terms =array_unique($cellular_component_terms);
        $GOs = md5($GOs);

        $biological_process_file = "../storage/app/public/$GOs.BP.csv";
        $f = fopen($biological_process_file,'w');
        $runs = array_keys($res);
        $line =',';
        foreach ($runs as $k){//写入header
            $line .= $k.",";
        }
        fwrite($f,rtrim($line,',')."\n");

        foreach ($biological_process_terms as $term){
            $line = "$term,";
            foreach ($runs as $run){
                $line .= empty($biological_process[$run][$term])?'0,':$biological_process[$run][$term].',';
            }
            fwrite($f,rtrim($line,',')."\n");
        }
        fclose($f);
        $cmd = "Rscript ../woker/GOheatmap.R $biological_process_file $biological_process_file.png";
        $process = new Process($cmd);
        $process->run();

        //22222
        $molecular_function_file = "../storage/app/public/$GOs.MF.csv";
        $f = fopen($molecular_function_file,'w');
        $line =',';
        foreach ($runs as $k){//写入header
            $line .= $k.",";
        }
        fwrite($f,rtrim($line,',')."\n");
        foreach ($molecular_function_terms as $term){
            $line = "$term,";
            foreach ($runs as $run){
                $line .= empty($molecular_function[$run][$term])?'0,':$molecular_function[$run][$term].',';
            }
            fwrite($f,rtrim($line,',')."\n");
        }
        fclose($f);
        $cmd = "Rscript ../woker/GOheatmap.R $molecular_function_file $molecular_function_file.png";
        $process = new Process($cmd);
        $process->run();

        //33333
        $cellular_component_file = "../storage/app/public/$GOs.CC.csv";
        $f = fopen($cellular_component_file,'w');
        $line =',';
        foreach ($runs as $k){//写入header
            $line .= $k.",";
        }
        fwrite($f,rtrim($line,',')."\n");
        foreach ($cellular_component_terms as $term){
            $line = "$term,";
            foreach ($runs as $run){
                $line .= empty($cellular_component[$run][$term])?'0,':$cellular_component[$run][$term].',';
            }
            fwrite($f,rtrim($line,',')."\n");
        }
        fclose($f);
        $cmd = "Rscript ../woker/GOheatmap.R $cellular_component_file $cellular_component_file.png";
        $process = new Process($cmd);
        $process->run();
        unset($res);
        $res = [];
        $res['cellular_component'] = "/storage/$GOs.CC.csv.png";
        $res['molecular_function'] = "/storage/$GOs.MF.csv.png";
        $res['biological_process'] = "/storage/$GOs.BP.csv.png";
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);

    }
    public function getGOOfRuns(Request $request)
    {
        $request->validate([
            'runs'=>'required'
        ]);
        $runs = $request->input('runs');
        foreach ($runs as $run)
        {
            $path = '/home/microbiome_web/analysis_output/run/' . $run . '/';
            $GO = $path.$run.'_goslim_countgo_com.csv';
            if (!file_exists($GO)){
                return JsonResponse::create(['error_code' => 1, 'error_message' => 'No GO data available for '.$run]);
            }
        }

        $res = [];
        foreach ($runs as $run){
            $path = '/home/microbiome_web/analysis_output/run/' . $run . '/';
            $GO = $path.$run.'_goslim_countgo_com.csv';
            $file = fopen($GO,'r');
            while (($line = fgets($file)) !== false){
                $line = trim($line);
                $line = str_replace('"','',$line);
                $items = explode(',',$line);
                $res[$run][$items[2]][$items[1]] = (int)($items[3]);
            }
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }

    public function getRunsWithGO(Request $request)
    {
        $object_id = $request->input('object_id');
        $project = Project::where('object_id',$object_id)->get()[0];
        $runs = $project->runs;
        foreach ($runs as $k=>$v){
            $path = '/home/microbiome_web/analysis_output/run/' . $v->run_accession . '/';
            $GO = $path.$v->run_accession.'_goslim_countgo_com.csv';
            if (!file_exists($GO)){
//                return JsonResponse::create(['error_code' => 1, 'error_message' => 'No GO data available for '.$run]);
                unset($runs[$k]);
            }
        }
        $res = [];
        foreach ($runs as $run){
            $res[] = $run;
        }
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
        return $res;
    }

}
