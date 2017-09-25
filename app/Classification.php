<?php

namespace Microbiome;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $table = 'classification';

    public function getFullPath()
    {
        $path = [];
        $pid = $this->getAttribute('pid');
        while ($pid !== 0){
            $p = Classification::find($pid);
            $path[] = $p;
            $pid = $p->pid;
        }
        array_reverse($path);
        $this->getAttribute();
        return $path;
    }
}
