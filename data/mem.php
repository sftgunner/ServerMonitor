<?php
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
    if ($mem[1] == 0){
        echo 0;
    }
    else{
        $memory_usage = $mem[2]/$mem[1]*100;
        
        echo $memory_usage;
    }
?>
