<?php
namespace DbObject;
class DbObject
{
    public $sql;
    public $sql_where;
    public $sql_where_or;
    public $sql_limit;
    public $sql_order_by;

    public function __construct(\mysqli $dbConnection)
    {
        $this->myconn = $dbConnection;
    }
    private function db_query($sql,$object = true)
    {
        // if you are performig a UPDATE query; you will need to set $object == false
        file_put_contents('lo.txt',$sql);
    
        $result = mysqli_query($this->myconn,$sql);
        $count  = ($object)?mysqli_num_rows($result):mysqli_affected_rows($this->myconn);
        if($object)
        {
            if($count > 0)
            {
                $data = array();
                while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
                {
                    $data[] = $row;
                }
                return $data;
            }else
            {
                return null;
            }
        }else
        {
            return $count;
        }
    }
    
    private function doInsert($table,$arr,$exp_arr)
    {
        $patch1  = "(";
        $patch2  = "(";
        foreach($arr as $key=>$value)
        {
            if(!in_array($key,$exp_arr))
            {
                $patch1.= $key.",";
                $patch2.= "'".mysqli_real_escape_string($this->myconn,$value)."',";
            }
        }
        $patch1 =  substr($patch1,0,-1).")";
        $patch2 =  substr($patch2,0,-1).")";
        $sql = "insert into ".$table." ".$patch1." VALUES ".$patch2;
        file_put_contents('m_query.txt',$sql);
        $num_row = $this->db_query($sql,false);
        return $num_row;
    }
    private function doUpdate($table,$arr,$exp_arr,$clause)
    {
        $patch1     = "";
        $key_id     = "";
        foreach($arr as $key=>$value)
        {
            if(!in_array($key,$exp_arr))
            {
                $patch1.= $key."='".mysqli_real_escape_string($this->myconn,$value)."',";
            }
        }
        foreach($clause as $key=>$value)
        {
            $key_id.= " ".$key."='".$value."' AND";
        }
        $key_id  =  substr($key_id,0,-3);
        $patch1  =  substr($patch1,0,-1);
        $sql    = "UPDATE ".$table." SET ".$patch1." WHERE ".$key_id;
        file_put_contents("user_edit.txt",$sql);
        $num_row = $this->db_query($sql,false);
        return $num_row;
    }
    public function doSelect($table_name,array $field_name = array("*"))
    {
        $this->sql_where = null;
        $this->sql_where_or = null;
        $this->sql_limit = null;
        $fields = "";
        foreach($field_name as $value)
        {
            $fields .= $value.",";
        }
        $fields = substr($fields,0,-1);
        $sql = "SELECT ".$fields." FROM ".$table_name;
        $this->sql = $sql;
        return $this;
    }
    public function limit($lower_limit,$upper_limit = "")
    {
        $end = ($upper_limit == "")?"":",".$upper_limit;
        $this->sql_limit = " LIMIT ".$lower_limit.$end;
        return $this;
    }
    public function where($key,$operand,$value)
    {
        $where_clause = $key." ".$operand." '".mysqli_real_escape_string($this->myconn,$value)."' AND";
        $this->sql_where = $this->sql_where.$where_clause;
        return $this;
    }
    public function orderBy($field_name,$sort_type = "ASC")
    {
        $this->sql_order_by = " ORDER BY ".$field_name." ".$sort_type;
        return $this;
    }
    public function run()
    {
        $where_clause = ($this->sql_where == null)?"":substr($this->sql_where,0,-4);
        $where_or_clause = ($this->sql_where_or == null)?"":substr($this->sql_where_or,0,-3);
        $limit_clause = ($this->sql_limit == null)?"":$this->sql_limit;
        $where = ($this->sql_where == null && $this->sql_where_or == null)?"":" WHERE ".$where_clause.$where_or_clause;
        $result = $this->db_query($this->sql."".$where." ".$this->sql_order_by.$limit_clause);
        $this->sql          = "";
        $this->sql_order_by = "";
        $this->sql_where    = null;
        $this->sql_where_or = null;
        $this->sql_limit    = null;
        
        return $result;
    }
}