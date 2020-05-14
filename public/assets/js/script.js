


let customFile=document.getElementById("customFile");
customFile.addEventListener('change', updateFileInfo);

function updateFileInfo(){
    $("#upload-file-info").html("nouvelle illustration : <strong>"+$(this).val()+"</strong>");
}