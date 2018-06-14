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
}