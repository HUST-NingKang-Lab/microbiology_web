<?php

namespace Microbiome\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SearchController extends Controller
{
    public function metaStormsSearch(Request $request)
    {
        Redis::publish('cd', json_encode(['foo' => 'bar']));
    }

    public function taxonomy()
    {

    }
}
