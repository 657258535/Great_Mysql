# Great_Mysql

Mysql的傻瓜式应用方法，不必劳神学习命令，Great_Mysql帮助你快速玩转MySQL

# 1.使用PHP：include、require、include_once、require_once其中之一的函数引入此文件

# 2.语法概括：

新增数据语法：sqlz("article","cid,uid,title,data,view,time","'$cid','$uid','$title','$data','$view','$time'");

删除数据语法：sqls("article",$id);

更新数据库语法：sqlg("article",$id,"title='newtitle',data='newdata'");

查询数据语法：sqlc("article","id,cid,uid,title,view,time","order by id desc");

其中：增删改返回值为:

array(
  "id"=>$nums,//受影响ID
  "line"=>$num//受影响行数
);

查询操作返回：

array(
  "data"=> 查询结果,
  "page"=>当前页码,
  "pagesize"=>总页数,
  "page-pre"=>上一页,
  "page-next"=>下一页
);

# 注意数据库表id必须为小写，除非用不到预置功能

# 用户传给数据库的数据建议用getpost()函数接收

rdata(站内的绝对路径)//简易的本地数据读取

wdata(站内的绝对路径)//简易的本地数据存入[数组类型]

1.例如：wdata("/Admin/Tool/conn.php",$array);

2.例如：rdata("/Admin/Tool/conn.php");

echolist($str,$arr)//简易的模版引擎$arr为数组，$str为文本型的模版字符串

需要替换为字段的部分:

例如：
$data=sqlc("article");
$str="<title>{{title}}</title><p>{{data}}</p>";

echo echolist($str,$data);




