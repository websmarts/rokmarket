<?php
class DBX
{
    static protected $dbh;
    static $lastQuery;
    static $error;

    private static $instance = NULL;

    function DBX() { }

    static function dbDateFormat($dateStr, $format='Y-m-d'){
        // date str is in the format dd/mm/yyyy 
        //strtotime makes assumptions on wether the input is US,UK et on the seperator.
        // for strtotime to work for UK dates it is best to use a dot as the seperator      
        return date( $format, self::strtotime($dateStr));
    }

    static function strtotime($dateStr){
        // date str is inthe format dd/mm/yyyy 
        //php strtotime function makes assumptions on wether the input is US,UK et on the seperator.
        // for strtotime to work for UK dates it is best to use a dot as the seperator.
        $dateStr = preg_replace('/\D/','.',$dateStr); // replace any non dgit with a dot
        return strtotime($dateStr);
    }

    static function dbToPhpFormatDate($dbDateStr,$format='d/m/Y'){
        return date($format,strtotime($dbDateStr));
    }

    //TRANSACTION SUPPORT - TABLES MUST be INNODB
    static function begin(){
        mysql_query("BEGIN");
    }
    static function commit(){
        mysql_query("COMMIT");
    }
     static function rollback(){
        mysql_query("ROLLBACK");
    }
    

    static function execute($sql)
    {
        self::$error=false;
        self::$lastQuery=$sql;

        $result=mysql_query($sql);

        if (!$result)
        {
            self::$error='Database error: ' . mysql_error();
        }
        return $result;
    }
    /**
    * @desc make a database query
    *  returns insert_id if INSERT , affected_rows if UPDATE , result ID if SELECT
    * 
    */
    static function query($sql)
    {


        if(!$sql){
            return false;
        }

        self::$error=false;
        self::$lastQuery=$sql;
        $sql=trim($sql);

        $result=mysql_query($sql);

        if ($result)
        { // no query errors
            if (preg_match("/^\binsert\b\s+/i", $sql))
            {
                return mysql_insert_id();
            }
            elseif (preg_match("/^\b(update|delete|replace)\b\s+/i", $sql))
            {
                return true;
            }
            else
            {
                // if query returns data
                if (mysql_num_rows($result))
                {
                    return $result;
                }
                else
                {
                    return false;
                }
            }
        }
        else
        {          // query failed

            self::$error='Database error: ' . mysql_error();
            return -1; //cant use false as this is the same result as no rows returned from query
        }
    }

    // ----- Start of insert functions

    /**
    * @desc returns a the data quoted and escaped if it should be    
    */
    static function quote($data, $type = 'varchar') { return in_array($type, array
        (
            'varchar',
            'char',
            'date',
            'datetime',
            'blob',
            'mediumblob',
            'text',
            'mediumtext',
            'timestamp',
            'enum'
        )) ? "'" . mysql_real_escape_string($data) . "'" : $data; }

    /**
    * @desc Creates a select sting for the table to match the data
    */
    static function select_string($table, $data)
    {
        $selectData=self::_getQueryArray($table, $data, false);
        if(!$selectData){
            return false;
        }

        if (!empty($selectData))
        {
            foreach ($selectData as $i)
            {
                $o[]="`" . $i['Field'] . "` = " . $i['Value'];
            }
            return $sql="SELECT * FROM " . $table . " WHERE " . join(" and ", $o);
        }
        else
        {
            return false;
        }
    }

    /**
    * @Desc This function prepares an insert statement based on the data and the table properties
    * it returns the insert string or false if no data fields match the table fields.
    */
    static function insert_string($table, $data)
    {
        $insertData=self::_getQueryArray($table, $data); // false means no timestamp data
        if(!$insertData){
            return false;
        }

        if (!empty($insertData))
        {
            foreach ($insertData as $i)
            {
                $iFields[] = "`" . $i['Field'] . "`";
                $iValues[]=$i['Value'];
            }
            return "INSERT into " . $table . " (" . implode(",", $iFields) . ') VALUES (' . implode(",", $iValues)
            . ')';
        }
        else
        {
            return false;
        }
    }

    /**
    * @Desc This function prepares an update statement based on the data and the table properties
    * it returns the insert string or false if no data fields match the table fields.
    */
    static function update_string($table, $data, $where = '')
    {
        $updateData=self::_getQueryArray($table, $data);
        if(!$updateData){
            return false;
        }
        
        
        // kill created date modification
        unSet($updateData['created']);

        if (!empty($updateData))
        {
            foreach ($updateData as $i)
            {
                if (empty($where) && $i['Field'] === 'id')
                {
                    $where=" WHERE `id`=" . $data['id'];
                    continue;
                } else {
                    $where = trim($where);
                    if(!empty($where)){
                        if(!preg_match('/^where /i',$where)){
                            $where = ' WHERE '.$where;
                        }
                    }
                }

                // remove the created value for UPDATES
                if ($i['Field'] != 'created')
                {
                    $items[]="`" . $i['Field'] . "`" . "=" . $i['Value'];
                }
            }
            return "UPDATE " . $table . " SET " . implode(',', $items) . " " . $where;
        }
        else
        {
            return false;
        }
    }

    /**
    * @desc helper function for insert_string and update_string funcs
    *  returns a array of filed names and quoted/escaped values
    */
    static function _getQueryArray($table, $data, $timestamp = true)
    {

        $query = "SHOW COLUMNS FROM " . $table;
        self::$lastQuery = $query;
        $result=mysql_query($query);
        if(!$result){
            self::$error = 'Database error: ' . mysql_error();
            return false;
        }

        if (mysql_num_rows($result) > 0)
        {
            while ($row=mysql_fetch_assoc($result))
            {
                if (isSet($data[$row['Field']]))
                {
                    preg_match('/^([^\(]*)/', $row['Type'], $type);
                    $queryData[]=array
                    (
                        'Field' => $row['Field'],
                        'Value' => self::quote($data[$row['Field']], $type[1])
                    );
                }
                elseif ($timestamp)
                {
                    // check for modifies and created fields
                    if ($row['Field'] == 'modified' or $row['Field'] == 'created')
                    {
                        $queryData[]=array
                        (
                            'Field' => $row['Field'],
                            'Value' => self::quote(gmdate("Y-m-d H:i:s", time()), 'datetime')
                        );
                    }
                }
            }

            return $queryData;
        }
        else
        {
            pr("Table " . $table . " has no columns");
        }
    }

    // -------------------- end of insert funcs

    static function ajaxQuery($sql)
    {
        if (!empty($sql))
        {
            if ($query=self::query($sql))
            {
                while ($row=mysql_fetch_assoc($query))
                {
                    $result[]=$row;
                }
                return array
                (
                    'replyCode' => '200',
                    'replyText' => 'Ok',
                    'data' => $result
                );
            }
            else
            {
                return array
                (
                    'replyCode' => '500',
                    'replyText' => self::$error . " SQL=" . $sql,
                    'data' => array()
                );
            }
        }
    }

    /**
    * @desc return a multirow result set as assoc array
    */
    static function getRows($sql, $key = '')
    {
        if (!empty($sql))
        {
            $result=array();
            $query=self::query($sql);

            if (!self::$error)
            {
                if ($query)
                {
                    while ($row=mysql_fetch_assoc($query))
                    {
                        if (empty($key) || !isSet($row[$key]))
                        {
                            $result[]=$row;
                        }
                        else
                        {

                            $result[$row[$key]]=$row;
                        }
                    }
                }

                return $result;
            }
            else
            {

                return false;
            }
        }
    }
    /**
    * @desc return a single result set as assoc array
    */
    static function getRow($sql)
    {
        if (!empty($sql))
        {
            $result=array();
            $query=self::query($sql);

            if ($query > -1)
            {
                $result=mysql_fetch_assoc($query);
            }
            return $result;
        }

        return -1;
    }

    static function getById($table, $id)
    {
        $sql="select * from " . $table . " where id =" . $id;

        return self::getRow($sql);
    }

    static function update($table, $data, $where = '') { self::query(self::update_string($table, $data, $where)); }

    static function insert($table, $data)
    {
        unSet($data['id']);
        $sql=self::insert_string($table, $data);
        $insertId=self::query($sql);
        return $insertId;
    }

    static function delete($table, $criteria)
    {
        $sql="delete from " . $table . ' where ';

        if (is_array($criteria) && count($criteria) > 0)
        {
            foreach ($criteria as $k => $v)
            {
                $sql.='`' . $k . '`=' . $v . ' and';
            }
            // remove last ' and'
            $sql=rtrim($sql, 'and');
        }
        elseif (is_int($criteria))
        {
            $sql.="`id`=" . $criteria . " limit 1";
        }
        elseif (is_string($criteria))
        {
            $criteria=preg_replace('/\bwhere\b /i', '', $criteria); // remove any user supplied where
            $sql.= $criteria;
        }
        else
        {
            return false;
        }
        //TWS::pr($sql);
        self::query($sql);
    }
    /**
    * @desc used to execute a srting with multiple sql statements 
    *  useful for modules that read their setup sql from a file
    */

    static function error() { return self::$error; }
    static function showError(){return 'DBX ERROR: '.self::$error .'Last Query:'.self::$lastQuery;}
    static function abortOnError(){
        if(self::error()){
            self::pr(self::showError());
            exit;
        }
    }
    static function pr($a, $print = 1)
    {
        $html='<pre class="prettyprint linenums" >';
        $html.=print_r($a, true);
        $html.='</pre>';

        if ($print)
        {
            echo $html;
        }
        else
        {
            return $html;
        }
    }
    function cleanInt($i){
        return (int) $i;
    }

    // end of class
}
?>