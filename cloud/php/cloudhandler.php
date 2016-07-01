<script type="text/javascript" src="../js/cloud.js"></script>
<?php
    //上传图片
    function upload_pic() {
        if($_FILES["upload"]["error"] > 0) {
            echo "<script>alert('上传出错了');</script>";
            return;
        }

        $type = get_img_type($_FILES["upload"]["name"]);
        if($type != ".jpg" && $type != ".jpeg" && $type != ".png" && $type != ".gif") {
            echo "<script>alert('只支持上传jpg,jpeg,png,gif格式的图片');</script>";
            return;
        }

        //对上传的图片重命名,防止重名或出现中文名称而造成乱码等问题
        $img_name = md5(microtime().rand()).$type;
        $img_dir = "../images/".$img_name;
        if(!file_exists($img_dir)) {
            move_uploaded_file($_FILES["upload"]["tmp_name"], $img_dir);
            adjust_img($img_name);
            write_file($img_name);
        }
    }

    //获得图片文件扩展名
    function get_img_type($img_name) {
        $i = strrpos($img_name, ".");
        $type = strtolower(substr($img_name, $i));
        return $type;
    }

    //获得缩略图文件名
    function get_thumb_name($img_name) {
        $i = strrpos($img_name, ".");
        $thumb_name = substr($img_name, 0, $i)."_thumb".get_img_type($img_name);
        return $thumb_name;
    }

    //缩放并裁剪图片  生成缩略图文件
    function adjust_img($img_name) {
        //获取原图片
        $type = get_img_type($img_name);
        $src_im = null;
        $src_dir = "../images/".$img_name;
        if($type == ".jpg" || $type == ".jpeg") {
            $src_im = imagecreatefromjpeg($src_dir);
        } else if($type == ".png") {
            $src_im = imagecreatefrompng($src_dir);
        } else if($type == ".gif") {
            $src_im = imagecreatefromgif($src_dir);
        }
        if($src_im == null) {
            return;
        }

        //缩放图片
        $src_size = getimagesize($src_dir);
        $src_w = $src_size[0];
        $src_h = $src_size[1];
        $scale_w = $src_w / 100;
        $scale_h = $src_h / 100;
        $scale = min(array($scale_w, $scale_h));
        $mid_w = $src_w / $scale;
        $mid_h = $src_h / $scale;
        $mid_im = imagecreatetruecolor($mid_w, $mid_h);
        imagecopyresampled($mid_im, $src_im, 0, 0, 0, 0, $mid_w, $mid_h, $src_w, $src_h);

        //裁剪图片
        $im = imagecreatetruecolor(100, 100);
        $x = round(($mid_w - 100) * 0.5);
        $y = round(($mid_h - 100) * 0.5);
        imagecopy($im, $mid_im, 0, 0, $x, $y, 100, 100);
        $dir = "../images/".get_thumb_name($img_name);
        if($type == ".jpg" || $type == ".jpeg") {
            header("content-type", "image/jpeg");
            imagejpeg($im, $dir);
        } else if($type == ".png") {
            header("content-type", "image/png");
            imagepng($im, $dir);
        } else if($type == ".gif") {
            header("content-type", "image/gif");
            imagegif($im, $dir);
        }

        imagedestroy($src_im);
        imagedestroy($mid_im);
        imagedestroy($im);
    }

    //上传图片后将图片的信息写入到文件
    function write_file($img_name) {
        $file_dir = "../txt/data.txt";
        $file = fopen($file_dir, "a");
        chmod($file_dir, 0777); //Linux Mac操作系统修改权限

        $img_dir = "../images/".$img_name;
        $fsize = filesize($img_dir);
        if($fsize > 1024 * 1024) {
            $fsize = round($fsize / 1024 / 1024, 2)."MB";
        } else if($fsize > 1024) {
            $fsize = round($fsize / 1024, 2)."KB";
        } else {
            $fsize .= "字节";
        }

        date_default_timezone_set("Asia/Shanghai");
        $t = date("Y-m-d H:i");

        $str = $img_name."|".$fsize."|".$t."\n";
        fwrite($file, $str);
        fclose($file);
    }

    //读取文件中的图片信息到数组
    function read_file() {
        $file_dir = "../txt/data.txt";
        if(!file_exists($file_dir)) {
            return false;
        }

        $img_arr = array();
        $file = fopen($file_dir, "r");
        while($img_info = fgets($file)) {
            $img_info = trim($img_info);
            array_push($img_arr, explode("|", $img_info));
        }
        fclose($file);

        return $img_arr;
    }

    //展示图片列表
    function show_pics() {
        $img_arr = read_file();
        if(!$img_arr) {
            return;
        }

        for($i = 0; $i < count($img_arr); $i++) {
            $img_info = $img_arr[$i];
            $img_name = $img_info[0];
            $upl_time = $img_info[1];
            $img_size = $img_info[2];
            echo "<script>appendItem('{$img_name}', '{$upl_time}', '{$img_size}');</script>";
        }
    }

    //删除图片
    function rmpic($img_name) {
        $img_arr = read_file();
        if(!$img_arr) {
            return;
        }

        $img_dir = "../images/".$img_name;
        if(file_exists($img_dir)) {
            unlink($img_dir);
        }

        $thumb_name = get_thumb_name($img_name);
        $thumb_dir = "../images/".$thumb_name;
        if(file_exists($thumb_dir)) {
            unlink($thumb_dir);
        }

        for($i = 0; $i < count($img_arr); $i++) {
            $img_info = $img_arr[$i];
            if($img_name == $img_info[0]) {
                array_splice($img_arr, $i, 1);
                break;
            }
        }

        $file_dir = "../txt/data.txt";
        $file = fopen($file_dir, "w");
        $str = "";
        for($i = 0; $i < count($img_arr); $i++) {
            $img_info = $img_arr[$i];
            $str .= $img_info[0]."|".$img_info[1]."|".$img_info[2]."\n";
        }
        fwrite($file, $str);
        fclose($file);
    }

    //下载图片
    function download_pic($img_name) {
        $type = get_img_type($img_name);
        if($type == ".jpg" || $type == ".jpeg") {
            header("content-type", "image/jpeg");
        } else if($type == ".png") {
            header("content-type", "image/png");
        } else if($type == ".gif") {
            header("content-type", "image/gif");
        }

        $img_dir = "../images/".$img_name;
        header("content-disposition:attachment;filename=".$img_dir);
        header("content-length", filesize($img_dir));

        ob_clean();
        readfile($img_dir);
        flush();
    }
?>