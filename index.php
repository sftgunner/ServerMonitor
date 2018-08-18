<html>
<head>
<title>
Server Status
</title>
</head>
<body>
<?php
    function get_server_memory_usage(){
        
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2]/$mem[1]*100;
        
        return $memory_usage;
    }
    function get_server_cpu_usage(){
        
        $load = sys_getloadavg();
        return $load[0];
        
    }
    function system_load($coreCount = 2, $interval = 1) {
        $rs = sys_getloadavg();
        $interval = $interval >= 1 && 3 <= $interval ? $interval : 1;
        $load = $rs[$interval];
        return round(($load * 100) / $coreCount,2);
    }
    function system_cores() {
        
        $cmd = "uname";
        $OS = strtolower(trim(shell_exec($cmd)));
        
        switch($OS) {
            case('linux'):
                $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
                break;
            case('freebsd'):
                $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
                break;
            default:
                unset($cmd);
        }
        
        if ($cmd != '') {
            $cpuCoreNo = intval(trim(shell_exec($cmd)));
        }
        
        return empty($cpuCoreNo) ? 1 : $cpuCoreNo;
        
    }
    function http_connections() {
        
        if (function_exists('exec')) {
            
            $www_total_count = 0;
            @exec ('netstat -an | egrep \':80|:443\' | awk \'{print $5}\' | grep -v \':::\*\' |  grep -v \'0.0.0.0\'', $results);
            
            foreach ($results as $result) {
                $array = explode(':', $result);
                $www_total_count ++;
                
                if (preg_match('/^::/', $result)) {
                    $ipaddr = $array[3];
                } else {
                    $ipaddr = $array[0];
                }
                
                if (!in_array($ipaddr, $unique)) {
                    $unique[] = $ipaddr;
                    $www_unique_count ++;
                }
            }
            
            unset ($results);
            
            return count($unique);
            
        }
        
    }
    
    function server_memory_usage() {
        
        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = $mem[2] / $mem[1] * 100;
        
        return $memory_usage;
        
    }
    
    function disk_usage() {
        
        $disktotal = disk_total_space ('/');
        $diskfree  = disk_free_space  ('/');
        $diskuse   = round (100 - (($diskfree / $disktotal) * 100)) .'%';
        
        return $diskuse;
        
    }
    function server_uptime() {
        
        $uptime = floor(preg_replace ('/\.[0-9]+/', '', file_get_contents('/proc/uptime')) / 86400);
        
        return $uptime;
        
    }
    function kernel_version() {
        
        $kernel = explode(' ', file_get_contents('/proc/version'));
        $kernel = $kernel[2];
        
        return $kernel;
        
    }
    function number_processes() {
        
        $proc_count = 0;
        $dh = opendir('/proc');
        
        while ($dir = readdir($dh)) {
            if (is_dir('/proc/' . $dir)) {
                if (preg_match('/^[0-9]+$/', $dir)) {
                    $proc_count ++;
                }
            }
        }
        
        return $proc_count;
        
    }
    function memory_usage() {
        
        $mem = memory_get_usage(true);
        
        if ($mem < 1024) {
            
            $$memory = $mem .' B';
            
        } elseif ($mem < 1048576) {
            
            $memory = round($mem / 1024, 2) .' KB';
            
        } else {
            
            $memory = round($mem / 1048576, 2) .' MB';
            
        }
        
        return $memory;
        
    }
    function cpu_temp(){
        //Function requires the package lm-sensors.
        $checkinstalled = shell_exec('dpkg -s lm-sensors | grep -o "Status: .*"');
        if (substr($checkinstalled, 0, -1) == 'Status: install ok installed'){
            return shell_exec('sensors | grep -o "id 0:  +.*" | cut -f2- -d+ | cut -b-4');
        }
        else{
            return 'lm-sensors not installed: error '.$checkinstalled;
        }
    }
    echo '<h1>Server Monitor v0.1</h1>';
    echo '<h2>'.gethostname().'@'.$_SERVER['SERVER_ADDR'].'</h2>';
    ?>

<div style='width:300px' id='status'>
<?php
    echo '<h4>Last refreshed:'.time().'</h4>';
    echo '<h4>get_server_memory_usage(): '.get_server_memory_usage().'</h4>';
    echo '<h4>get_server_cpu_usage(): '.get_server_cpu_usage().'</h4>';
    echo '<h4>cpu_temp(): '.cpu_temp().'</h4>';
    echo '<h4>system_load(): '.system_load().'</h4>';
    echo '<h4>system_cores(): '.system_cores().'</h4>';
    echo '<h4>http_connections(): '.http_connections().'</h4>';
    echo '<h4>server_memory_usage(): '.server_memory_usage().'</h4>';
    echo '<h4>disk_usage(): '.disk_usage().'</h4>';
    echo '<h4>server_uptime(): '.server_uptime().' hours</h4>';
    echo '<h4>kernel_version(): '.kernel_version().'</h4>';
    echo '<h4>number_processes(): '.number_processes().'</h4>';
    echo '<h4>memory_usage(): '.memory_usage().'</h4>';
    ?>
</div>

<script>
function update() {
    $.ajax({url: "http://jeeves.gunner.systems/status.php", cache:false, success: function (result) {
           $('#status').html(result);
           setTimeout(function(){update()}, 5000);
           }});
}
update();
</script>
</body>
</html>
