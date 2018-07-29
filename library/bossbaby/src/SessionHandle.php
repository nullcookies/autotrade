<?php
namespace BossBaby;

class SessionHandle
{
    private $savePath;
    private $lifetime;

    function open($savePath, $sessionName)
    {
        $this->savePath = LOGS_DIR; // Ignore savepath and use our own to keep it safe from automatic GC
        $this->lifetime = 24 * 3600; // 1 hour minimum session duration
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    function close()
    {
        return true;
    }

    function read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $this->lifetime < time() && file_exists($file)) { // Use our own lifetime
                unlink($file);
            }
        }

        return true;
    }
}