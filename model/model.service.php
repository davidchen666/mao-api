<?php
class ServiceModel extends AgentModel
{
    /**
     * 上传文件
     */
//     public function uploadUserFile()
//     {
//         $targetFolder = UPLOAD_PATH;
//         $rs = 'error';
//         if (!empty($_FILES)) {
//             $tempFile = $_FILES['Filedata']['tmp_name'];
//             $targetPath = $targetFolder;
//             $fileName = time() . '_' . $_FILES['Filedata']['name'];
//             $targetFile = rtrim($targetPath, '/') . '/' . $fileName;
// //            echo $targetFile;
//             $fileTypes = array('xls', 'xlsx', 'jpg', 'jpeg', 'avi', 'mp4', 'rar', 'doc', 'docx', 'png');
//             $fileParts = pathinfo($_FILES['Filedata']['name']);
//             if (in_array($fileParts['extension'], $fileTypes)) {
//                 move_uploaded_file($tempFile, $targetFile);

//                 $rs = $fileName;
//             } else {
//                 $rs = 'error';
//             }
//         }
//         return $rs;
//     }

    //上传图片: 1,子文件夹名
    public function uploadFile($pathName){
        if(!$pathName){
            return to_error("失败！缺少路径参数。");
            exit;
        }
        $url = dirname(dirname(__FILE__))."/uploads/".$pathName."/";//文件路径
        //检查路径是否存在
        if(!is_dir($url)){
            mkdir ($url,0777,true);
        }
        if(!is_dir($url)){
            return to_error("路径不存在。".$url);
            exit;
        }

        $file = $_FILES['file'];
        $name = $file['name'];
        $type = $file['type'];
        $size = $file['size'];
        $tmp_name = $file['tmp_name'];
        
        $tpname = substr(strrchr($name,'.'),1);//获取文件后缀
        $pre_str = date("YmdHis",time()).'-'.rand(1,99999);
        // $tmp_url = $url.$pre_str.$name;
        //重新生成不包含中文的文件名称（要求不重合）
        $name = $pre_str.'.'.$tpname;
        $tmp_url = $url.$pre_str.$name;
        // date("YmdHis",time())+rand(1,99999);
        $types = array('jpg','png','jpeg','bmp','gif');
        $filesize = 1024 * 1024 * 100;
        if($size > $filesize){
            //              echo "<script>alert('退出成功!');location.href='".$_SERVER["HTTP_REFERER"]."';</script>";
            // echo "'文件过大!";
            return to_error("文件过大!");
            exit;
        }else if(!in_array($tpname,$types)){
            // echo "文件类型不符合!";
            return to_error("文件类型不符合!");
            exit;
        }else if(!move_uploaded_file($tmp_name,$tmp_url)){
            // echo "移动文件失败!";
            // var_dump($tmp_name,$tmp_url);
            return to_error("移动文件失败!(请检查文件名是否合法)");
            exit;
        }else{
            move_uploaded_file($tmp_name,$tmp_url);
            $size = round($size/1024/1024,2); //转换成Mb
            $upload = array('size' => $size, 'url' => $tmp_url, 'name' => $name,'newname'=>$pre_str.$name, 'type' => $tpname);
            // var_dump($upload);
            // return $upload;
            return to_success($upload);
        }
    }

    //发送邮件
    public function sendMail($addNum,$connectNum,$nextNum){
        //当前年月日
        $nowDate = date("Y-m-d",time());
        $downUrl = 'http://mao.dxx-tech.top/mysql_backup/'.date("Ymd",time()).'.sql.gz';
        // 实例化PHPMailer核心类
        $mail = new PHPMailer();
        // 是否启用smtp的debug进行调试 开发环境建议开启 生产环境注释掉即可 默认关闭debug调试模式
        $mail->SMTPDebug = 1;
        // 使用smtp鉴权方式发送邮件
        $mail->isSMTP();
        // smtp需要鉴权 这个必须是true
        $mail->SMTPAuth = true;
        // 链接qq域名邮箱的服务器地址
        $mail->Host = 'smtp.qq.com';
        // 设置使用ssl加密方式登录鉴权
        $mail->SMTPSecure = 'ssl';
        // 设置ssl连接smtp服务器的远程服务器端口号
        $mail->Port = 465;
        // 设置发送的邮件的编码
        $mail->CharSet = 'UTF-8';
        // 设置发件人昵称 显示在收件人邮件的发件人邮箱地址前的发件人姓名
        $mail->FromName = '大猩猩';
        // smtp登录的账号 QQ邮箱即可
        $mail->Username = 'davidchen666@qq.com';
        // smtp登录的密码 使用生成的授权码
        // $mail->Password = 'ldvosjawnsgrbide';
        // $mail->Password = 'ovfczulhudbhbdfc';
        $mail->Password = 'iojqdxkkecjsdedj';
        // 设置发件人邮箱地址 同登录账号
        $mail->From = 'davidchen666@qq.com';
        // 邮件正文是否为html编码 注意此处是一个方法
        $mail->isHTML(true);
        // 设置收件人邮箱地址
        // $mail->addAddress('1281366591@qq.com');
        // 添加多个收件人 则多次调用方法即可
        $mail->addAddress('davidchen666@qq.com');
        // 添加该邮件的主题
        $mail->Subject = '备份数据数据库提醒-'.$nowDate;
        // 添加邮件正文
        $mail->Body = '<p>今日新增客户数目：'.$addNum.'；</p><p>今日联系客户数目：'.$connectNum.'；</p><p>明日联系客户数目：'.$nextNum.'；</p><p>数据已备份成功，邮件附件已发送，请放心使用。</p><p>备份地址：' . $downUrl . '</p><p><a href="'. $downUrl .'" title="">点击下载</a></p>';
        // return $mail->Body;die();
        // 为该邮件添加附件
        $mail->addAttachment('../mysql_backup/'.date("Ymd",time()).'.sql.gz');
        // 发送邮件 返回状态
        $status = $mail->send();
        if($status === true){
            return to_success('发送成功');
        }else{
            return to_error('发送失败，请联系大猩猩');
        }
    }

}