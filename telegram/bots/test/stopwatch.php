<?php
if (!defined('IS_VALID')) die('Access denied.' . "\n");

class Stopwatch
{
    /** @var mysqli */
    private $db_conn;
    private $conn_type = 'mysqli';
    private $db_result;
    private $mysqli;
    
    /** @var int */
    private $stopwatch_id;

    /**
     * Stopwatch constructor
     * @param mysqli $mysqli
     * @param $stopwatch_id
     */
    public function __construct(\mysqli $mysqli, $stopwatch_id)
    {
        $this->mysqli = $mysqli;
        $this->stopwatch_id = intval($stopwatch_id);

        // Generate table
        $mysqli->generate_table();
    }

    private function init()
    {
        // make link
        if (!$this->db_conn) {
            if ($this->conn_type == "mysqli") {
                // make mysqli connection
                $this->db_conn = mysqli_connect('host', 'user', 'pass', 'dbname');
                if ($this->db_conn) {
                    $this->query("SET NAMES utf8;");
                }
            } else if ($this->conn_type == "mysql") {
                // make mysqli connection
                $this->db_conn = mysql_connect('host', 'user', 'pass');
                if ($this->db_conn) {
                    mysql_select_db('dbname', $this->db_conn);
                    $this->query("SET NAMES utf8;");
                } else {
                    trigger_error("No database connection");
                }
            } else {
                trigger_error("Unknown database connection-type");
            }
        }
        return $this->db_conn;
    }

    public function query($sql = null)
    {
        if (!$sql) return null;
        
        // check for link      
        if ($this->db_conn || $this->init()) {
            // do query
            if ($this->conn_type == "mysqli") {
                if (!$this->db_result = mysqli_query($this->db_conn, $sql)) {
                    throw new Exception(mysqli_error($this->db_conn) . "\n\n" . $sql);
                    trigger_error();
                    exit();
                }
            } else if ($this->conn_type == "mysql") {
                if (!$this->db_result = mysql_query($sql, $this->db_conn)) {
                    throw new Exception($sql);
                    trigger_error(mysql_error($this->db_conn) . "\n\n" . $sql);
                    exit();
                }
            } else {
                trigger_error("Unknown database connection-type");
            }

            return $this->db_result;
        }
    }
    
    public function escape($string)
    {
        if ($this->db_conn || $this->init()) {
            if ($this->conn_type == "mysqli") {
                return mysqli_real_escape_string($this->db_conn, $string);
            } else if ($this->conn_type == "mysql") {
                return mysql_real_escape_string($string);
            } else {
                trigger_error("Unknown database connection-type");
            }
        }
    }
    
    public function get_results()
    {
        $results = array();
        // get results
        if ($this->conn_type == "mysqli") {
            while ($row = @mysqli_fetch_array($this->db_result, MYSQLI_ASSOC)) {
                $results[] = $row;
            }
            // free result
            if ($this->db_result) {
                mysqli_free_result($this->db_result);
            }
        } else if ($this->conn_type == "mysql") {
            while ($row = @mysql_fetch_assoc($this->db_result)) {
                $results[] = $row;
            }
            // free result
            if ($this->db_result) {
                mysql_free_result($this->db_result);
            }
        } else {
            trigger_error("Unknown database connection-type");
        }
        // return
        return $results;
    }
    
    public function get_result()
    {
        $result = false;
        if ($this->conn_type == "mysqli") {
            // get result
            if ($this->db_result && !is_bool($this->db_result)) {
                $result = @mysqli_fetch_array($this->db_result, MYSQLI_ASSOC);
            } else {
                $result = false;
            }
            // free result
            if ($this->db_result && !is_bool($this->db_result)) {
                @mysqli_free_result($this->db_result);
            }
        } else if ($this->conn_type == "mysql") {
            // get result
            if ($this->db_result && !is_bool($this->db_result)) {
                $result = @mysql_fetch_assoc($this->db_result);
            } else {
                $result = false;
            }
            // free result
            if ($this->db_result && !is_bool($this->db_result)) {
                @mysql_free_result($this->db_result);
            }
        } else {
            trigger_error("Unknown database connection-type");
        }
        // return
        return $result;
    }

    // developer-1: get last inserted id
    public function get_last_insereted_id()
    {
        if ($this->conn_type == "mysqli") {
            return mysqli_insert_id($this->db_conn);
        } else if ($this->conn_type == "mysql") {
            return mysql_insert_id($this->db_conn);
        } else {
            trigger_error("Unknown database connection-type");
        }
        return 0;
    }
    
    // developer-2: get affected rows
    public function get_affected_rows() {
        if ($this->conn_type == "mysqli") {
            return mysqli_affected_rows($this->db_conn);
        } else if ($this->conn_type == "mysql") {
            return mysql_affected_rows($this->db_conn);
        } else {
            trigger_error("Unknown database connection-type");
        }
        return 0;
    }

    private function check_table_exists($table_name = null)
    {
        if (!$table_name)
            return -1;
        
        $sql = "SELECT * FROM `information_schema`.`tables` WHERE `table_schema` = DATABASE() AND  `table_name` = '" . $this->escape($table_name) . "';";
        $this->query($sql);
        return $this->get_result();
    }

    public function check_field_exists($table_name = null, $field_name = null)
    {
        if (!$table_name || !$field_name)
            return null;
        
        $sql = "SELECT * FROM `information_schema`.`columns` WHERE `table_schema` = DATABASE() AND TABLE_NAME='" . $this->escape($table_name) . "' AND COLUMN_NAME='" . $this->escape($field_name) . "'";
        $this->query($sql);
        return $this->get_result();
    }

    // --------------------------------------------- //

    public function generate_table()
    {
        $table_exist = $this->check_table_exists('stopwatch');
        // $db->query("DROP TABLE IF EXISTS `stopwatch`;");
        if (!$table_exist) {
            $sql = "CREATE TABLE IF NOT EXISTS `stopwatch` (
              `chat_id` int(10) unsigned NOT NULL,
              `timestamp` int(10) unsigned NOT NULL,
              PRIMARY KEY (`chat_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
            // echo "<br/>" . $sql;
            return $this->query($sql);
        }
        return $table_exist;
    }

    public function start()
    {
        $timestamp = time();
        $query = "
            INSERT INTO  `stopwatch` (`chat_id`, `timestamp`)
            VALUES ('$this->stopwatch_id', '$timestamp')
            ON DUPLICATE KEY UPDATE timestamp = '$timestamp'       
        ";
        return $this->mysqli->query($query);
    }

    /**
     * Delete row with stopwatch id
     * @return bool|mysqli_result
     */
    public function stop()
    {
    $query = "
        DELETE FROM `stopwatch`
        WHERE `chat_id` = $this->stopwatch_id
        ";
        return $this->mysqli->query($query);
    }

    /**
     * Find row with stopwatch id and return difference in seconds from saved Unix time and current time
     * @return string
     */
    public function status()
    {
        $query = "
            SELECT `timestamp` FROM  `stopwatch`
            WHERE `chat_id` = $this->stopwatch_id        
        ";
        $timestamp = $this->mysqli->query($query)->fetch_row();
        if (!empty($timestamp)) {
            return gmdate("H:i:s", time() - reset($timestamp));
        }
    }
}