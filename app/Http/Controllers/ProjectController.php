<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Microbiome\Project;

class ProjectController extends Controller
{
    public function getProjectList(Request $request)
    {
        $pageSize = $request->input('pageSize', 20);
        $currentPage = $request->input('currentPage', 1);
        $res = Project::offset($pageSize * ($currentPage - 1))
            ->limit($pageSize)
//            ->join('classification', 'projec.author_id', '=', 'author.object_id')
//            ->leftJoin('post_favorite','post.object_id', '=', 'post_favorite.post_id')
//            ->select('post.*', 'author.name as author',(DB::raw("COUNT( DISTINCT post_favorite.id) AS favorite")))
//            ->groupBy('post.object_id')
            ->get();
        return JsonResponse::create(['error_code' => 0, 'data' => $res]);
    }

    public function getProjectInfo(Request $request)
    {
        $object_id = $request->input('object_id');
        $p = Project::where('object_id', $object_id)->first();
        if ($p) {
            return JsonResponse::create(['error_code' => 0, 'data' => $p]);
        } else {
            return JsonResponse::create(['error_code' => 1, 'error_message' => 'invalid id for project']);
        }
    }

    public function getProjectNum()
    {
        $count['total_project_num'] = Project::all()->count();
        return JsonResponse::create(['error_code' => 0, 'data' => $count]);
    }
}
