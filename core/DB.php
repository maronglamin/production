<?php

class DB 
{
    private static $_instance = null;
    private $_pdo, $_query, $_error = false, $_result, $_count = 0, $_last_insert_id = null;

    private function __construct()
    {
        try
        {
            $this->_pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
        }
        catch(PDOException $ex)
        {
            die($ex->getMessage());
        }
    }

    public static function get_instance()
    {
        if (!isset(self::$_instance)) 
        {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    public function query($sql, $params = [])
    {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($sql))
        {
            $x = 1;
            if (count($params)) 
            {
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($this->_query->execute())
            {
                $this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
                $this->_last_insert_id = $this->_pdo->lastInsertId();
            }
            else
            {
                $this->_error = true;
            }
        }
        return $this;
    }

    protected function _read($table, $params = [])
    {
        $condition_string = '';
        $bind = [];
        $order = '';
        $limit = '';

        if (isset($params['conditions']))
        {
            // conditions
            if (is_array($params['conditions']))
            {
                foreach ($params['conditions'] as $condition)
                {
                    $condition_string .= ' ' . $condition . ' AND'; 
                }
                $condition_string = trim($condition_string);
                $condition_string = rtrim($condition_string, ' AND');
            }
            else
            {
                $condition_string = $params['conditions'];
            }

            if ($condition_string != '')
            {
                $condition_string = ' WHERE ' . $condition_string;
            }

            // binding
            if (array_key_exists('bind', $params))
            {
                $bind = $params['bind'];
            }

            // order 
            if (array_key_exists('order', $params)) 
            {
                $order = ' ORDER BY ' . $params['order'];
            }

            // limit
            if (array_key_exists('limit', $params))
            {
                $limit = ' LIMIT ' . $params['limit'];
            }

            $sql = "SELECT * {$table}{$condition_string}{$order}{$limit}";
            
            if ($this->query($sql, $bind))
            {
                if (!count($this->_result)) return false;
                return true;
            }
            return false;
        }
    }

    public function find($table, $params = [])
    {
        if ($this->_read($table, $params))
        {
            return $this->results();
        }
        return false;
    }

    public function findFirst($table, $params = [])
    {
        if ($this->_read($table, $params))
        {
            return $this->first();
        }
        return false;
    }

    public function insert($table, $fields = [])
    {
        $feild_string = '';
        $value_string = '';
        $values = [];

        foreach ($fields as $field => $value)
        {
            $feild_string .= '`'. $field . '`,';
            $value_string .= '?,';
            $values[] = $value;
        }

        $feild_string = rtrim($feild_string, ',');
        $value_string = rtrim($value_string, ',');

        $sql = "INSERT INTO {$table} ({$feild_string}) VALUES ({$value_string})";
        if (!$this->query($sql, $values)-> error()) 
        {
            return true;
        }
        return false;
    }

    public function update($table, $id, $fields = [])
    {
        $feild_string = '';
        $values = [];

        foreach ($fields as $field => $value)
        {
            $feild_string .= ' ' . $field . ' = ?,';
            $values[] = $value;
        }

        $feild_string = trim($feild_string);
        $feild_string = rtrim($feild_string, ',');

        $sql = "UPDATE {$table} SET {$feild_string} WHERE id = {$id}";
        if (!$this->query($sql, $values)-> error()) 
        {
            return true;
        }
        return false;
    }

    public function delete($table, $id)
    {
        $sql = "DELETE FROM {$table} WHERE id = {$id}";
        if (!$this->query($sql)-> error()) 
        {
            return true;
        }
        return false;
    }

    public function results()
    {
        return $this->_result;
    }

    public function first() 
    {
        return (!empty($this->_result))? $this->_result[0]: [];
    }

    public function count() 
    {
        return $this->_count;
    }

    public function lastID()
    {
        return $this->_last_insert_id;
    }

    public function get_columns($table)
    {
        return $this->query("SHOW COLUMNS FROM {$table}")->results();
    }

    public function error() 
   {
       return $this->_error;
   } 
}