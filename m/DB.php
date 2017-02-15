<?php
/**
 * Class Mysqli
 * Mysqli 数据库操作类
 * @author Mr.Hao
 * @date 2017-02-15
 * @email 409328820@qq.com
 * @website www.51hsw.com
 */
class MysqliClass
{

    private $link_id;
    private $handle;
    private $is_log;
    private $time;

    private $host = '127.0.0.1';
    private $user = 'root';
    private $pwd = '';
    private $database = 'lvyouapp';
    private $charset = 'utf8';
    private $log = 0;
    private $logfilepath = './';

    /**
     * 初始化mysqli操作
     */
    public function __construct()
    {
        $this->time = $this->microtime_float();

        $this->connect();
        $this->is_log = $this->log;
        if ($this->is_log) {
            $handle = fopen($this->logfilepath . "dblog.txt", "a+");
            $this->handle = $handle;
        }
    }

    /**
     * 连接接口具体实现
     */
    public function connect()
    {
        $this->link_id = mysqli_connect($this->host, $this->user, $this->pwd, $this->database);
        if (is_null($this->link_id)) {
            $this->halt("数据库连接失败");
        }
        mysqli_query($this->link_id, "set names " . $this->charset);
    }

    /**
     * 执行sql的语句
     * @param $sql mysql语句
     * @return bool|mysqli_result
     */
    public function query($sql)
    {
        $this->write_log("查询 " . $sql);
        $query = mysqli_query($this->link_id, $sql);
        if (!$query)
            $this->halt('Query Error: ' . $sql);
        return $query;
    }

    /**
     * 查询单条记录
     * @param $sql mysql查询语句
     * @return array|null
     */
    public function get_one($sql)
    {
        $query = $this->query($sql);
        $rt = mysqli_fetch_array($query,MYSQLI_ASSOC);
        $this->write_log("获取一条记录 " . $sql);
        return $rt;
    }

    /**
     * 查询全部记录
     * @param $sql mysql查询语句
     * @return array
     */
    public function get_all($sql)
    {
        $query = $this->query($sql);
        $i = 0;
        $rt = array();
        if ($query) {
            while ($row = mysqli_fetch_array($query,MYSQLI_ASSOC)) {
                $rt [$i] = $row;
                $i++;
            }
        }
        $this->write_log("获取全部记录 " . $sql);
        return $rt;
    }

    /**
     * 插入数据库操作
     * @param $table 表名
     * @param $dataArray 数据字段数组
     * @return bool
     */
    public function insert($table, $dataArray)
    {
        $field = "";
        $value = "";
        if (!is_array($dataArray) || count($dataArray) <= 0) {
            $this->halt('没有要插入的数据');
            return false;
        }
        while (list ($key, $val) = each($dataArray)) {
            $field .= "$key,";
            $value .= "'$val',";
        }
        $field = substr($field, 0, -1);
        $value = substr($value, 0, -1);
        $sql = "insert into $table($field) values($value)";
        $this->write_log("插入 " . $sql);
        if (!$this->query($sql))
            return false;
        return true;
    }

    /**
     * 更新数据表操作
     * @param $table 表名
     * @param $dataArray 要更新的字段数组
     * @param string $condition 条件，必须得设置
     * @return bool
     */
    public function update($table, $dataArray, $condition = "")
    {
        if (!is_array($dataArray) || count($dataArray) <= 0) {
            $this->halt('没有要更新的数据');
            return false;
        }
        $value = "";
        while (list ($key, $val) = each($dataArray))
            $value .= "$key = '$val',";
        $value .= substr($value, 0, -1);
        $sql = "update $table set $value where 1=1 and $condition";
        $this->write_log("更新 " . $sql);
        if (!$this->query($sql))
            return false;
        return true;
    }

    /**
     * 删除数据表数据操作
     * @param $table 表名
     * @param string $condition 条件，必须得设置
     * @return bool
     */
    public function delete($table, $condition = "")
    {
        if (empty ($condition)) {
            $this->halt('没有设置删除的条件');
            return false;
        }
        $sql = "delete from $table where 1=1 and $condition";
        $this->write_log("删除 " . $sql);
        if (!$this->query($sql))
            return false;
        return true;
    }

    /**
     * 返回结果集
     * @param $query
     * @return array|null
     */
    public function fetch_array($query)
    {
        $this->write_log("返回结果集");
        return mysqli_fetch_array($query,MYSQLI_ASSOC);
    }

    /**
     * 获取记录数
     * @param $results
     * @return int
     */
    public function num_rows($results)
    {
        if (!is_bool($results)) {
            $num = mysqli_num_rows($results);
            $this->write_log("获取的记录条数为" . $num);
            return $num;
        } else {
            return 0;
        }
    }

    /**
     * 释放结果集
     */
    public function free_result()
    {
        $void = func_get_args();
        foreach ($void as $query) {
            if (is_resource($query) && get_resource_type($query) === 'mysqli result') {
                return mysqli_free_result($query);
            }
        }
        $this->write_log("释放结果集");
    }

    /**
     * 获取自增插入的最后一条ID
     * @return int|string
     */
    public function insert_id()
    {
        $id = mysqli_insert_id($this->link_id);
        $this->write_log("最后插入的id为" . $id);
        return $id;
    }

    /**
     * 关闭数据库连接
     * @return bool
     */
    protected function close()
    {
        $this->write_log("已关闭数据库连接");
        return mysqli_close($this->link_id);
    }

    /**
     * mysql 错误提示
     * @param string $msg
     */
    private function halt($msg = '')
    {
        $msg .= "\r\n" . mysqli_error();
        $this->write_log($msg);
        echo $msg;
    }

    /**
     * 释放类的时候执行
     */
    public function __destruct()
    {
        $this->free_result();
        $use_time = ($this->microtime_float()) - ($this->time);
        $this->write_log("完成整个查询任务,所用时间为" . $use_time);
        if ($this->is_log) {
            fclose($this->handle);
        }
    }

    /**
     * 记录mysql的Log日志
     * @param string $msg
     */
    public function write_log($msg = '')
    {
        if ($this->is_log) {
            $text = date("Y-m-d H:i:s") . " " . $msg . "\r\n";
            fwrite($this->handle, $text);
        }
    }

    /**
     * 获取毫秒级时间
     * @return float
     */
    public function microtime_float()
    {
        list ($usec, $sec) = explode(" ", microtime());
        return (( float )$usec + ( float )$sec);
    }
}

?>