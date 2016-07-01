/**
 * Created by zmx on 16/7/1.
 */
var upload = document.getElementById("upload");
//选择上传的图片后立刻提交
upload.onchange = function() {
    //设置上传的时间戳 防止刷新重复提交
    var uplTime = document.getElementById("upl-time");
    uplTime.value = new Date().getTime() + "";

    var uploadForm = document.getElementById("upload-form");
    uploadForm.submit();
}

//添加图片列表项
function appendItem(imgName, uplTime, imgSize) {
    var imgNameWithoutType = getImgNameWithoutType(imgName);

    var imgItem = document.createElement("div");
    imgItem.setAttribute("class", "img-item");

    //图片
    var img = document.createElement("img");
    img.src = "../images/" + getThumbName(imgName);
    imgItem.appendChild(img);

    var imgInfo = document.createElement("div");
    imgInfo.setAttribute("class", "img-info");

    //图片名称
    var imgNameLink = document.createElement("a");
    imgNameLink.setAttribute("class", "img-name");
    imgNameLink.href = "../images/" + imgName;
    imgNameLink.target = "_blank";
    imgNameLink.innerText = imgName;
    imgInfo.appendChild(imgNameLink);

    var imgTimeSize = document.createElement("div");
    imgTimeSize.setAttribute("class", "img-time-size");

    //上传时间
    var uplTimeText = document.createElement("div");
    uplTimeText.setAttribute("class", "img-time");
    uplTimeText.innerText = uplTime;
    imgTimeSize.appendChild(uplTimeText);

    //图片大小
    var imgSizeText = document.createElement("div");
    imgSizeText.setAttribute("class", "img-size");
    imgSizeText.innerText = imgSize;
    imgTimeSize.appendChild(imgSizeText);

    imgInfo.appendChild(imgTimeSize);

    var imgDownRm = document.createElement("div");
    imgDownRm.setAttribute("class", "img-down-rm");

    var imgDownForm = document.createElement("form");
    imgDownForm.setAttribute("id", imgNameWithoutType + "-down-form");
    imgDownForm.setAttribute("method", "post");
    imgDownForm.setAttribute("action", "cloud.php");

    //下载按钮
    var downLink = document.createElement("a");
    downLink.href = "javascript:downloadPic('" + imgNameWithoutType + "')";
    downLink.innerText = "下载";
    imgDownForm.appendChild(downLink);

    //下载的文件名
    var downVal = document.createElement("input");
    downVal.setAttribute("type", "hidden");
    downVal.setAttribute("name", "download");
    downVal.setAttribute("value", imgName);
    imgDownForm.appendChild(downVal);

    //下载的时间戳
    var downTime = document.createElement("input");
    downTime.setAttribute("type", "hidden");
    downTime.setAttribute("name", "down_time");
    downTime.setAttribute("id", imgNameWithoutType + "-down-time");
    imgDownForm.appendChild(downTime);

    imgDownRm.appendChild(imgDownForm);

    var imgRmForm = document.createElement("form");
    imgRmForm.setAttribute("id", imgNameWithoutType + "-rm-form");
    imgRmForm.setAttribute("method", "post");
    imgRmForm.setAttribute("action", "cloud.php");

    //删除按钮
    var rmLink = document.createElement("a");
    rmLink.href = "javascript:removePic('" + imgNameWithoutType + "')";
    rmLink.innerText = "删除";
    imgRmForm.appendChild(rmLink);

    //删除的文件名
    var rmVal = document.createElement("input");
    rmVal.setAttribute("type", "hidden");
    rmVal.setAttribute("name", "remove");
    rmVal.setAttribute("value", imgName);
    imgRmForm.appendChild(rmVal);

    //删除的时间戳
    var rmTime = document.createElement("input");
    rmTime.setAttribute("type", "hidden");
    rmTime.setAttribute("name", "rm_time");
    rmTime.setAttribute("id", imgNameWithoutType + "-rm-time");
    imgRmForm.appendChild(rmTime);

    imgDownRm.appendChild(imgRmForm);

    imgInfo.appendChild(imgDownRm);

    imgItem.appendChild(imgInfo);

    var content = document.getElementById("content");
    content.appendChild(imgItem);
}

//获得没有后缀的图片文件名
function getImgNameWithoutType(imgName) {
    var i = imgName.lastIndexOf(".");
    var name = imgName.substr(0, i);
    return name;
}

//获得图片文件名后缀
function getImgType(imgName) {
    var i = imgName.lastIndexOf(".");
    var type = imgName.substr(i).toLowerCase();
    return type;
}

//获得缩略图文件名
function getThumbName(imgName) {
    var thumbName = getImgNameWithoutType(imgName) + "_thumb" + getImgType(imgName);
    return thumbName;
}

//点击下载按钮后执行的操作
function downloadPic(imgNameWithoutType) {
    //设置下载时间戳 防止刷新重复提交
    var downTime = document.getElementById(imgNameWithoutType + "-down-time");
    downTime.value = new Date().getTime() + "";

    var downForm = document.getElementById(imgNameWithoutType + "-down-form");
    downForm.submit();
}

//点击删除按钮后执行的操作
function removePic(imgNameWithoutType) {
    var flag = confirm("确定要删除吗");
    if(!flag) {
        return;
    }

    //设置删除时间戳 防止刷新重复提交
    var rmTime = document.getElementById(imgNameWithoutType + "-rm-time");
    rmTime.value = new Date().getTime() + "";

    var rmForm = document.getElementById(imgNameWithoutType + "-rm-form");
    rmForm.submit();
}