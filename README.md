# composer-mysql
#MysqlClass
封装了mysql类

#主要的方法：
#//host为主机名，user为数据库用户，pwd为数据库密码，dbname为数据库名
#1.public function __construct($host, $user, $pwd, $dbname)


#//向数据库中插入数据，第一个参数表示要插入的表名，第二个参数是一个数组，数组的键名为数据表的字段名，数组的值为要插入的值，字符类型的需代理引号。
#2.public function insert($table, $inCondition)


#/*
* 选择语句，第一个参数为要查询的表名，
*          第二个参数为一个数组，存储要查询的字段，如a('name','id'),也可以为全部查询，a('*').
*          第三个参数为一个数组，存储要查询的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要查询的值
*          如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
*/
#3.public function select($table, $getField, $inCondition)



#/*
* 删除语句，第一个参数为要删除的表名，
*          第二个参数为一个数组，存储要查询的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要删除的            值。
*           如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
*/
#4. public function delete($table, $inCondition = '')


#//整表删除，参数为要删除的表名。
#5.public function drop($table)


#/*
* 修改语句，第一个参数为要修改的表名，
*          第二个参数为一个数组，存储要修改的值，数组的下标为数据表的字段名，数组的值为要修改的值，如a('name'=>'lin','id'=>1)。
*          第三个参数为一个数组，存储要修改的条件，数组的下标为数据表的字段名，需带有条件符号，如'='，’>‘等，数组的值为要查询的            值。
*          如$a1 = array('name=AND' => 'hhhh','id>=' => 7);支持多条件，需在前一个字段名后面带上
*/

#6. public function updata($table, $inData, $inCondition)


#待续....
