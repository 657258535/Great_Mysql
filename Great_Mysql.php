<?php
//屏蔽错误
// error_reporting(0);
header('Access-Control-Allow-Origin: *');
date_default_timezone_set('Asia/Shanghai');

// 数据库配置信息
$mysqlhost="localhost";
$mysqluser="dbuser";
$mysqlpass="123456";
$mysqlname="dbname";
//获取网站基础信息
$conn = rdata("/Admin/Tool/conn.php");
//获取用户设备头信息
$agent=strtolower($_SERVER['HTTP_USER_AGENT']);
// $conns=array(
//     "标题" => "Sport",
//     "关键词" => "",
//     "描述" => "",
//     "账号" => "admin",
//     "密码" => pass("sp666666"),
//     "支付宝商户号" => "",
//     "支付宝key" => "",
//     "支付宝密匙" => ""
// );
// wdata("/Admin/Tool/conn.php",$conns);
//读取数据
function rdata($path){
    $articles=explode("\n",file_get_contents($_SERVER['DOCUMENT_ROOT'].$path));
    // echo "<pre>";print_r($articles['1']);echo "</pre>";exit;
    return json_decode($articles['1'],true);//解码并以数组返回，取消true则为->对象
}
//储存数据
function wdata($path,$arr=array()){
    file_put_contents($_SERVER['DOCUMENT_ROOT'].$path,"<?php exit; ?>"."\n".json_encode($arr,JSON_UNESCAPED_UNICODE));
}
//执行sql语句
function setsql($sql)
{
    // echo($sql);

    $conn = mysqli_connect($GLOBALS["mysqlhost"], $GLOBALS["mysqluser"], $GLOBALS["mysqlpass"], $GLOBALS["mysqlname"]);
    if (!$conn) {
        die("请检查数据库账号密码是否正确");//mysqli_connect_error();
    }
    mysqli_query($conn, "SET NAMES UTF8");//设置数据库编码
    $results = mysqli_query($conn, $sql);//返回结果集
    $num=mysqli_affected_rows($conn);//获取受影响行数
    $nums=mysqli_insert_id($conn);//获取受影响id
    mysqli_close($conn);//关闭数据库
    if ($num>0) {
        if(strstr(strtolower($sql),"select")){
            $arr = array();
            while($row = mysqli_fetch_assoc($results)) {
                array_push($arr,$row);
            }
            unset($results);
	        return $arr;
        }else{
            $data=array(
                "id"=>$nums,
                "line"=>$num
                );
            return $data;
        }
    }else{
        return false;//echo "请检查sql语句正确性: " . $sql;//mysqli_error($conn);
    }
}

//增删改查
// 数据库新增操作
function sqlz($from, $key, $value){
	return setsql("insert into $from($key) values ($value)");
	//用法：sqlz("article","cid,uid,title,data,view,time","'$cid','$uid','$title','$data','$view','$time'");
}
//数据库删除操作
function sqls($from, $id){
	return setsql("delete from $from where id=$id ");
	//用法：sqls("article",$id);
}
//数据库修改操作
function sqlg($from, $id, $value){
	return setsql("update $from set $value where id=$id ");
	//用法：sqlg("article",$id,"title='newtitle',data='newdata'");
}
//查询数据库
function sqlc($from, $title="*",$by='order by id desc'){//order by id desc

$p = empty($_REQUEST['p']) or (int)$_REQUEST['p']<1 ? 1 : (int)$_REQUEST['p'];
$pagenum = 20;//每页输出多少
$pzs=sqlzs($from);
$pagezs=ceil($pzs/$pagenum);//总页数
$p = $p > $pagezs ? $pagezs : $p;
$s = $p*$pagenum;
$sp = ($p-1)<1 ? 1 : ($p-1);//上一页
$xp = ($p+1)>$pagezs ? $pagezs : ($p+1);//下一页
$page = ($s-$pagenum)<0 ? 0 : $s-$pagenum;//分页获取数据的位置

    $sql="select $title from $from $by limit $page,$pagenum";
    $data=array(
        "data"=> setsql($sql),//查询结果
        "page"=>(int)$p,//当前页
        "pagesize"=>$pagezs,//总页数
        "page-pre"=>$sp,//上一页
        "page-next"=>$xp,//下一页
        "pagezs"=>$pzs//总页
        );
    //   echo $sql;
	return $data;
	//用法：$data=sqlc("article","id,cid,uid,title,view,time","order by id desc");
}
//获取总页数
function sqlzs($from, $title="id"){
	  $arr=setsql("select count($title) from $from");
	  $count="count(".$title.")";
	  return $arr[0][$count];
}
//获取并过滤用户传过来的参数
function getpost($str){
    return getgl($_REQUEST[$str]);
}
//过滤sql语句注入
function getgl($post) 
{   
    $post = strtolower($post);//转换为小写
    $post = str_replace("_", "", $post); // 把 '_'过滤掉
    $post = str_replace("%", "", $post); // 把' % '过滤掉
    $post = str_replace("\"", "", $post); // 把' 双引号 '过滤掉
    $post = str_replace("'", "", $post); // 把' 单引号 '过滤掉
    $post = str_replace(",", "", $post); // 把' , '过滤掉
    $post = str_replace(" ", "", $post); // 把' 空格 '过滤掉
    $post = str_replace("&", "", $post); // 把' & '过滤掉
    $post = str_replace("#", "", $post); // 把' # '过滤掉
    $post = str_replace("$", "", $post); // 把' $ '过滤掉
    $post = str_replace("and", "", $post); // 把' and '过滤掉
    $post = str_replace("or", "", $post); // 把' or '过滤掉
    $post = str_replace("(", "", $post); // 把' ( '过滤掉
    $post = str_replace(")", "", $post); // 把' ) '过滤掉
    $post = str_replace("eval", "", $post); // 把' eval '过滤掉
    $post = str_replace("=", "", $post); // 把' = '过滤掉
    $post = str_replace("by", "", $post); // 把' by '过滤掉
    $post = str_replace("order", "", $post); // 把' order '过滤掉
    $post = str_replace("+", "", $post); // 把' + '过滤掉
    $post = str_replace("-", "", $post); // 把' - '过滤掉
    $post = str_replace("*", "", $post); // 把' * '过滤掉
    $post = str_replace("/", "", $post); // 把' / '过滤掉
    $post = str_replace("where", "", $post); // 把' where '过滤掉
    $post = str_replace("group", "", $post); // 把' group '过滤掉
    $post = str_replace("insert", "", $post); 
    $post = str_replace("delete", "", $post); 
    $post = str_replace("update", "", $post); 
    $post = str_replace("select", "", $post); 
    $post = str_replace("length", "", $post); 
    $post = str_replace("ascii", "", $post);
    $post = str_replace("from", "", $post);
    $post = str_replace("exists", "", $post);
    $post = str_replace("limit", "", $post);
    $post = str_replace("sleep", "", $post);
    $post = str_replace("if", "", $post);
    $post = str_replace("database", "", $post);
    $post = str_replace(";", "", $post);
    $post = str_replace("values", "", $post);
    $post = str_replace("like", "", $post);
    $post = str_replace("in", "", $post);
    $post = str_replace("into", "", $post);
    $post = str_replace("?", "", $post);
    $post = str_replace("@", "", $post);
    $post = str_replace("table", "", $post);
    $post = str_replace("script", "", $post);
    $post = str_replace("<", "", $post);
    $post = str_replace(">", "", $post);
    $post = str_replace("create", "", $post);
    $post = str_replace("alter", "", $post);
    $post = str_replace("drop", "", $post);
    $post = str_replace("truncate", "", $post);
    $post = str_replace("exec", "", $post);
    $post = str_replace("set", "", $post);
    $post = nl2br($post); // 回车转换
    // $post= htmlspecialchars($post); // html标记转换
    return $post;
}

//渲染列表输出
function echolist($str,$data){
    //自动根据字段输出内容
    if(!empty($data)){
    $datas=array_keys($data['0']);
    $s="";
    //列表数据生成
    for ($i = 0; $i < count($data); $i++) {
        $s .= $str;
        for ($ii = 0; $ii < count($datas); $ii++) {
            $s=str_ireplace("{{".$datas[$ii]."}}",$data[$i][$datas[$ii]],$s);
        }
    }
    return $s;
    }
    // else{
    //     echo "<script>location.href='?p=1'</script>";
    // }
    //end
}
function randabc( $length = 8 ){
    // 密码字符集，可任意添加你需要的字符
    $chars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 
    'i', 'j', 'k', 'l','m', 'n', 'o', 'p', 'q', 'r', 's', 
    't', 'u', 'v', 'w', 'x', 'y','z', 'A', 'B', 'C', 'D', 
    'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L','M', 'N', 'O', 
    'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y','Z', 
    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    // 在 $chars 中随机取 $length 个数组元素键名
    //, '!', '@','#', '$', '%', '^', '&', '*', '(', ')', '-', '_', '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',', '.', ';', ':', '/', '?', '|'
    $keys = array_rand($chars, $length); 
    $password = '';
    for($i = 0; $i < $length; $i++)
    {
        // 将 $length 个数组元素连接成字符串
        $password .= $chars[$keys[$i]];
    }
    return $password;
}
function pass($pass){
    for ($i = 0; $i < 99; $i++) {
         $pass=md5($pass);
    }
    return $pass;
}
?>