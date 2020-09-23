<?php

class DBX

{

    static $conn;

    static $lastQuery;

    static $error;



    private static $instance = null;



    public function DBX()
    {
    }



    public static function connect($modx)
    {
        self::$conn = $modx->db->conn;
    }

    public static function getConnection()
    {

        return self::$conn;
    }



    public static function pr($a)
    {

        echo '<pre>';

        print_r($a);

        echo '</pre>';
    }



    public static function dbDateFormat($dateStr, $format = 'Y-m-d')

    {

        // date str is in the format dd/mm/yyyy

        //strtotime makes assumptions on wether the input is US,UK et on the seperator.

        // for strtotime to work for UK dates it is best to use a dot as the seperator

        return date($format, self::strtotime($dateStr));
    }



    public static function strtotime($dateStr)

    {

        // date str is inthe format dd/mm/yyyy

        //php strtotime function makes assumptions on wether the input is US,UK et on the seperator.

        // for strtotime to work for UK dates it is best to use a dot as the seperator.

        $dateStr = preg_replace('/\D/', '.', $dateStr); // replace any non dgit with a dot

        return strtotime($dateStr);
    }



    public static function dbToPhpFormatDate($dbDateStr, $format = 'd/m/Y')

    {

        return date($format, strtotime($dbDateStr));
    }



    public static function execute($sql)

    {

        self::$error = false;

        self::$lastQuery = $sql;



        $result = mysqli_query(self::$conn, $sql);



        if (!$result) {

            self::$error = 'Database error: ' . mysqli_error(self::$conn);
        }

        return $result;
    }

    /**

     * @desc make a database query

     *  returns insert_id if INSERT , affected_rows if UPDATE , result ID if SELECT

     *

     */

    public static function query($sql)

    {



        if (!$sql) {

            return false;
        }



        self::$error = false;

        self::$lastQuery = $sql;

        $sql = trim($sql);



        // \TWS::pr($sql);

        // \TWS::pr(self::$conn);



        $result = mysqli_query(self::$conn, $sql);



        if ($result) {

            // no query errors

            if (preg_match("/^\binsert\b\s+/i", $sql)) {

                return mysqli_insert_id(self::$conn);
            } elseif (preg_match("/^\b(update|delete|replace)\b\s+/i", $sql)) {

                return mysqli_affected_rows(self::$conn);
            } else {

                // if query returns data

                if (mysqli_num_rows($result)) {

                    return $result;
                } else {

                    return false;
                }
            }
        } else {

            // query failed



            self::$error = 'Database error: ' . mysqli_error(self::$conn);

            return -1; //cant use false as this is the same result as no rows returned from query

        }
    }



    // ----- Start of insert functions



    /**

     * @desc returns a the data quoted and escaped if it should be

     */

    public static function quote($data, $type = 'varchar')

    {

        return in_array($type, array(

            'varchar',

            'char',

            'date',

            'datetime',

            'blob',

            'mediumblob',

            'text',

            'mediumtext',

            'timestamp',

            'enum',

        )) ? "'" . mysqli_real_escape_string(self::$conn, $data) . "'" : $data;
    }



    /**

     * @desc Creates a select sting for the table to match the data

     */

    public static function select_string($table, $data)

    {

        $selectData = self::_getQueryArray($table, $data, false);

        if (!$selectData) {

            return false;
        }



        if (!empty($selectData)) {

            foreach ($selectData as $i) {

                $o[] = "`" . $i['Field'] . "` = " . $i['Value'];
            }

            return $sql = "SELECT * FROM " . $table . " WHERE " . join(" and ", $o);
        } else {

            return false;
        }
    }



    /**

     * @Desc This function prepares an insert statement based on the data and the table properties

     * it returns the insert string or false if no data fields match the table fields.

     */

    public static function insert_string($table, $data)

    {

        $insertData = self::_getQueryArray($table, $data); // false means no timestamp data

        if (!$insertData) {

            return false;
        }



        if (!empty($insertData)) {

            foreach ($insertData as $i) {

                $iFields[] = "`" . $i['Field'] . "`";

                $iValues[] = $i['Value'];
            }

            return "INSERT into " . $table . " (" . implode(",", $iFields) . ') VALUES (' . implode(",", $iValues)

                . ')';
        } else {

            return false;
        }
    }



    /**

     * @Desc This function prepares an update statement based on the data and the table properties

     * it returns the insert string or false if no data fields match the table fields.

     */

    public static function update_string($table, $data, $where = '')

    {

        $updateData = self::_getQueryArray($table, $data);

        if (!$updateData) {

            return false;
        }

        // kill created date modification

        unset($updateData['created']);



        if (!empty($updateData)) {

            foreach ($updateData as $i) {

                if (empty($where) && $i['Field'] === 'id') {

                    $where = " WHERE `id`=" . $data['id'];

                    continue;
                }



                // remove the created value for UPDATES

                if ($i['Field'] != 'created') {

                    $items[] = "`" . $i['Field'] . "`" . "=" . $i['Value'];
                }
            }

            return "UPDATE " . $table . " SET " . implode(',', $items) . " " . $where;
        } else {

            return false;
        }
    }



    /**

     * @desc helper function for insert_string and update_string funcs

     *  returns a array of filed names and quoted/escaped values

     */

    public static function _getQueryArray($table, $data, $timestamp = true)

    {



        $query = "SHOW COLUMNS FROM " . $table;

        self::$lastQuery = $query;

        $result = mysqli_query(self::$conn, $query);

        if (!$result) {

            self::$error = 'Database error: ' . mysqli_error(self::$conn);

            return false;
        }



        if (mysqli_num_rows($result) > 0) {

            while ($row = mysqli_fetch_assoc($result)) {

                if (isset($data[$row['Field']])) {

                    preg_match('/^([^\(]*)/', $row['Type'], $type);

                    $queryData[] = array(

                        'Field' => $row['Field'],

                        'Value' => self::quote($data[$row['Field']], $type[1]),

                    );
                } elseif ($timestamp) {

                    // check for modifies and created fields

                    if ($row['Field'] == 'modified' or $row['Field'] == 'created') {

                        $queryData[] = array(

                            'Field' => $row['Field'],

                            'Value' => self::quote(gmdate("Y-m-d H:i:s", time()), 'datetime'),

                        );
                    }
                }
            }



            return $queryData;
        } else {

            pr("Table " . $table . " has no columns");
        }
    }



    // -------------------- end of insert funcs



    public static function ajaxQuery($sql)

    {

        if (!empty($sql)) {

            if ($query = self::query($sql)) {

                while ($row = mysqli_fetch_assoc($query)) {

                    $result[] = $row;
                }

                return array(

                    'replyCode' => '200',

                    'replyText' => 'Ok',

                    'data' => $result,

                );
            } else {

                return array(

                    'replyCode' => '500',

                    'replyText' => self::$error . " SQL=" . $sql,

                    'data' => array(),

                );
            }
        }
    }



    /**

     * @desc return a multirow result set as assoc array

     */

    public static function getRows($sql, $key = '')

    {

        if (!empty($sql)) {

            $result = array();

            $query = self::query($sql);



            if (!self::$error) {

                if ($query) {

                    while ($row = mysqli_fetch_assoc($query)) {

                        if (empty($key) || !isset($row[$key])) {

                            $result[] = $row;
                        } else {



                            $result[$row[$key]] = $row;
                        }
                    }
                }



                return $result;
            } else {



                return false;
            }
        }
    }

    /**

     * @desc return a single result set as assoc array

     */

    public static function getRow($sql)

    {

        if (!empty($sql)) {

            $result = array();

            $query = self::query($sql);



            if ($query > -1) {

                $result = mysqli_fetch_assoc($query);
            }

            return $result;
        }



        return -1;
    }



    public static function getById($table, $id)

    {

        $sql = "select * from " . $table . " where id =" . $id;



        return self::getRow($sql);
    }



    public static function update($table, $data, $where = '')

    {
        self::query(self::update_string($table, $data, $where));
    }



    public static function insert($table, $data)

    {

        unset($data['id']);

        $sql = self::insert_string($table, $data);

        $insertId = self::query($sql);

        return $insertId;
    }



    public static function delete($table, $criteria)

    {

        $sql = "delete from " . $table . ' where ';



        if (is_array($criteria) && count($criteria) > 0) {

            foreach ($criteria as $k => $v) {

                $sql .= '`' . $k . '`=' . $v . ' and';
            }

            // remove last ' and'

            $sql = rtrim($sql, 'and');
        } elseif (is_int($criteria)) {

            $sql .= "`id`=" . $criteria . " limit 1";
        } elseif (is_string($criteria)) {

            $criteria = preg_replace('/\bwhere\b /i', '', $criteria); // remove any user supplied where

            $sql .= $criteria;
        } else {

            return false;
        }

        //TWS::pr($sql);

        self::query($sql);
    }

    /**

     * @desc used to execute a srting with multiple sql statements

     *  useful for modules that read their setup sql from a file

     */



    public static function error()

    {
        return self::$error;
    }

    public static function showError()

    {
        return 'DBX ERROR: ' . self::$error . 'Last Query:' . self::$lastQuery;
    }

    public static function abortOnError()

    {

        if (self::error()) {

            self::pr(self::showError());

            exit;
        }
    }

    public function cleanInt($i)

    {

        return (int) $i;
    }



    // end of class

}
