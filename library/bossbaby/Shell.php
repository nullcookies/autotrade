<?php
/**
 * @author     Ashraf M Kaabi
 * @name       Advance Linux Exec
 */
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
}