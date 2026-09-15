<?php
declare(strict_types=1);

/** Separate connections share only synthetic input and a filesystem start barrier. */
function pl_foundation_race(array $jobs): array
{
    $directory=sys_get_temp_dir().'/pl-party-outbound-race-'.bin2hex(random_bytes(10));
    if(!mkdir($directory,0700)){throw new RuntimeException('Cannot prepare foundation race.');}
    $barrier=$directory.'/start';$processes=[];
    try{
        foreach($jobs as $index=>$job){
            $file=$directory.'/'.$index.'.json';
            file_put_contents($file,json_encode($job+['barrier'=>$barrier],JSON_THROW_ON_ERROR));
            $pipes=[];
            $process=proc_open([PHP_BINARY,__DIR__.'/party_outbound_worker.php',$file],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
            if(!is_resource($process)){throw new RuntimeException('Cannot start foundation worker.');}
            fclose($pipes[0]);stream_set_blocking($pipes[1],false);stream_set_blocking($pipes[2],false);
            $processes[]=['process'=>$process,'out'=>$pipes[1],'err'=>$pipes[2],'stdout'=>'','stderr'=>'','exit'=>null];
        }
        touch($barrier);$deadline=microtime(true)+30;
        do{
            $running=false;
            foreach($processes as &$entry){
                if($entry['exit']!==null){continue;}
                $entry['stdout'].=stream_get_contents($entry['out']);$entry['stderr'].=stream_get_contents($entry['err']);
                $status=proc_get_status($entry['process']);
                if($status['running']){$running=true;}else{$entry['exit']=$status['exitcode'];}
            }
            unset($entry);
            if($running){usleep(10000);}
        }while($running&&microtime(true)<$deadline);
        assert_true(!$running,'Foundation workers exceeded the deadline.');$results=[];
        foreach($processes as &$entry){
            $entry['stdout'].=stream_get_contents($entry['out']);$entry['stderr'].=stream_get_contents($entry['err']);
            assert_same(0,$entry['exit'],$entry['stderr']);
            $results[]=json_decode($entry['stdout'],true,512,JSON_THROW_ON_ERROR);
        }
        unset($entry);return $results;
    }finally{
        unset($entry);
        foreach($processes as $entry){
            if($entry['exit']===null&&is_resource($entry['process'])){proc_terminate($entry['process']);}
            foreach(['out','err'] as $pipe){if(is_resource($entry[$pipe])){fclose($entry[$pipe]);}}
            if(is_resource($entry['process'])){proc_close($entry['process']);}
        }
        foreach(glob($directory.'/*')?:[] as $file){unlink($file);}rmdir($directory);
    }
}
