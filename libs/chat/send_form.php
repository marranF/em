
<div style="float: left; width: 50%;">

<form action="../chat/upload.php" method="post" target="hiddenframe" enctype="multipart/form-data">
<iframe id="hiddenframe" name="hiddenframe" style="width:0px; height:0px; border:0px"> </iframe>
<table border="3">
  <tr>
    <td>    
      <input type="file" id="userfile" style="width: 100%;" name="userfile" value="img">
    </td>
    <td>
      <input type="submit" style="width: 100px;" name="upload" id="upload" value="Загрузить" >
    </td>
  </tr>
  <tr>
    <td>
      <textarea rows="10" cols="60" id="message" ></textarea>
    </td>
    <td>
      <input type="button" style="width: 100px;" value="url" id="url" onclick="urlss();"> <br>
      <input type="button" style="width: 100px;" value="youtube" id="youtube" onclick="youtb();"> <br><br>
      <input type="submit" id="send" value="Send" style="width: 100px;" onclick="send_click();">
    </td>
  </tr>
</table>
</form>
</div>

<div id="additions" style="float: right;"></div>
<div id="res"></div>


    <script type="text/javascript">    
    
      var img=new Array();
      var url=new Array();
      var ytb=new Array();

      function urlss()
      {
        var prmt=prompt("URL адрес:","");
        if(prmt!=null && prmt!="")
        {
          url[url.length]=prmt;
          $("#additions").append("<div class='comment'>Добавлен Url: "+prmt);
        }
      }
      function youtb()
      {
        var prmt=prompt("Youtube ролик:","");
        if(prmt!=null && prmt!="")
        {
          var f1=prmt.indexOf('https');
          var f2=prmt.indexOf('frameborder');
          prmt=prmt.substring(f1,f2-2);
          ytb[ytb.length]=prmt;
          $("#additions").append("<div class='comment'>Добавлен ролик Youtube: "+prmt);
        }
      }
        
        function handleResponse(mes) {
            if (mes.errors != null) {
                $('#res').html("Возникли ошибки во время загрузки файла: " + mes.errors);
            }    
            else {
                $("#additions").append("<div class='comment'>Добавлено изображение: "+mes.name);
                img[img.length]=mes.name; 
                $('#res').html("");
            } 
             $("#userfile").val("");   
        }
        
    </script>


