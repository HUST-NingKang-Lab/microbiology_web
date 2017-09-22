<?php

namespace Microbiome\Http\Controllers;

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
//        return Storage::size($path);
        $task = new Task();
        $task->setRawAttributes([
            'input_file'=>$path,
            'task_id'=>Storage::get($path)
        ]);
        $task->save();
    }
}
