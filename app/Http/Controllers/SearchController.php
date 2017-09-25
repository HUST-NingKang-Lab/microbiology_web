<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Microbiome\Task;

class SearchController extends Controller
{
    public function metaStormsSearch(Request $request)
    {
        Redis::publish('cd', json_encode(['foo' => 'bar']));
    }

    public function taxonomyAnalysis(Request $request)
    {
        $path = $request->file('seq_file')->store('taxonomyAnalysis');
        $task = new Task();
        $md5 = md5(Storage::get($path) . microtime());
        $task->setRawAttributes([
            'input_file' => $path,
            'task_id' => $md5,
            'status' => 'queuing',
            'type' => 'taxonomyAnalysis'
        ]);
        $task->save();

        Redis::publish('taxonomyAnalysis', json_encode($task));

        $res['task_id'] = $md5;
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }

    public function getTaskStatus(Request $request)
    {
        $request->validate([
            'task_id'=>['required']
        ]);

        $task = Task::where('task_id',$request->task_id)
            ->get();
        return JsonResponse::create(['error_code' => 0, 'data' => $task]);
    }

    public function downloadTaxonomyAnalysis(Request $request)
    {
        $request->validate([
            'task_id'=>['required']
        ]);
        $task_id = $request->task_id;
        $path = "../storage/app/taxonomyAnalysis/output/$task_id";
        $html = "../storage/app/taxonomyAnalysis/output/$task_id/taxonomy.html";
        if (file_exists($html))
        {
            $zip = new \ZipArchive;
            $zip->open("./download/$task_id.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $zip->addGlob("$path/*",0,array('remove_all_path' => TRUE));
            $zip->close();
            return redirect("./download/$task_id.zip");
        }else{
            return 'output unavailable';
        }
    }


}
