<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StatusServer;

use Log;
use DB;
use View;

class SaveStatusServerCommand extends Command
{

    protected $signature = 'command:SaveStatusServerCommand';

    protected $description = 'get all ';

    static function getLastErrors() {
     $obj = new \stdClass();
        $cmd = "tail -230 ".env('RUTA_ERROR');
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get t°  ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "lastErrors";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
//            $obj->percent = intval(self::getServerLoad());
        }
        return $obj;
    }

    static function getLastAccess() {
     $obj = new \stdClass();
        $cmd = "tail -230 ".env('RUTA_ACCESS');
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get t°  ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "lastAccess";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
//            $obj->percent = intval(self::getServerLoad());
        }
        return $obj;
    }

    static function getTemp() {
     $obj = new \stdClass();
        $cmd = "cat /sys/class/thermal/thermal_zone0/temp";
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get t°  ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "temperatura";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->percent = 0;
            $obj->percent = $output[0]/1000;
//            $obj->percent = intval(self::getServerLoad());
        }
        return $obj;
    }

    static function getProcess() {
     $obj = new \stdClass();
        $cmd = 'ps -o "rss,cmd" -e | grep php';
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get t°  ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $total_memory=0;
            $total_process=0;
            foreach ($output as $key_linea => $linea) {
                $explode_data=explode(' ',$linea);
                $total_memory+=intval($explode_data[0]);
                $total_process++;
            }

            $obj->title = "process";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->memory = $total_memory/1024;
            $obj->percent = $total_process;

        }
        return $obj;
        
       
    }

    static function getLoadAvegare() {
        $obj = new \stdClass();
      
            $obj->title = "";
            $obj->success = 1;
            $obj->output =  sys_getloadavg();
            $obj->command = "";
            $obj->percent = abs(sys_getloadavg()[0]*100);
            // find model nam
        return $obj;
    }
     
    static function getCpu() {
        $obj = new \stdClass();
        $cmd = "cat /proc/cpuinfo";
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get CPU ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->percent = 0;
       /*
        $stat1 = file('/proc/stat'); 
        sleep(1); 
        $stat2 = file('/proc/stat'); 
        $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0])); 
        $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0])); 
        $dif = array(); 
        $dif['user'] = $info2[0] - $info1[0]; 
        $dif['nice'] = $info2[1] - $info1[1]; 
        $dif['sys'] = $info2[2] - $info1[2]; 
        $dif['idle'] = $info2[3] - $info1[3]; 
        $total = array_sum($dif); 
        foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);

        $obj->percent = intval($cpu['user']-100);
*/

            $obj->percent = intval(self::getServerLoad());
            // find model name
            foreach ($output as $value) {
                if (preg_match("/model name.+:(.*)/i", $value, $match)) {
                    $obj->title = $match[1];
                    break;
                }
            }
        }
        return $obj;
    }

    static function getMemory() {
        $obj = new \stdClass();
        $cmd = "free";
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get Memmory ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->title = "";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
            $obj->memTotalBytes = 0;
            $obj->memUsedBytes = 0;
            $obj->memFreeBytes = 0;
            if (preg_match("/Mem: *([0-9]+) *([0-9]+) *([0-9]+) */i", $output[1], $match)) {
                $obj->memTotalBytes = $match[1]*1024;
                $obj->memUsedBytes = $match[2]*1024;
                $obj->memFreeBytes = $match[3]*1024;
                $onePc = $obj->memTotalBytes / 100;
                $obj->memTotal = self::humanFileSize($obj->memTotalBytes);
                $obj->memUsed = self::humanFileSize($obj->memUsedBytes);
                $obj->memFree = self::humanFileSize($obj->memFreeBytes);
                $obj->percent = intval($obj->memUsedBytes / $onePc);
                $obj->title = "Total: {$obj->memTotal} | Free: {$obj->memFree} | Used: {$obj->memUsed}";
            }
            if (preg_match("/Swap: *([0-9]+) *([0-9]+) *([0-9]+) */i", $output[2], $match)) {
                $obj->memTotalBytesSwap = $match[1]*1024;
                $obj->memUsedBytesSwap = $match[2]*1024;
                $obj->memFreeBytesSwap = $match[3]*1024;
                $onePc = $obj->memTotalBytesSwap / 100;
                $obj->memTotalSwap = self::humanFileSize($obj->memTotalBytes);
                $obj->memUsedSwap = self::humanFileSize($obj->memUsedBytes);
                $obj->memFreeSwap = self::humanFileSize($obj->memFreeBytes);
                $obj->percentSwap = intval($obj->memUsedBytesSwap / $onePc);
                $obj->titleSwap = "Total: {$obj->memTotal} | Free: {$obj->memFree} | Used: {$obj->memUsed}";
            }
            if (isset($output[3]) && preg_match("/Swap: *([0-9]+) *([0-9]+) *([0-9]+) */i", $output[3], $match)) {
                $obj->memTotalBytesSwap = $match[1]*1024;
                $obj->memUsedBytesSwap = $match[2]*1024;
                $obj->memFreeBytesSwap = $match[3]*1024;
                $onePc = $obj->memTotalBytesSwap / 100;
                $obj->memTotalSwap = self::humanFileSize($obj->memTotalBytes);
                $obj->memUsedSwap = self::humanFileSize($obj->memUsedBytes);
                $obj->memFreeSwap = self::humanFileSize($obj->memFreeBytes);
                $obj->percentSwap = intval($obj->memUsedBytesSwap / $onePc);
                $obj->titleSwap = "Total: {$obj->memTotal} | Free: {$obj->memFree} | Used: {$obj->memUsed}";
            }
        }
        return $obj;
    }

    static function getDisk() {
        $obj = new \stdClass();
        $cmd = "df -h";
        exec($cmd . "  2>&1", $output, $return_val);
        if ($return_val !== 0) {
            $obj->error = "Get Disk ERROR** " . print_r($output, true);
            $obj->command = $cmd;
        } else {
            $obj->percent = 0;
            foreach ($output as $value) {
                if (preg_match("/([0-9]+)% \/$/i", $value, $match)) {
                    $obj->percent = intval($match[1]);
                    break;
                }
            }
            $obj->title = "Usage of {$obj->percent}%";
            $obj->success = 1;
            $obj->output = $output;
            $obj->command = $cmd;
        }
        return $obj;
        
    }

    static function humanFileSize($size, $unit = "") {
        if ((!$unit && $size >= 1 << 30) || $unit == "GB")
            return number_format($size / (1 << 30), 2) . "GB";
        if ((!$unit && $size >= 1 << 20) || $unit == "MB")
            return number_format($size / (1 << 20), 2) . "MB";
        if ((!$unit && $size >= 1 << 10) || $unit == "KB")
            return number_format($size / (1 << 10), 2) . "KB";
        return number_format($size) . " bytes";
    }

    static private function _getServerLoadLinuxData() {
        if (is_readable("/proc/stat")) {
            $stats = @file_get_contents("/proc/stat");

            if ($stats !== false) {
                // Remove double spaces to make it easier to extract values with explode()
                $stats = preg_replace("/[[:blank:]]+/", " ", $stats);

                // Separate lines
                $stats = str_replace(array("\r\n", "\n\r", "\r"), "\n", $stats);
                $stats = explode("\n", $stats);

                // Separate values and find line for main CPU load
                foreach ($stats as $statLine) {
                    $statLineData = explode(" ", trim($statLine));

                    // Found!
                    if
                    (
                            (count($statLineData) >= 5) &&
                            ($statLineData[0] == "cpu")
                    ) {
                        return array(
                            $statLineData[1],
                            $statLineData[2],
                            $statLineData[3],
                            $statLineData[4],
                        );
                    }
                }
            }
        }

        return null;
    }

    // Returns server load in percent (just number, without percent sign)
    static function getServerLoad() {
        $load = null;

        if (stristr(PHP_OS, "win")) {
            $cmd = "wmic cpu get loadpercentage /all";
            @exec($cmd, $output);

            if ($output) {
                foreach ($output as $line) {
                    if ($line && preg_match("/^[0-9]+\$/", $line)) {
                        $load = $line;
                        break;
                    }
                }
            }
        } else {
            if (is_readable("/proc/stat")) {
                // Collect 2 samples - each with 1 second period
                // See: https://de.wikipedia.org/wiki/Load#Der_Load_Average_auf_Unix-Systemen
                $statData1 = self::_getServerLoadLinuxData();
                sleep(1);
                $statData2 = self::_getServerLoadLinuxData();

                if
                (
                        (!is_null($statData1)) &&
                        (!is_null($statData2))
                ) {
                    // Get difference
                    $statData2[0] -= $statData1[0];
                    $statData2[1] -= $statData1[1];
                    $statData2[2] -= $statData1[2];
                    $statData2[3] -= $statData1[3];

                    // Sum up the 4 values for User, Nice, System and Idle and calculate
                    // the percentage of idle time (which is part of the 4 values!)
                    $cpuTime = $statData2[0] + $statData2[1] + $statData2[2] + $statData2[3];

                    // Invert percentage to get CPU time, not idle time
                    $load = 100 - ($statData2[3] * 100 / $cpuTime);
                }
            }
        }

        return $load;
    }

    static function getPhpFpmMInfo(){
        /*
        $data=file_get_contents ('http://127.0.0.1/status?json');
        $data=json_decode($data,true);
        if($data===false)
            $data=array();
        if(!isset($data['idle processes']))
            $data['idle processes']=0;
        if(!isset($data['active processes']))
            $data['active processes']=0;
        if(!isset($data['slow requests']))
            $data['slow requests']=0;
        if(!isset($data['listen queue']))
            $data['listen queue']=0;
        return $data;
        */
        $data=array();
        $data['idle processes']=0;
        $data['active processes']=0;
        $data['slow requests']=0;
        $data['listen queue']=0;
        return $data;
    }
    static function handle(){
        //dd(self::getPhpFpmMInfo());
        //obtener una señal más exacta espero 40 seg
        sleep(40);
        $statusServer=new StatusServer();
        $statusServer->name=env("APP_ENV");
        $tmp=self::getTemp();
            $statusServer->temp=@$tmp->percent;
        $tmp=self::getLoadAvegare();
            $statusServer->lv1=@$tmp->output[0];
            $statusServer->lv2=@$tmp->output[1];
            $statusServer->lv3=@$tmp->output[2];
        $tmp=self::getCpu();
            $statusServer->cpu=@$tmp->percent;
        $tmp=self::getMemory();
            $statusServer->memorySwap=@$tmp->percentSwap;
            $statusServer->memory=@$tmp->percent;
        $tmp=self::getProcess();
            $statusServer->memoryPhp=@$tmp->memory;
            $statusServer->processPhp=@$tmp->percent;
        $tmp=self::getDisk();
            $statusServer->disk=@$tmp->percent;
        $tmp=self::getLastErrors();
            $statusServer->last_error="".addslashes(@implode ('\n',@$tmp->output));
        $tmp=self::getLastAccess();
            $statusServer->last_access="".addslashes(@implode ('\n',@$tmp->output));
        $tmp=self::getPhpFpmMInfo();
            $statusServer->fpm_idle_processes=$tmp['idle processes'];
            $statusServer->fpm_active_processes=$tmp['active processes'];
            $statusServer->fpm_slow_requests=$tmp['slow requests'];
            $statusServer->fpm_listen_queue=$tmp['listen queue'];

        $statusServer->save();
    }
}