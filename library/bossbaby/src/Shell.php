<?php
/**
 * @author     Ashraf M Kaabi
 * @name       Advance Linux Exec
 */

namespace BossBaby;

class Shell
{
    /**
     * Execute command in background
     *
     * @param     string $command
     * @param     int $priority
     * @return    int
     */
    function background($command = 'whoami', $priority = 0)
    {
        $priority = (int) $priority;
        
        if ($priority)
            $pid = shell_exec("nohup nice -n $priority $command > /dev/null & echo $!");
        else
            $pid = shell_exec("nohup $command > /dev/null & echo $!");
        
        return ($pid);
    }
    
    /**
     * Execute command and waiting for response
     */
    function exec($command = 'whoami')
    {
        return exec($command);
    }
    
    /**
     * Check command is running
     *
     * @param     int $pid
     * @return    boolean
     */
    function is_running($pid = 0)
    {
        if (!$pid)
            return null;
        
        exec("ps $pid", $processState);
        return (count($processState) >= 2);
    }
    
    /**
     * Kill command by pid
     *
     * @param  int $pid
     * @return boolean
     */
    function kill($pid = 0)
    {
        if (!$pid)
            return null;
        
        if (self::is_running($pid)) {
            exec("kill -KILL $pid");
            return true;
        } else
            return false;
    }

    public static function async_execute_file($file = null, $debug = false)
    {
        try {
            if (!is_file($file) or !file_exists($file)) {
                \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::error::file does not exist');
                throw new Exception("File $file does not exist");
            }

            $file = trim($file);
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $file);
            $output = shell_exec('php ' . $file . ' > /dev/null 2>/dev/null &');

            if ($debug) {
                // dump($output);
                \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::output::' . serialize($output));
            }

            return $output;
        }
        catch(Exception $e) {
            if ($debug) {
                \BossBaby\Utility::writeLog(__FILE__ . '::' . __FUNCTION__ . '::error::' . serialize($e));
            }

            throw new Exception($e->getMessage());
        }
    }

    public static function async_stream_request($url, $data = null, $optional_headers = null, $getresponse = false)
    {
        $params = array(
            'http' => array(
                'method' => 'GET',
                'content' => $data
            )
        );

        if ($optional_headers !== null) {
             $params['http']['header'] = $optional_headers;
        }

        $ctx = stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);

        if (!$fp) {
            return false;
        }

        if ($getresponse) {
            $response = stream_get_contents($fp);
            return $response;
        }

        return true;
    }

    public static function async_curl_request($url, $data = null, $optional_headers = null, $getresponse = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);

        return $response;
    }
}