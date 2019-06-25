<?php
    function mysql_fatal_error($msg){
        $msg2 = mysqli_error();
        echo <<< _END
<pre>
很抱歉，系統異常。
錯誤信息如下：
<p>$msg ： $msg2</p>
請回到上一頁後重新再試一次。
如果你還有問題，請發送 <a href="mailto:admin@server.com">電子郵件</a> 給管理員。
謝謝。
        
We are sorry, but it was not possible to complete the requested task.
The error message we got was:
<p>$msg： $msg2</p>
Please click the back button on your browser and try again.
If you are still having problems, please <a href="mailto:admin@server.com">email out administrator</a>.
Thank you.
</pre>
_END;
    }

    $db_server = "localhost";
    $db_user = "testers";
    $db_password = "";
    $db_name = "drupal7";
    $db_node = "share_node";
    $db_node_type = "dn_news";
    //$db_node_fields = array("nid", "vid", "type", "language", "title", "uid", "status", "created", "changed");
    //$db_field = "share_field_data_body";
    //$db_field_fields = array("entity_type", "bundle", "entity_id", "revision_id", "body_value", "body_format");
    
    $db = new mysqli($db_server, $db_user, $db_password, $db_name);//$db變數為mysqli新物件
    //$db = new mysqli("localhost", $db_user, $db_password, $db_name);
    
    //if (!$db) {
        
    if ($db -> connect_errno) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        //echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
        //echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        echo "Debugging errno: " . $db -> connect_errno . PHP_EOL;
        echo "Debugging error: " . $db -> connect_error . PHP_EOL;
        exit;
    }
    
    $db -> query("SET CHARACTER SET UTF8");
?>