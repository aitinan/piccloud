<script type="text/javascript" src="../js/cloud.js"></script>
<?php
//上传图片
function upload_pic() {
    if($_FILES["upload"]["error"] > 0) {
        echo "<script>alert('上传出错了');</script>";
        return;
    }

    $type = get_type($_FILES["upload"]["name"]);
    if($type != ".jpg" && $type != ".jpeg" && $type != ".png" && $type != ".gif") {
        echo "<script>alert('请上传jpg,jpeg,png,gif格式的图片');</script>";
        return;
    }

    //对上传的图片重命名,防止重名或出现中文名称而造成乱码等问题
    $filename = md5(microtime().rand()).$type;
    $dest_dir = "../images/".$filename;
    if(!file_exists($dest_dir)) {
        move_uploaded_file($_FILES["upload"]["tmp_name"], $dest_dir);
        adjust_img($filename);
        write_file($filename);
    }
}

//删除图片
function rmpic() {
    $img_arr = read_file();
    for($i = 0; $i < count($img_arr); $i++) {
        $img_info = $img_arr[$i];
        if($img_info["img_name"] == $_GET["remove"]) {
            $img_name = $_GET["remove"];
            $img_dir = "../images/".$_GET["remove"];
            $thumb_name = get_thumb_name($img_name);
            $thumb_dir = "../images/".$thumb_name;

            if(file_exists($img_dir)) {
                unlink($img_dir);
            }
            if(file_exists($thumb_dir)) {
                unlink($thumb_dir);
            }

            array_splice($img_arr, $i, 1);
            break;
        }
    }

    $str = "";
    for($i = 0; $i < count($img_arr); $i++) {
        $img_info = $img_arr[$i];
        $str .= implode("|", $img_info)."\n";
    }

    $file = fopen("../txt/data.txt", "w");
    fwrite($file, $str);
    fclose($file);
}

//下载图片
function download_pic() {
    $img_name = $_GET["download"];
    $img_dir = "../images/".$img_name;

    header("content-type:application/octet-stream");
    header("content-disposition:attachment;filename={$img_name}");
    header("content-length:".filesize($img_dir));

    ob_clean();
    flush();

    readfile($img_dir);
}

//上传图片后将图片的信息写入到文件
function write_file($img_name) {
    $file = fopen("../txt/data.txt", "a");
    chmod("../txt/data.txt", 0666);  //Linux Mac操作系统修改权限

    $img_size = filesize("../images/".$img_name);
    if($img_size >= 1024 * 1024) {
        $img_size = $img_size / 1024 / 1024;
        $img_size = round($img_size, 2)."MB";
    } else if($img_size >= 1024) {
        $img_size = $img_size / 1024;
        $img_size = round($img_size, 2)."KB";
    } else {
        $img_size = $img_size."字节";
    }

    ini_set("date.timezone", "Asia/Shanghai");
    $t = date("Y-m-d H:i");

    $str = $img_name."|".$img_size."|".$t."\n";
    fwrite($file, $str);

    fclose($file);
}

//读取文件中的图片信息到数组
function read_file() {
    $img_arr = array();

    if(!file_exists("../txt/data.txt")) {
        return $img_arr;
    }

    $file = fopen("../txt/data.txt", "r");
    while($str = fgets($file)) {
        $str = trim($str);
        $img_info = explode("|", $str);
        $keys = array("img_name", "img_size", "upload_time");
        $img_info = array_combine($keys, $img_info);
        array_push($img_arr, $img_info);
    }
    fclose($file);

    return $img_arr;
}

//展示图片列表
function show_pics() {
    $img_arr = read_file();
    for($i = 0; $i < count($img_arr); $i++) {
        $img_info = $img_arr[$i];
        $img_name = $img_info["img_name"];
        $img_size = $img_info["img_size"];
        $upload_time = $img_info["upload_time"];
        echo "<script>addListItem('{$img_name}', '{$img_size}', '{$upload_time}');</script>";
    }
}

//缩放并裁剪图片  生成缩略图文件
function adjust_img($img_name) {
    $src_dir = "../images/".$img_name;
    $type = get_type($img_name);
    $src_im = null;
    //获取原图片
    if($type == ".jpg" || $type == ".jpeg") {
        $src_im = imagecreatefromjpeg($src_dir);
    } else if($type == ".png") {
        $src_im = imagecreatefrompng($src_dir);
    } else if($type == ".gif") {
        $src_im = imagecreatefromgif($src_dir);
    }

    //缩放图片
    $size = getimagesize($src_dir);
    $w = round($size[0]);
    $h = round($size[1]);
    $scale = ($w > $h ?$h / 100 : $w / 100);
    $dst_w = round($w > $h ?$w / $scale : 100);
    $dst_h = round($w > $h ?100 : $h / $scale);
    $dst_im = imagecreatetruecolor($dst_w, $dst_h);
    imagecopyresampled($dst_im, $src_im, 0, 0, 0, 0, $dst_w, $dst_h, $w, $h);

    //裁剪图片
    $im = imagecreatetruecolor(100, 100);
    $src_x = ($w > $h ?ceil(($dst_w - 100) * 0.5) : 0);
    $src_y = ($w > $h ?0 : ceil(($dst_h - 100) * 0.5));
    imagecopy($im, $dst_im, 0, 0, $src_x, $src_y, 100, 100);

    $thumb_dir = "../images/".get_thumb_name($img_name);
    if($type == ".jpg" || $type == ".jpeg") {
        header("content-type", "image/jpeg");
        imagejpeg($im, $thumb_dir);
    } else if($type == ".png") {
        header("content-type", "image/png");
        imagepng($im, $thumb_dir);
    } else if($type == ".gif") {
        header("content-type", "image/gif");
        imagegif($im, $thumb_dir);
    }
    chmod($thumb_dir, 0777); //Linux Mac操作系统修改权限

    imagedestroy($im);
    imagedestroy($src_im);
    imagedestroy($dst_im);
}

//获得图片文件扩展名
function get_type($img_name) {
    $i = strrpos($img_name, ".");
    $type = strtolower(substr($img_name, $i));
    return $type;
}

//获得缩略图文件名
function get_thumb_name($img_name) {
    $i = strrpos($img_name, ".");
    $type = strtolower(substr($img_name, $i));
    $thumb_name = substr($img_name, 0, $i)."_thumb".$type;
    return $thumb_name;
}
?>