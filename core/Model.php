<?php

class Model 
{
    protected $_db, $_table, $_model_name, $_soft_delete = false, $_column_names = [];
    public $id;

    public function __construct($table)
    {
        $this->_db = DB::get_instance();
        $this->_table = $table;
        $this->_set_table_columns();

        $this->_model_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->_table)));
    }

    protected function _set_table_columns()
    {
        $columns = $this->get_columns();
        foreach ($columns as $column)
        {
            $this->_column_names[] = $column->Field;
            $this->{$column_name} = null;
        }
    }

    public function get_columns()
    {
        return $this->_db->get_columns($this->_table);
    }

    public function find($params = [])
    {
        $result = [];
        $result_query = $this->_db->find($this->_table, $params);
        
        foreach ($result_query as $result)
        {
            $obj = new $this->_model_name($this->_table);
            $obj->populate_object_data($result);
            $result[] = $obj;
        }
        return $result;
    }

    public function find_first($params = [])
    {
        $result_query = $this->_db->find_first($this->_table);
        $result = new $this->_model_name($this->_table);
        $result->populate_object_data($result_query);
        return $result;
    }

    public function find_by_id($id)
    {
        return $this->find_first([
            'conditions' => "id = ?",
            'bind' => [$id]
        ]);
    }

    public function insert($fields)
    {
        if (empty($fields)) return false;
        return $this->_db->insert($this->_table, $fields);
    }

    public function update($id, $fields)
    {
        if (empty($fields)) return false;
        return $this->_db->update($this->_table, $id, $fields);
    }

    public function delete($id = '')
    {
        if ($id = ' ' && $this->id == '') return false;
        $id = ($id == '') ? $this->id : $id;
        if ($this->_soft_delete)
        {
            return $this->update($id, [
                'deleted' => 1
            ]);
        }
        return $this->delete($this->_table, $id);
    }

    protected function populate_object_data($result) 
    {
        foreach($result as $key => $val)
        {
            $this->$key = $val;
        }
    }
}