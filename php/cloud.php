<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>云盘</title>
        <link rel="stylesheet" type="text/css" href="../css/cloud.css">
    </head>
    <body>
        <div class="header">
            <div class="title">我的云盘</div>
            <form method="post" action="cloud.php" enctype="multipart/form-data">
                <div class="upload">
                    <div>上传图片</div>
                    <input type="file" name="upload" id="upload">
                </div>
            </form>
        </div>
        <div id="content" class="content"></div>
    </body>
    <?php
        require_once("handlecloud.php");

        if(isset($_FILES["upload"])) {
            upload_pic();
        }

        if(isset($_GET["remove"])) {
            rmpic();
        }

        if(isset($_GET["download"])) {
            download_pic();
        }

        show_pics();
    ?>
</html>