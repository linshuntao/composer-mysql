<?php
namespace Lin\Mysql;
/**
 * Created by PhpStorm.
 * User: linshuntao
 * Date: 2016/9/2
 * Time: 15:02
 */
class MysqlClass
{
    private $host;
    private $user;
    private $pwd;
    private $dbName;
    private $charset;
    private $conn = null;

    public function __construct($host, $user, $pwd, $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pwd = $pwd;
        $this->dbName = $dbname;
        $this->charset = 'UTF8';

        $this->connect();
        $this->switchDB($this->dbName);
        $this->setCharSet();
    }

    //连接数据库
    private function connect()
    {
        $this->conn = mysqli_connect($this->host, $this->user, $this->pwd);
    }

    //负责发送sql查询
    public function query($sql)
    {
        return mysqli_query($this->conn, $sql);
    }

    //设置编码格式
    private function setCharSet()
    {
        $sql = 'set names ' . $this->charset;
        $this->query($sql);
    }

    //选择数据库
    public function switchDB($db)
    {
        $sql = 'use ' . $db;
        $result = $this->query($sql);
        if ($result == false)
            echo '数据库选择失败';
    }

    //负责获取多行多列的select结果
    public function getAll($sql)
    {
        $list = array();
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        return $list;
    }

    //向数据库中插入数据，第一个参数表示要插入的表名，第二个参数是一个数组，数组的键名为数据表的字段名，数组的值为要插入的值，字符类型的需代理引号。
    public function insert($table, $inCondition)
    {
        $sql = 'INSERT INTO ' . $table;
        $field = '';
        $dataInsert = '';
        foreach ($inCondition as $key => $item) {
            $field = $field . ',' . $key;
            if (is_string($item)) {
                $dataInsert = $dataInsert . ',\'' . $item . '\'';
            } else {
                $dataInsert = $dataInsert . ',' . $item;
            }
        }
        $field = substr($field, 1);
        $dataInsert = substr($dataInsert, 1);
        $sql = $sql . ' (' . $field . ')' . ' VALUES (' . $dataInsert . ')';
        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 选择语句，第一个参数为要查询的表名，
     *          第二个参数为一个数组，存储要查询的字段，如a('name','id'),也可以为全部查询，a('*').
     *          第三个参数为一个数组，存储要查询的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要查询的值。
     *          如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
     */
    public function select($table, $getField, $inCondition)
    {
        $subSql = '';
        foreach ($getField as $v) {
            $subSql = $subSql . ',' . $v;
        }
        $subSql = substr($subSql, 1);
        $sql = 'SELECT ' . $subSql . ' FROM ' . $table . ' WHERE ';

        $condition = '';
        foreach ($inCondition as $key => $v) {
            if (strpos($key, 'AND')) {
                if (is_string($v)) {
                    $condition = $condition . substr($key, 0, strlen($key) - 3) . '\'' . $v . '\'' . ' AND ';
                } else {
                    $condition = $condition . substr($key, 0, strlen($key) - 3) . $v . ' AND ';
                }
            } elseif (strpos($key, 'OR')) {
                if (is_string($v)) {
                    $condition = $condition . substr($key, 0, strlen($key) - 2) . '\'' . $v . '\'' . ' OR ';
                } else {
                    $condition = $condition . substr($key, 0, strlen($key) - 2) . $v . ' OR ';
                }
            } else {
                if (is_string($v)) {
                    $condition = $condition . $key . '\'' . $v . '\'';
                } else {
                    $condition = $condition . $key . $v;
                }
            }
        }
        $sql = $sql . $condition;
        $list = array();
        $result = $this->query($sql);
        if (!$result) {
            return false;
        }
        while ($row = mysqli_fetch_assoc($result)) {
            $list[] = $row;
        }
        return $list;
    }

    /*
    * 删除语句，第一个参数为要删除的表名，
    *          第二个参数为一个数组，存储要查询的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要删除的值。
    *          如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
    */
    public function delete($table, $inCondition = '')
    {
        $sql = 'DELETE FROM ' . $table;
        $condition = '';

        if ($inCondition != '') {
            $sql = $sql . ' WHERE ';
            foreach ($inCondition as $key => $v) {
                if (strpos($key, 'AND')) {
                    if (is_string($v)) {
                        $condition = $condition . substr($key, 0, strlen($key) - 3) . '\'' . $v . '\'' . ' AND ';
                    } else {
                        $condition = $condition . substr($key, 0, strlen($key) - 3) . $v . ' AND ';
                    }
                } elseif (strpos($key, 'OR')) {
                    if (is_string($v)) {
                        $condition = $condition . substr($key, 0, strlen($key) - 2) . '\'' . $v . '\'' . ' OR ';
                    } else {
                        $condition = $condition . substr($key, 0, strlen($key) - 2) . $v . ' OR ';
                    }
                } else {
                    if (is_string($v)) {
                        $condition = $condition . $key . '\'' . $v . '\'';
                    } else {
                        $condition = $condition . $key . $v;
                    }
                }
            }
        }
        $sql = $sql . $condition;
        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    //整表删除，参数为要删除的表名。
    public function drop($table)
    {
        $sql = 'DROP TABLE ' . $table;
        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 修改语句，第一个参数为要修改的表名，
     *          第二个参数为一个数组，存储要修改的值，数组的下标为数据表的字段名，数组的值为要修改的值，如a('name'=>'lin','id'=>1)。
     *          第三个参数为一个数组，存储要修改的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要查询的值。
     *          如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
     */
    public function updata($table, $inData, $inCondition)
    {
        $sql = 'UPDATE ' . $table . ' SET ';
        $data = '';
        foreach ($inData as $key => $v) {
            $data = $data . ',' . $key . $v;
        }
        $data = substr($data, 1);
        $condition = '';
        foreach ($inCondition as $key => $v) {
            if (strpos($key, 'AND')) {
                if (is_string($v)) {
                    $condition = $condition . substr($key, 0, strlen($key) - 3) . '\'' . $v . '\'' . ' AND ';
                } else {
                    $condition = $condition . substr($key, 0, strlen($key) - 3) . $v . ' AND ';
                }
            } elseif (strpos($key, 'OR')) {
                if (is_string($v)) {
                    $condition = $condition . substr($key, 0, strlen($key) - 2) . '\'' . $v . '\'' . ' OR ';
                } else {
                    $condition = $condition . substr($key, 0, strlen($key) - 2) . $v . ' OR ';
                }
            } else {
                if (is_string($v)) {
                    $condition = $condition . $key . '\'' . $v . '\'';
                } else {
                    $condition = $condition . $key . $v;
                }
            }
        }
        $sql = $sql . $data . ' WHERE ' . $condition;
        if ($this->query($sql)) {
            return true;
        } else {
            return false;
        }

    }

//关闭数据库连接
    public
    function close()
    {
        mysqli_close($this->conn);
    }

}






