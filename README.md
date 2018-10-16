# FuseboxApplication
Fusebox framework are used

## Add PDO class to updata old mysql-li

```
<?php
/**
 * PDODatabase
 *
 * @see http://php.net/manual/en/book.pdo.php
 *
 * @version 1.0.3 :: Added executeReturnLastID() method
 * @version 1.0.2 :: Added bindParamArray() helper method for IN
 * @version 1.0.1 :: Added getRow for expected single records
 * @version 1.0.0
 *
 * @author Ross MacLachlan
 *
 */
namespace App;

use \PDO as PDO;  // <--- need by PhpStorm to find Methods of PDO

class PDODatabase{

    /**
     * @var PDO
     */
    private $conn;

    /**
     * @var string
     */
    private $error;

    /**
     * @var PDOStatement
     */
    private $stmt;

    /**
     * @var array $params Used with debugShowQuery()
     */
    private $params = [];

    /**
     * @var string $sql Used with debugShowQuery()
     */
    private $sql = '';

    /**
     * PDODatabase constructor.
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     */
    public function __construct($dsn, $username, $password, $options = [])
    {
        // Set default options
        $default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_EMULATE_PREPARES => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $options = array_replace($default_options, $options);
        $this->conn = new PDO($dsn, $username, $password, $options);
    }

    /**
     * Takes a SQL query and prepares a statement for execution. Stores a PDOStatement object in stmt property
     * @param string $query
     * @return void
     */
    public function prepare($query){
        // re-init
        $this->params = [];
        $this->sql = $query;
        $this->stmt = $this->conn->prepare($query);
    }

    /**
     * @param $param
     * @param $value
     * @param null $type
     * @return void
     */
    public function bind($param, $value, $type = null){
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->params[] = ['Param' => $param, 'Value' => $value, 'Type' => $type];
        $this->stmt->bindValue($param, $value, $type);
    }

    /**
     * @return bool
     */
    public function execute(){
        return $this->stmt->execute();
    }
    
    /**
     * @return string
     */
    public function executeReturnID () {
        $this->execute();
        return $this->lastInsertId();
    }
    
    /**
     * @return array
     */
    public function getResultSet(){
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * @return array
     *
     * @desc    This method should be used when there are no nested queries to loop through. If there are,
     *          use getResultSet() instead to extract the entire record set from the object TBS 2018-08-14
     */
    public function getRow(){
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return int
     */
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    /**
     * @return string
     */
    public function lastInsertId(){
        return $this->conn->lastInsertId();
    }

    /**
     * @return bool
     */
    public function beginTransaction(){
        return $this->conn->beginTransaction();
    }

    /**
     * @return bool
     */
    public function endTransaction(){
        return $this->conn->commit();
    }

    /**
     * @return bool
     */
    public function cancelTransaction(){
        return $this->conn->rollBack();
    }

    /**
     * Helper Method
     *
     * Returns a string :id1,:id2,:id3 and also updates your $bindArray of
     * bindings that you will need when it's time to run your query. Easy!
     *
     * Usage:
     *   $bindString = $PDOdb->bindParamArray("id", $_GET['ids'], $bindArray);
     *   $userConditions .= " AND users.id IN($bindString)";
     *
     * See:
     * https://stackoverflow.com/questions/920353/can-i-bind-an-array-to-an-in-condition#22663617
     *
     * @param $prefix string
     * @param $values array Indexed array of values
     * @param $bindArray array
     * @return string
     */
    public function bindParamArray($prefix, $values, &$bindArray)
    {
        $str = "";
        foreach($values as $index => $value){
            $str .= ":".$prefix.$index.",";
            $bindArray[$prefix.$index] = $value;
        }
        return rtrim($str,",");
    }

    /**
     * @param bool $returnResult
     * @return string
     */
    public function debugShowQuery($returnResult = false)
    {
        $str = '';
        $str .= '<b>Show Query</b>:';
        $str .= '<blockquote style="margin:5px">' . $this->sql . '</blockquote>';
        $str .= '<b>Where:</b>:';
        $str .= '<blockquote style="margin:5px">';
        foreach ($this->params as $param) {
            $str .= $param['Param'] . ' = ' . $param['Value'] . '<br>';
        }
        $str .= '</blockquote>';
        if ($returnResult) {
            return $str;
        } else {
            echo $str;
        }
    }
}

```

## others

* make some changes for broadcast

