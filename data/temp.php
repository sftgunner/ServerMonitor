<?php

        //Function requires the package lm-sensors.
        $checkinstalled = shell_exec('dpkg -s lm-sensors | grep -o "Status: .*"');
        if (substr($checkinstalled, 0, -1) == 'Status: install ok installed'){
            echo shell_exec('sensors | grep -o "id 0:  +.*" | cut -f2- -d+ | cut -b-4');
        }
        else{
            echo 'lm-sensors not installed: error '.$checkinstalled;
        }
    ?>
