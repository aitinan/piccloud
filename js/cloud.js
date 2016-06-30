/**
 * Created by zmx on 16/6/28.
 */
var upl = document.getElementById("upload");
//选择上传的图片后立刻提交
upl.onchange = function() {
    var form = document.getElementsByTagName("form")[0];
    form.submit();
}

//添加图片列表项
function addListItem(imgName, imgSize, uploadTime) {
    var listItem = document.createElement("div");
    listItem.setAttribute("class", "list-item");

    //图片
    var img = document.createElement("img");
    img.src = "../images/" + getThumbName(imgName);
    listItem.appendChild(img);

    var itemInfo = document.createElement("div");
    itemInfo.setAttribute("class", "item-info");

    //图片名称呢
    var picTitle = document.createElement("a");
    picTitle.setAttribute("class", "pic-title");
    picTitle.setAttribute("href", "../images/" + imgName);
    picTitle.innerText = imgName;
    itemInfo.appendChild(picTitle);

    var picInfo = document.createElement("div");
    picInfo.setAttribute("class", "pic-info");

    //上传时间
    var timeText = document.createElement("div");
    timeText.setAttribute("class", "upload-time");
    timeText.innerText = uploadTime;
    picInfo.appendChild(timeText);

    //图片大小
    var sizeText = document.createElement("div");
    sizeText.setAttribute("class", "pic-size");
    sizeText.innerText = imgSize;
    picInfo.appendChild(sizeText);

    itemInfo.appendChild(picInfo);

    var oprt = document.createElement("div");
    oprt.setAttribute("class", "oprt");

    //下载按钮
    var down = document.createElement("a");
    down.setAttribute("href", "../php/cloud.php?download=" + imgName);
    down.innerText = "下载";
    oprt.appendChild(down);

    //删除按钮
    var rm = document.createElement("a");
    rm.setAttribute("href", "javascript:confirmRemove('" + imgName + "');");
    rm.innerText = "删除";
    oprt.appendChild(rm);

    itemInfo.appendChild(oprt);

    listItem.appendChild(itemInfo);

    var content = document.getElementById("content");
    content.appendChild(listItem);
}

//弹出确定是否删除的提示框
function confirmRemove(imgName) {
    var flag = confirm("确定要删除吗");
    if(flag) {
        window.location.href = "../php/cloud.php?remove=" + imgName;
    }
}

//获得缩略图文件名
function getThumbName(imgName) {
    var i = imgName.lastIndexOf(".");
    var type = imgName.substr(i);
    var thumbName = imgName.substr(0, i) + "_thumb" + type;
    return thumbName;
}