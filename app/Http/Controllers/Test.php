<?php

namespace microbiome\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use microbiome\Project;
use microbiome\Sample;

class Test extends Controller
{
    public function test(Request $request){
//        $res = DB::table('project')
//                ->get();
//        foreach ($res as $r){
//            $p = new Project();
//            $p->setRawAttributes([
//                'NCBI_Accession'=>$r->NCBI_Accession,
//                'name'=>$r->name,
//                'subname'=>$r->subname,
//                'type'=>$r->type,
//                'description'=>$r->description,
//                'object_id'=>$this->uniqidReal(),
//                ]);
////            var_dump($r->getAttributes());
//            $p->save();
//        }

//        $res = DB::table('sample')
//            ->get();
//        foreach ($res as $r) {
//            $s = new Sample();
//            $s->setRawAttributes([
//                'SRA_Accession'=>$r->sample,
//                'meta_info'=>$r->meta,
//                'object_id'=>$this->uniqidReal(),
//            ]);
//            $s->save();
//        }
        $id = $request->input('id');
        $path = '/home/microbiome_web/analysis_output/run/'.$id;
        $QC = $path.'/NGSQC_out/output_'.$id.'.fastq.html';
        
//        preg_match('/QC statistics./',file_get_contents($QC),$matches);
        return file_get_contents($QC);


    }

    function uniqidReal($lenght = 13) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
}
