<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;


$worker = new Worker();
$worker->count = 1;
$worker->onWorkerStart = function($worker)
{
    echo "Worker starting...\n";
    $redis = new Redis();
    $redis->pconnect('127.0.0.1', 6379);

    function f($redis, $chan, $msg) {
        $msg_obj = json_decode($msg);
        $input = realpath('../storage/app/'.$msg_obj->input_file);
        $task_id = $msg_obj->task_id;
        $output = '../storage/app/taxonomyAnalysis/output/'.$task_id;

        $mysql = mysqli_connect('localhost:3306','root','root','microbiome');
        $sql = "UPDATE task SET `status` = 'processing' WHERE task_id = '$task_id'";

        $cmd = 'PM-parallel-meta -r '.$input.' -f F -o '.$output;
        system($cmd);

        $sql = "UPDATE task SET `status` = 'done' WHERE task_id = '$task_id'";
        $mysql->query($sql);
        $mysql->close();
    }
    $redis->subscribe(array('taxonomyAnalysis'), 'f'); // subscribe to 3 chans

    // 每2.5秒执行一次
    $time_interval = 2.5;
    Timer::add($time_interval, function($redis,$mysql)
    {
        $redis->ping();
    });
};
// 运行worker
Worker::runAll();

