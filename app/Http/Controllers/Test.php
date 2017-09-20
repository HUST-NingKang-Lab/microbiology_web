<?php

namespace microbiome\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use microbiome\Project;

class Test extends Controller
{
    public function test(){
        $res = DB::table('project')
                ->get();
        foreach ($res as $r){
            $p = new Project();
            $p->setRawAttributes([
                'NCBI_Accession'=>$r->NCBI_Accession,
                'name'=>$r->name,
                'subname'=>$r->subname,
                'type'=>$r->type,
                'description'=>$r->description,
                'object_id'=>$this->uniqidReal(),
                ]);
//            var_dump($r->getAttributes());
            $p->save();
        }
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
