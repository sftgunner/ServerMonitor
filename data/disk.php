<?php
        
        $disktotal = disk_total_space ('/');
        $diskfree  = disk_free_space  ('/');
        $diskuse   = round (100 - (($diskfree / $disktotal) * 100));
        
        echo $diskuse;
    
    ?>
