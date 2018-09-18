<?php

header('Access-Control-Allow-Origin: *');

header("Content-Type: application/json; charset=utf-8");

date_default_timezone_set("Asia/Shanghai");


require_once '../inc/common_header.php';

require_once '../util/print_form.php';

require_once '../domain/Solution.php';

$solution = new Solution(print_form($_POST));


$filename = basename(__FILE__);


try {
    $answer = "no frame or script loaded";


    require_once '../util/check_form.php';

    $chat_key = get_chat_key();


    include '../util/chinese_support.php';

 
// ============== 在此下方添加逻辑 =============
    
// ============== Write behind this line =============



    if (isset($_GET['frame'])) {
        $frame = "./" . $_GET['frame'] . ".php";

        if (file_exists($frame)) {

            include $frame;
        }
        else {
            $answer = "frame not exist";
        }
    } 
        elseif (isset($_GET['script'])) {
        $script = "../" . $user_script_dir . $_GET['script'] . ".php";

        if (file_exists($script)) {
            include $script;
        } 
        else {
            $answer = "script not exist";
  
      }
  
  } 
   else {
        // no frame or script loaded
    }

    
// ============== Your solution ends here =============
    
// ============== 请勿修改下方代码，除非你知道你在做什么 =============

    
// Get result from answer if using Chinese
    if (isset($答案)) {
        $answer = $答案;
    }


    $solution = new Solution($answer);
 
   if (isset($next)) {
        $solution = new Solution($answer, $next);

    }

}

catch (Exception $e) {
    $solution = new Solution('Caught exception: 
' . $e->getMessage() . ' Form: ' 
. print_form($_POST), 'error');

}


$result = $solution->to_json();


file_put_contents("/home/ubuntu/test3.txt", $result, FILE_APPEND);

file_put_contents("/home/ubuntu/test3.txt", "\n", FILE_APPEND);



echo $result;

//This is the background data interaction code of the 6th China (Shanghai) international technology import and export fair.
$next = "error";
$answer = "start answer";
//This is to save the listener log to the TXT file.
$post_content = print_form($_POST);
file_put_contents("/home/ubuntu/test2.txt", $post_content, FILE_APPEND);
file_put_contents("/home/ubuntu/test2.txt", "\n", FILE_APPEND);
// Configure the database information.
define('DB_HOST', 'localhost');
define('DB_USER', 'cp');
define('DB_PWD', '123456');
define('DB_NAME', 'cp_trade'); //
//Connect to mysql database.
$connect = mysql_connect(DB_HOST, DB_USER, DB_PWD) or die('数据库连接失败，错误信息：' . mysql_error());
mysql_select_db(DB_NAME, $connect) or die('数据库连接错误，错误信息：' . mysql_error());
mysql_query("SET NAMES 'utf8'", $connect);
//提取数字 前提是只有一次数字，可以从字符串%中提取数字，比如你好123.45或你好123.45%==》123.45
//缺陷：当有两个或者以上数字时，提取到两个数值并且会把两个数值合在一起
function number($str) {
    return preg_replace('/[^\.0123456789]/s', '', $str); //如果有数字，那么返回的是数字，如果没有数字只有字符串则返回的是false
    
}
//对满意度回答给予检查评分，如果是数字并且在1到10的数字，那么通过，此外其他回答都是0分  返回的是分数
function checkIntegral($str_integral) {
    $total = 0;
    if (number($str_integral) != false && number($str_integral) > 0 && number($str_integral) <= 10) {
        $total = $total + number($str_integral);
    } else {
        $total = $total;
    }
    return $total;
}
//get args (action_httppost) from post: action_name
$action_name = from_post('action_name', 'null');
//判断action_httppost进行action逻辑处理与action间的跳转
//对问卷满意度进行评分计算总分$total_integral
$total_integral = 0;
switch ($action_name) {
        //观众个人信息 5个参数，姓名，公司，部门，电话，邮件
        
    case 'audience_httppost':
        $name = from_post('name', 'null');
        $company = from_post('company', 'null');
        $department = from_post('department', 'null');
        $phone = from_post('phone', 'null');
        $email = from_post('email', 'null');
        //存储观众个人信息表  INFORMATION_AUDIENCE
        $query = sprintf("SELECT * FROM `INFORMATION_AUDIENCE` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
            $sql = "INSERT INTO `INFORMATION_AUDIENCE`(`chat_key`,`name`, `company`,`department`, `phone`, `email`) VALUES('$chat_key','$name','$company','$department','$phone','$email')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                $answer = "insert success";
            }
        } else {
            $name = from_post('name', 'null');
            $company = from_post('company', 'null');
            $department = from_post('department', 'null');
            $phone = from_post('phone', 'null');
            $email = from_post('email', 'null');
            if (($name == 'null') && ($data['name'] != 'null')) {
                $name = $data['name'];
            }
            if (($company == 'null') && ($data['company'] != 'null')) {
                $company = $data['company'];
            }
            if (($department == 'null') && ($data['department'] != 'null')) {
                $department = $data['department'];
            }
            if (($phone == 'null') && ($data['phone'] != 'null')) {
                $phone = $data['phone'];
            }
            if (($email == 'null') && ($data['email'] != 'null')) {
                $email = $data['email'];
            }
            $sql = "UPDATE `INFORMATION_AUDIENCE` SET `chat_key`='$chat_key',`name`='$name',`company`='$company',`department`='$department',`phone`='$phone',`email`='$email' WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
        }
        if ($name == 'null') {
            $next = 'name';
            $answer = $next;
        } else if ($company == 'null') {
            $next = 'company';
            $answer = $next;
        } else if ($department == 'null') {
            $next = 'department';
            $answer = $next;
        } else if ($phone == 'null') {
            $next = 'phone';
            $answer = $next;
        } else if ($email == 'null') {
            $next = 'email';
            $answer = $next;
        } else {
            $next = 'finishaudience'; //下一个模块的问答
            $answer = $next;
        }
        $next = 'finishaudience'; //下一个模块的问答
        $answer = $next;
        break;
        //观众满意度评价 14个问题参数
        
    case 'evaluation_httppost':
        $professional = from_post('professional', 'null'); //专业化程度
        $foreign_exhibitors = from_post('foreign_exhibitors', 'null'); //境外展商数量
        $quality = from_post('quality', 'null'); //境外展商质量
        $themes = from_post('themes', 'null'); //展品与主题符合程度
        $specifications = from_post('specifications', 'null'); //展品规格
        $publicity = from_post('publicity', 'null'); //展会宣传力度
        $consultant = from_post('consultant', 'null'); //配套设施及服务方面的现场咨询
        $indicator = from_post('indicator', 'null'); //配套设施及服务方面的指示标识
        $food_beverage = from_post('food_beverage', 'null'); //配套设施及服务方面的餐饮服务
        $scene_order = from_post('scene_order', 'null'); //配套设施及服务方面的现场秩序
        $guest_countries = from_post('guest_countries', 'null'); //主宾国活动及展区
        $seminar = from_post('seminar', 'null'); //论坛或研讨会
        $comprehensive = from_post('comprehensive', 'null'); //展会综合评价
        $prospects = from_post('prospects', 'null'); //展会的发展前景
            //满意度分数统计标准 $total_integral  观众是14个问题*10分  满分100分=$total_integral/14*10
        //对满意度分数计算总和
        $total_integral = checkIntegral($professional) + checkIntegral($foreign_exhibitors) + checkIntegral($quality) + checkIntegral($themes) + checkIntegral($specifications) + checkIntegral($publicity) + checkIntegral($consultant) + checkIntegral($indicator) + checkIntegral($food_beverage) + checkIntegral($scene_order) + checkIntegral($guest_countries) + checkIntegral($seminar) + checkIntegral($comprehensive) + checkIntegral($prospects);
        $total_integral = round($total_integral / 14 * 10, 2); //保留两位小数点
       
        //观众展会满意度评价表evaluation_audience
        $query = sprintf("SELECT * FROM `EVALUATION_AUDIENCE` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
             $sql = "INSERT INTO `EVALUATION_AUDIENCE`(`chat_key`,`professional`, `foreign_exhibitors`,`quality`, `themes`, `specifications`, `publicity`, `consultant`, `indicator`, `food_beverage`, `scene_order`, `guest_countries`, `seminar`, `comprehensive`, `prospects`,`total_integral`) 
        VALUES('$chat_key','$professional','$foreign_exhibitors','$quality','$themes','$specifications','$publicity','$consultant','$indicator','$food_beverage','$scene_order','$guest_countries','$seminar','$comprehensive','$prospects','$total_integral')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                //当成功存储到满意度评价的时候，同时存储满意度统计总分
                $answer = "insert success";
            }
            
        } else {
            $professional = from_post('professional', 'null'); //专业化程度
            $foreign_exhibitors = from_post('foreign_exhibitors', 'null'); //境外展商数量
            $quality = from_post('quality', 'null'); //境外展商质量
            $themes = from_post('themes', 'null'); //展品与主题符合程度
            $specifications = from_post('specifications', 'null'); //展品规格
            $publicity = from_post('publicity', 'null'); //展会宣传力度
            $consultant = from_post('consultant', 'null'); //配套设施及服务方面的现场咨询
            $indicator = from_post('indicator', 'null'); //配套设施及服务方面的指示标识
            $food_beverage = from_post('food_beverage', 'null'); //配套设施及服务方面的餐饮服务
            $scene_order = from_post('scene_order', 'null'); //配套设施及服务方面的现场秩序
            $guest_countries = from_post('guest_countries', 'null'); //主宾国活动及展区
            $seminar = from_post('seminar', 'null'); //论坛或研讨会
            $comprehensive = from_post('comprehensive', 'null'); //展会综合评价
            $prospects = from_post('prospects', 'null'); //展会的发展前景
            if (($professional == 'null') && ($data['professional'] != 'null')) {
                $professional = $data['professional'];
            }
            if (($foreign_exhibitors == 'null') && ($data['foreign_exhibitors'] != 'null')) {
                $foreign_exhibitors = $data['foreign_exhibitors'];
            }
            if (($quality == 'null') && ($data['quality'] != 'null')) {
                $quality = $data['quality'];
            }
            if (($themes == 'null') && ($data['themes'] != 'null')) {
                $themes = $data['themes'];
            }
            if (($specifications == 'null') && ($data['specifications'] != 'null')) {
                $specifications = $data['specifications'];
            }
            if (($publicity == 'null') && ($data['publicity'] != 'null')) {
                $publicity = $data['publicity'];
            }
            if (($consultant == 'null') && ($data['consultant'] != 'null')) {
                $consultant = $data['consultant'];
            }
            if (($indicator == 'null') && ($data['indicator'] != 'null')) {
                $indicator = $data['indicator'];
            }
            if (($food_beverage == 'null') && ($data['food_beverage'] != 'null')) {
                $food_beverage = $data['food_beverage'];
            }
            if (($scene_order == 'null') && ($data['scene_order'] != 'null')) {
                $scene_order = $data['scene_order'];
            }
            if (($guest_countries == 'null') && ($data['guest_countries'] != 'null')) {
                $guest_countries = $data['guest_countries'];
            }
            if (($seminar == 'null') && ($data['seminar'] != 'null')) {
                $seminar = $data['seminar'];
            }
            if (($comprehensive == 'null') && ($data['comprehensive'] != 'null')) {
                $comprehensive = $data['comprehensive'];
            }
            if (($prospects == 'null') && ($data['prospects'] != 'null')) {
                $prospects = $data['prospects'];
            }
          $sql = "UPDATE `EVALUATION_AUDIENCE` SET `chat_key`='$chat_key',`professional`='$professional',`foreign_exhibitors`='$foreign_exhibitors',`quality`='$quality',`themes`='$themes',`specifications`='$specifications',`publicity`='$publicity',`consultant`='$consultant',`indicator`='$indicator',`food_beverage`='$food_beverage',`scene_order`='$scene_order',`seminar`='$seminar',`comprehensive`='$comprehensive',`scene_order`='$scene_order',`prospects`='$prospects',`total_integral`='$total_integral' WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
         
        }
        if ($professional == 'null') {
            $next = 'professional';
            $answer = $next;
        } else if ($foreign_exhibitors == 'null') {
            $next = 'foreign_exhibitors';
            $answer = $next;
        } else if ($quality == 'null') {
            $next = 'quality';
            $answer = $next;
        } else if ($themes == 'null') {
            $next = 'themes';
            $answer = $next;
        } else if ($specifications == 'null') {
            $next = 'specifications';
            $answer = $next;
        } else if ($publicity == 'null') {
            $next = 'publicity';
            $answer = $next;
        } else if ($consultant == 'null') {
            $next = 'consultant';
            $answer = $next;
        } else if ($indicator == 'null') {
            $next = 'indicator';
            $answer = $next;
        } else if ($food_beverage == 'null') {
            $next = 'food_beverage';
            $answer = $next;
        } else if ($scene_order == 'null') {
            $next = 'scene_order';
            $answer = $next;
        } else if ($guest_countries == 'null') {
            $next = 'guest_countries';
            $answer = $next;
        } else if ($seminar == 'null') {
            $next = 'seminar';
            $answer = $next;
        } else if ($comprehensive == 'null') {
            $next = 'comprehensive';
            $answer = $next;
        } else if ($prospects == 'null') {
            $next = 'prospects';
            $answer = $next;
        } else {
            $next = 'finishexhibition'; //下一个模块的问答
            $answer = $next;
        }
        $total_integral=0;
        $next = 'finishexhibition'; //下一个模块的问答
        $answer = $next;
        
        break;
        //观众展会相关信息  9个问题参数
        
    case 'exhibition_audience_httppost':
        //单选情况，不存在多选时
        $channel = from_post('channel', 'null'); //渠道深知本届展会的
        $other_channel = from_post('other_channel', 'null'); //如果 $channel!='其他'，这是存储参数：从其他什么渠道知道本届展会的
        $impressive_thems = from_post('impressive_thems', 'null'); //印象深刻的主题内容
        $increase_theme = from_post('increase_theme', 'null'); //希望增加哪些印象深刻的主题内容
        $target = from_post('target', 'null'); //观展目标有哪些
        $other_target = from_post('other_target', 'null'); ////如果 $other_target!='其他'，这是存储参数：观展的其他目标是什么？
        $implementation = from_post('implementation', 'null'); //观战目标实现情况如何？比如：实现，未实现
        $is_joinlast = from_post('is_joinlast', 'null'); //是否参加了上一届参会
        $is_joinnext = from_post('is_joinnext', 'null'); //是否有意向参加下一届展会
        //观众展会相关信息表exhibition_audience
        $query = sprintf("SELECT * FROM `EXHIBITION_AUDIENCE` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
            $sql = "INSERT INTO `EXHIBITION_AUDIENCE`(`chat_key`,`channel`, `other_channel`,`impressive_thems`, `increase_theme`, `target`, `other_target`, `implementation`, `is_joinlast`, `is_joinnext`) 
        VALUES('$chat_key','$channel','$other_channel','$impressive_thems','$increase_theme','$target','$other_target','$implementation','$is_joinlast','$is_joinnext')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                $answer = "insert success";
            }
        } else {
            $channel = from_post('channel', 'null'); //渠道深知本届展会的
            $other_channel = from_post('other_channel', 'null'); //如果 $channel!='其他'，这是存储参数：从其他什么渠道知道本届展会的
            $impressive_thems = from_post('impressive_thems', 'null'); //印象深刻的主题内容
            $increase_theme = from_post('increase_theme', 'null'); //希望增加哪些印象深刻的主题内容
            $target = from_post('target', 'null'); //观展目标有哪些
            $other_target = from_post('other_target', 'null'); ////如果 $other_target!='其他'，这是存储参数：观展的其他目标是什么？
            $implementation = from_post('implementation', 'null'); //观战目标实现情况如何？比如：实现，未实现
            $is_joinlast = from_post('is_joinlast', 'null'); //是否参加了上一届参会
            $is_joinnext = from_post('is_joinnext', 'null'); //是否有意向参加下一届展会
            if (($channel == 'null') && ($data['channel'] != 'null')) {
                $channel = $data['channel'];
            }
            if (($other_channel == 'null') && ($data['other_channel'] != 'null')) {
                $other_channel = $data['other_channel'];
            }
            if (($impressive_thems == 'null') && ($data['impressive_thems'] != 'null')) {
                $impressive_thems = $data['impressive_thems'];
            }
            if (($increase_theme == 'null') && ($data['increase_theme'] != 'null')) {
                $increase_theme = $data['increase_theme'];
            }
            if (($target == 'null') && ($data['target'] != 'null')) {
                $target = $data['target'];
            }
            if (($other_target == 'null') && ($data['other_target'] != 'null')) {
                $other_target = $data['other_target'];
            }
            if (($implementation == 'null') && ($data['implementation'] != 'null')) {
                $implementation = $data['implementation'];
            }
            if (($is_joinlast == 'null') && ($data['is_joinlast'] != 'null')) {
                $is_joinlast = $data['is_joinlast'];
            }
            if (($is_joinnext == 'null') && ($data['is_joinnext'] != 'null')) {
                $is_joinnext = $data['is_joinnext'];
            }
            $sql = "UPDATE `EXHIBITION_AUDIENCE` SET `chat_key`='$chat_key',`channel`='$channel',`other_channel`='$other_channel',`impressive_thems`='$impressive_thems',`increase_theme`='$increase_theme',`target`='$target',`other_target`='$other_target',`implementation`='$implementation',`is_joinlast`='$is_joinlast',`is_joinnext`='$is_joinnext' WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
        }
        if ($channel == 'null') {
            $next = 'channel';
            $answer = $next;
        } else if ($other_channel == 'null') {
            $next = 'other_channel';
            $answer = $next;
        } else if ($impressive_thems == 'null') {
            $next = 'impressive_thems';
            $answer = $next;
        } else if ($increase_theme == 'null') {
            $next = 'increase_theme';
            $answer = $next;
        } else if ($target == 'null') {
            $next = 'target';
            $answer = $next;
        } else if ($other_target == 'null') {
            $next = 'other_target';
            $answer = $next;
        } else if ($implementation == 'null') {
            $next = 'implementation';
            $answer = $next;
        } else if ($is_joinlast == 'null') {
            $next = 'is_joinlast';
            $answer = $next;
        } else if ($is_joinnext == 'null') {
            $next = 'is_joinnext';
            $answer = $next;
        } else {
            $next = 'finish'; //下一个模块是结束语
            $answer = $next;
        }
        $next = 'finish'; //下一个模块的问答
        $answer = $next;
        break;
        //以下是参展商action
        //参展商个人信息 6参数，参展号，姓名，公司，职位，电话，邮件
        
    case 'exhibitor_httppost':
        $name_exhibitors = from_post('name_exhibitors', 'null'); //姓名
        $company_exhibitors = from_post('company_exhibitors', 'null'); //参展单位
        $booth = from_post('booth', 'null'); //展位号
        $phone_exhibitors = from_post('phone_exhibitors', 'null'); //电话
        $email_exhibitors = from_post('email_exhibitors', 'null'); //电子邮件
        $position = from_post('position', 'null'); //职位
        //存储观众个人信息表EXHIBITORS_INFO
        $query = sprintf("SELECT * FROM `INFORMATION_EXHIBITORS` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
            $sql = "INSERT INTO `INFORMATION_EXHIBITORS`(`chat_key`,`name_exhibitors`, `company_exhibitors`,`booth`, `phone_exhibitors`, `email_exhibitors`, `position`) VALUES('$chat_key','$name_exhibitors','$company_exhibitors','$booth','$phone_exhibitors','$email_exhibitors','$position')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                $answer = "insert success";
            }
        } else {
            $name_exhibitors = from_post('name_exhibitors', 'null'); //姓名
            $company_exhibitors = from_post('company_exhibitors', 'null'); //参展单位
            $booth = from_post('booth', 'null'); //展位号
            $phone_exhibitors = from_post('phone_exhibitors', 'null'); //电话
            $email_exhibitors = from_post('email_exhibitors', 'null'); //电子邮件
            $position = from_post('position', 'null'); //职位
            if (($name_exhibitors == 'null') && ($data['name_exhibitors'] != 'null')) {
                $name_exhibitors = $data['name_exhibitors'];
            }
            if (($company_exhibitors == 'null') && ($data['company_exhibitors'] != 'null')) {
                $company_exhibitors = $data['company_exhibitors'];
            }
            if (($booth == 'null') && ($data['booth'] != 'null')) {
                $booth = $data['booth'];
            }
            if (($phone_exhibitors == 'null') && ($data['phone_exhibitors'] != 'null')) {
                $phone_exhibitors = $data['phone_exhibitors'];
            }
            if (($email_exhibitors == 'null') && ($data['email_exhibitors'] != 'null')) {
                $email_exhibitors = $data['email_exhibitors'];
            }
            if (($position == 'null') && ($data['position'] != 'null')) {
                $position = $data['position'];
            }
            $sql = "UPDATE `INFORMATION_EXHIBITORS` SET `chat_key`='$chat_key',`name_exhibitors`='$name_exhibitors',`company_exhibitors`='$company_exhibitors',`booth`='$booth',`phone_exhibitors`='$phone_exhibitors',`email_exhibitors`='$email_exhibitors',`position`='$position' WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
        }
        if ($name_exhibitors == 'null') {
            $next = 'name_exhibitors';
            $answer = $next;
        } else if ($company_exhibitors == 'null') {
            $next = 'company_exhibitors';
            $answer = $next;
        } else if ($booth == 'null') {
            $next = 'booth';
            $answer = $next;
        } else if ($phone_exhibitors == 'null') {
            $next = 'phone_exhibitors';
            $answer = $next;
        } else if ($email_exhibitors == 'null') {
            $next = 'email_exhibitors';
            $answer = $next;
        } else if ($position == 'null') {
            $next = 'position';
            $answer = $next;
        } else {
            $next = 'finishexhibitors'; //下一个模块的问答
            $answer = $next;
        }
        $next = 'finishexhibitors'; //下一个模块的问答
        $answer = $next;
        break;
        //参展商满意度评价 16个问题参数
        
    case 'evaluation_exhibitors_httppost':
        $professional_exhibitors = from_post('professional_exhibitors', 'null'); //专业化程度
        $foreign_audience = from_post('foreign_audience', 'null'); //境外观众数量
        $quality_exhibitors = from_post('quality_exhibitors', 'null'); //境外观众质量
        $themes_exhibitors = from_post('themes_exhibitors', 'null'); //专业组织情况
        $specifications_exhibitors = from_post('specifications_exhibitors', 'null'); //展区分布
        $publicity_exhibitors = from_post('publicity_exhibitors', 'null'); //展会组织宣传力度
        $transportation = from_post('transportation', 'null'); //配套服务方面的展品运输
        $reception_exhibitors = from_post('reception_exhibitors', 'null'); //配套服务方面的展商接待
        $consultant_exhibitors = from_post('consultant_exhibitors', 'null'); //配套设施及服务方面的现场咨询
        $indicator_exhibitors = from_post('indicator_exhibitors', 'null'); //配套设施及服务方面的指示标识
        $food_beverage_exhibitors = from_post('food_beverage_exhibitors', 'null'); //配套设施及服务方面的餐饮服务
        $scene_order_exhibitors = from_post('scene_order_exhibitors', 'null'); //配套设施及服务方面的现场秩序
        $guest_countries_exhibitors = from_post('guest_countries_exhibitors', 'null'); //主宾国活动及展区
        $seminar_exhibitors = from_post('seminar_exhibitors', 'null'); //论坛或研讨会
        $comprehensive_exhibitors = from_post('comprehensive_exhibitors', 'null'); //展会综合评价
        $prospects_exhibitors = from_post('prospects_exhibitors', 'null'); //展会的发展前景

      //满意度分数统计标准 $total_integral  参展商是16个问题*10分  满分100分=$total_integral/16*10
        //对满意度分数计算总和
        $total_integral = checkIntegral($professional_exhibitors) + checkIntegral($foreign_audience) + checkIntegral($quality_exhibitors) +checkIntegral($themes_exhibitors) + checkIntegral($specifications_exhibitors) + checkIntegral($publicity_exhibitors) + checkIntegral($transportation) +checkIntegral($reception_exhibitors) + checkIntegral($consultant_exhibitors) + checkIntegral($indicator_exhibitors) + checkIntegral($food_beverage_exhibitors) +checkIntegral($scene_order_exhibitors) + checkIntegral($scene_order_exhibitors) + checkIntegral($seminar_exhibitors)+ checkIntegral($comprehensive_exhibitors)+ checkIntegral($prospects_exhibitors);
        $total_integral = round($total_integral / 16 * 10, 2); //保留两位小数点
       

        $query = sprintf("SELECT * FROM `EVALUATION_EXHIBITORS` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
             $sql = "INSERT INTO `EVALUATION_EXHIBITORS`(`chat_key`,`professional_exhibitors`, `foreign_audience`,`quality_exhibitors`, `themes_exhibitors`,`transportation`, `reception_exhibitors`, `specifications_exhibitors`, `publicity_exhibitors`, `consultant_exhibitors`, `indicator_exhibitors`, `food_beverage_exhibitors`, `scene_order_exhibitors`, `guest_countries_exhibitors`, `seminar_exhibitors`, `comprehensive_exhibitors`, `prospects_exhibitors`,`total_integral`)
            VALUES('$chat_key','$professional_exhibitors','$foreign_audience','$quality_exhibitors','$themes_exhibitors','$transportation','$reception_exhibitors','$specifications_exhibitors','$publicity_exhibitors','$consultant_exhibitors','$indicator_exhibitors','$food_beverage_exhibitors','$scene_order_exhibitors','$guest_countries_exhibitors','$seminar_exhibitors','$comprehensive_exhibitors','$prospects_exhibitors','$total_integral')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                $answer = "insert success";
            }
        } else {
            $professional_exhibitors = from_post('professional_exhibitors', 'null'); //专业化程度
            $foreign_audience = from_post('foreign_audience', 'null'); //境外观众数量
            $quality_exhibitors = from_post('quality_exhibitors', 'null'); //境外观众质量
            $themes_exhibitors = from_post('themes_exhibitors', 'null'); //专业组织情况
            $specifications_exhibitors = from_post('specifications_exhibitors', 'null'); //展区分布
            $publicity_exhibitors = from_post('publicity_exhibitors', 'null'); //展会组织宣传力度
            $transportation = from_post('transportation', 'null'); //配套服务方面的展品运输
            $reception_exhibitors = from_post('reception_exhibitors', 'null'); //配套服务方面的展商接待
            $consultant_exhibitors = from_post('consultant_exhibitors', 'null'); //配套设施及服务方面的现场咨询
            $indicator_exhibitors = from_post('indicator_exhibitors', 'null'); //配套设施及服务方面的指示标识
            $food_beverage_exhibitors = from_post('food_beverage_exhibitors', 'null'); //配套设施及服务方面的餐饮服务
            $scene_order_exhibitors = from_post('scene_order_exhibitors', 'null'); //配套设施及服务方面的现场秩序
            $guest_countries_exhibitors = from_post('guest_countries_exhibitors', 'null'); //主宾国活动及展区
            $seminar_exhibitors = from_post('seminar_exhibitors', 'null'); //论坛或研讨会
            $comprehensive_exhibitors = from_post('comprehensive_exhibitors', 'null'); //展会综合评价
            $prospects_exhibitors = from_post('prospects_exhibitors', 'null'); //展会的发展前景
            if (($professional_exhibitors == 'null') && ($data['professional_exhibitors'] != 'null')) {
                $professional_exhibitors = $data['professional_exhibitors'];
            }
            if (($foreign_audience == 'null') && ($data['foreign_audience'] != 'null')) {
                $foreign_audience = $data['foreign_audience'];
            }
            if (($quality_exhibitors == 'null') && ($data['quality_exhibitors'] != 'null')) {
                $quality_exhibitors = $data['quality_exhibitors'];
            }
            if (($themes_exhibitors == 'null') && ($data['themes_exhibitors'] != 'null')) {
                $themes_exhibitors = $data['themes_exhibitors'];
            }
            if (($specifications_exhibitors == 'null') && ($data['specifications_exhibitors'] != 'null')) {
                $specifications_exhibitors = $data['specifications_exhibitors'];
            }
            if (($publicity_exhibitors == 'null') && ($data['publicity_exhibitors'] != 'null')) {
                $publicity_exhibitors = $data['publicity_exhibitors'];
            }
            if (($transportation == 'null') && ($data['transportation'] != 'null')) {
                $transportation = $data['transportation'];
            }
            if (($reception_exhibitors == 'null') && ($data['reception_exhibitors'] != 'null')) {
                $reception_exhibitors = $data['reception_exhibitors'];
            }
            if (($consultant_exhibitors == 'null') && ($data['consultant_exhibitors'] != 'null')) {
                $consultant_exhibitors = $data['consultant_exhibitors'];
            }
            if (($indicator_exhibitors == 'null') && ($data['indicator_exhibitors'] != 'null')) {
                $indicator_exhibitors = $data['indicator_exhibitors'];
            }
            if (($food_beverage_exhibitors == 'null') && ($data['food_beverage_exhibitors'] != 'null')) {
                $food_beverage_exhibitors = $data['food_beverage_exhibitors'];
            }
            if (($scene_order_exhibitors == 'null') && ($data['scene_order_exhibitors'] != 'null')) {
                $scene_order_exhibitors = $data['scene_order_exhibitors'];
            }
            if (($guest_countries_exhibitors == 'null') && ($data['guest_countries_exhibitors'] != 'null')) {
                $guest_countries_exhibitors = $data['guest_countries_exhibitors'];
            }
            if (($seminar_exhibitors == 'null') && ($data['seminar_exhibitors'] != 'null')) {
                $seminar_exhibitors = $data['seminar_exhibitors'];
            }
            if (($comprehensive_exhibitors == 'null') && ($data['comprehensive_exhibitors'] != 'null')) {
                $comprehensive_exhibitors = $data['comprehensive_exhibitors'];
            }
            if (($prospects_exhibitors == 'null') && ($data['prospects_exhibitors'] != 'null')) {
                $prospects_exhibitors = $data['prospects_exhibitors'];
            }
           $sql = "UPDATE `EVALUATION_EXHIBITORS` SET `chat_key`='$chat_key',`professional_exhibitors`='$professional_exhibitors',`foreign_audience`='$foreign_audience',`quality_exhibitors`='$tra',`themes_exhibitors`='$themes_exhibitors',`transportation`='$transportation',`reception_exhibitors`='$reception_exhibitors',`specifications_exhibitors`='$specifications_exhibitors',`publicity_exhibitors`='$publicity_exhibitors',`consultant_exhibitors`='$consultant_exhibitors',`indicator_exhibitors`='$indicator_exhibitors',`food_beverage_exhibitors`='$food_beverage_exhibitors',`scene_order_exhibitors`='$scene_order_exhibitors',`seminar_exhibitors`='$seminar_exhibitors',`comprehensive_exhibitors`='$comprehensive_exhibitors',`scene_order_exhibitors`='$scene_order_exhibitors',`prospects_exhibitors`='$prospects_exhibitors',`total_integral`='$total_integral'  WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
         
        }
        if ($professional_exhibitors == 'null') {
            $next = 'professional_exhibitors';
            $answer = $next;
        } else if ($foreign_audience == 'null') {
            $next = 'foreign_audience';
            $answer = $next;
        } else if ($quality_exhibitors == 'null') {
            $next = 'quality_exhibitors';
            $answer = $next;
        } else if ($themes_exhibitors == 'null') {
            $next = 'themes_exhibitors';
            $answer = $next;
        } else if ($specifications_exhibitors == 'null') {
            $next = 'specifications_exhibitors';
            $answer = $next;
        } else if ($transportation == 'null') {
            $next = 'transportation';
            $answer = $next;
        } else if ($reception_exhibitors == 'null') {
            $next = 'reception_exhibitors';
            $answer = $next;
        } else if ($publicity_exhibitors == 'null') {
            $next = 'publicity_exhibitors';
            $answer = $next;
        } else if ($consultant_exhibitors == 'null') {
            $next = 'consultant_exhibitors';
            $answer = $next;
        } else if ($indicator_exhibitors == 'null') {
            $next = 'indicator_exhibitors';
            $answer = $next;
        } else if ($food_beverage_exhibitors == 'null') {
            $next = 'food_beverage_exhibitors';
            $answer = $next;
        } else if ($scene_order_exhibitors == 'null') {
            $next = 'scene_order_exhibitors';
            $answer = $next;
        } else if ($guest_countries_exhibitors == 'null') {
            $next = 'guest_countries_exhibitors';
            $answer = $next;
        } else if ($seminar_exhibitors == 'null') {
            $next = 'seminar_exhibitors';
            $answer = $next;
        } else if ($comprehensive_exhibitors == 'null') {
            $next = 'comprehensive_exhibitors';
            $answer = $next;
        } else if ($prospects_exhibitors == 'null') {
            $next = 'prospects_exhibitors';
            $answer = $next;
        } else {
            $next = 'finishappraise'; //下一个模块的问答
            $answer = $next;
        }
        $total_integral=0;
        $next = 'finishappraise'; //下一个模块的问答
        $answer = $next;
        break;
        //展商展会相关信息  11个问题参数
        
    case 'exhibition_exhibitors_httppost':
        //单选情况，不存在多选时
        $type_exhibitors = from_post('type_exhibitors', 'null'); //贵企业的参展类型
        $other_type_exhibitors = from_post('other_type_exhibitors', 'null'); //如果 $type_exhibitors!='展团或联合参展'，这是存储参数:所属展团或联合展商名称
        $industry = from_post('industry', 'null'); //贵企业业务涉及哪些行业
        $other_industry = from_post('other_industry', 'null'); ////如果 $other_industry!='其他'，这是存储参数：说明涉及的其他行业是什么？
        $increase_theme_exhibitors = from_post('increase_theme_exhibitors', 'null'); //希望增加哪些印象深刻的主题内容
        $target_exhibitors = from_post('target_exhibitors', 'null'); //参展目标有哪些
        $other_target_exhibitors = from_post('other_target_exhibitors', 'null'); ////如果 $other_target_exhibitors!='其他'，这是存储参数：说明其他目标是什么？
        $implementation_exhibitors = from_post('implementation_exhibitors', 'null'); //参展战目标实现情况如何？比如：实现，未实现
        $is_cooperation = from_post('is_cooperation', 'null'); //有没有达成合作意向？比如：有，没有
        $is_order = from_post('is_order', 'null'); //有没有接到订单？比如：有，没有
        $is_joinlater = from_post('is_joinlater', 'null'); //是否有意向参加下一届展会？比如：是，否，不一定
        //参展商展会相关信息表exhibition_audience
        $query = sprintf("SELECT * FROM `EXHIBITION_EXHIBITORS` WHERE `chat_key` = '%s'", $chat_key);
        $result = mysql_query($query) or die('sql语句错误：' . mysql_error());
        $data = mysql_fetch_array($result);
        if ($data == false) {
            $sql = "INSERT INTO `EXHIBITION_EXHIBITORS`(`chat_key`,`type_exhibitors`, `other_type_exhibitors`,`industry`, `other_industry`,`increase_theme_exhibitors`, `target_exhibitors`, `other_target_exhibitors`, `implementation_exhibitors`, `is_cooperation`, `is_order`, `is_joinlater`) 
      VALUES('$chat_key','$type_exhibitors','$other_type_exhibitors','$industry','$other_industry','$increase_theme_exhibitors','$target_exhibitors','$other_target_exhibitors','$implementation_exhibitors','$is_cooperation','$is_order','$is_joinlater')";
            if (!mysql_query($sql, $connect)) {
                $answer = "insert fail" . mysql_error() . " " . $sql;
            } else {
                $answer = "insert success";
            }
        } else {
            //单选情况，不存在多选时
            $type_exhibitors = from_post('type_exhibitors', 'null'); //贵企业的参展类型
            $other_type_exhibitors = from_post('other_type_exhibitors', 'null'); //如果 $type_exhibitors!='展团或联合参展'，这是存储参数:所属展团或联合展商名称
            $industry = from_post('industry', 'null'); //贵企业业务涉及哪些行业
            $other_industry = from_post('other_industry', 'null'); ////如果 $other_industry!='其他'，这是存储参数：说明涉及的其他行业是什么？
            $increase_theme_exhibitors = from_post('increase_theme_exhibitors', 'null'); //希望增加哪些印象深刻的主题内容
            $target_exhibitors = from_post('target_exhibitors', 'null'); //参展目标有哪些
            $other_target_exhibitors = from_post('other_target_exhibitors', 'null'); ////如果 $other_target_exhibitors!='其他'，这是存储参数：说明其他目标是什么？
            $implementation_exhibitors = from_post('implementation_exhibitors', 'null'); //参展战目标实现情况如何？比如：实现，未实现
            $is_cooperation = from_post('is_cooperation', 'null'); //有没有达成合作意向？比如：有，没有
            $is_order = from_post('is_order', 'null'); //有没有接到订单？比如：有，没有
            $is_joinlater = from_post('is_joinlater', 'null'); //是否有意向参加下一届展会？比如：是，否，不一定
            if (($type_exhibitors == 'null') && ($data['type_exhibitors'] != 'null')) {
                $type_exhibitors = $data['type_exhibitors'];
            }
            if (($other_type_exhibitors == 'null') && ($data['other_type_exhibitors'] != 'null')) {
                $other_type_exhibitors = $data['other_type_exhibitors'];
            }
            if (($industry == 'null') && ($data['industry'] != 'null')) {
                $industry = $data['industry'];
            }
            if (($other_industry == 'null') && ($data['other_industry'] != 'null')) {
                $other_industry = $data['other_industry'];
            }
            if (($increase_theme_exhibitors == 'null') && ($data['increase_theme_exhibitors'] != 'null')) {
                $increase_theme_exhibitors = $data['increase_theme_exhibitors'];
            }
            if (($target_exhibitors == 'null') && ($data['target_exhibitors'] != 'null')) {
                $target_exhibitors = $data['target_exhibitors'];
            }
            if (($other_target_exhibitors == 'null') && ($data['other_target_exhibitors'] != 'null')) {
                $other_target_exhibitors = $data['other_target_exhibitors'];
            }
            if (($implementation_exhibitors == 'null') && ($data['implementation_exhibitors'] != 'null')) {
                $implementation_exhibitors = $data['implementation_exhibitors'];
            }
            if (($is_cooperation == 'null') && ($data['is_cooperation'] != 'null')) {
                $is_cooperation = $data['is_cooperation'];
            }
            if (($is_order == 'null') && ($data['is_order'] != 'null')) {
                $is_order = $data['is_order'];
            }
            if (($is_joinlater == 'null') && ($data['is_joinlater'] != 'null')) {
                $is_joinlater = $data['is_joinlater'];
            }
            $sql = "UPDATE `EXHIBITION_EXHIBITORS` SET `chat_key`='$chat_key',`type_exhibitors`='$type_exhibitors',`other_type_exhibitors`='$other_type_exhibitors',`industry`='$industry',`other_industry`='$other_industry',`increase_theme_exhibitors`='$increase_theme_exhibitors',`target_exhibitors`='$target_exhibitors',`other_target_exhibitors`='$other_target_exhibitors',`implementation_exhibitors`='$implementation_exhibitors',`is_cooperation`='$is_cooperation',`is_order`='$is_order',`is_joinlater`='$is_joinlater' WHERE `chat_key`='$chat_key'";
            if (!mysql_query($sql, $connect)) {
                $answer = "update fail " . mysql_error() . " " . $sql;
            } else {
                $answer = "update success";
            }
        }
        if ($type_exhibitors == 'null') {
            $next = 'type_exhibitors';
            $answer = $next;
        } else if ($other_type_exhibitors == 'null') {
            $next = 'other_type_exhibitors';
            $answer = $next;
        } else if ($increase_theme_exhibitors == 'null') {
            $next = 'increase_theme_exhibitors';
            $answer = $next;
        } else if ($industry == 'null') {
            $next = 'industry';
            $answer = $next;
        } else if ($other_industry == 'null') {
            $next = 'other_industry';
            $answer = $next;
        } else if ($target_exhibitors == 'null') {
            $next = 'target_exhibitors';
            $answer = $next;
        } else if ($other_target_exhibitors == 'null') {
            $next = 'other_target_exhibitors';
            $answer = $next;
        } else if ($implementation_exhibitors == 'null') {
            $next = 'implementation_exhibitors';
            $answer = $next;
        } else if ($is_cooperation == 'null') {
            $next = 'is_cooperation';
            $answer = $next;
        } else if ($is_order == 'null') {
            $next = 'is_order';
            $answer = $next;
        } else if ($is_joinlater == 'null') {
            $next = 'is_joinlater';
            $answer = $next;
        } else {
            $next = 'finish'; //下一个模块是结束语
            $answer = $next;
        }
        $next = 'finish'; //下一个模块是结束语
        $answer = $next;
        break;
    }
?>