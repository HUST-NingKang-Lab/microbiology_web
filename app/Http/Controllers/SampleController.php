<?php

namespace Microbiome\Http\Controllers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Microbiome\Sample as Sample;


class SampleController extends Controller
{
    public function getSampleList(Request $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $currentPage = $request->input('currentPage', 1);
        $samples = Sample::offset($pageSize * ($currentPage - 1))
            ->limit($pageSize)
            ->get();
        foreach ($samples as &$sample){
            $sample->meta_info = json_decode($sample->meta_info);
            $sample->briefIntro = $sample->meta_info->describe;
        }
        $res['pageSize'] = $pageSize;
        $res['currentPage'] = $currentPage;
        $res['samples'] = $samples;
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);;
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

    public function getTotalNumberOfSamples()
    {
        $res['totalNumberOfSamples'] = Sample::all()->count();
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }
}
