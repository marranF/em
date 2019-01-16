
<?php
  header("Content-Type: text/html; charset=utf-8"); 
  $user=1;
  echo "<script type='text/javascript'> var user=".$user.";</script>";
?>
<style>
.comment{
   border:1px solid #666;
   margin:10px;
   padding:10px;
   font-family:Tahoma;
   border-radius:4px;
   box-shadow:2px 2px 2px #999;
}
</style>
<div id='user' value='<?=1?>'>user1</div>
<div id='commentBlock'>
</div>


<script type="text/javascript" src="jquery-1.5.1.min.js"></script>
<script type="text/javascript">

send_ajax(JSON.stringify({"action":"init"}));
var arr_id = [];

function send_ajax( data)
{
  $.ajax({
      type: "POST",
      url: "base_messages.php",
      data: {"data":data},
      cache: false,     
      success: function(response){
        obj=jQuery.parseJSON( response );
        if(JSON.parse(data).action=="init")
        {
        	$('#commentBlock').html("");
					output(obj);
        }          
        if(JSON.parse(data).action=="send")		
          output(obj);
        if(JSON.parse(data).action=="delete")
          delete_message(obj);
      }  
  });  
}

function update( )
{
	send_ajax(JSON.stringify({"action":"init"}));
}

 $(document).ready(function()
 {  
   setInterval('update()',10000);  
 });


function send_click()
{
  if($("#message").val()=="")
  {
    alert("Сообщение не может быть пустым");
    return;
  } 
  var message={"action":"send","id":user,"message":$("#message").val(),"url":url,"img":img,"ytb":ytb};
  send_ajax(JSON.stringify(message));
  $("div#additions").empty();
  $("#message").val("");
  $("#userfile").val(""); 
}

function output(obj)      
{ 
   for(i=0;i<obj.length;i++)
   {
     arr_id.push(obj[i].id);
     var res="<div class='comment' id='"+obj[i].id+"' title='div_id="+obj[i].id+"'>Отправил: <strong>"
      +obj[i].login+"</strong><br>"+obj[i].message+"<br>";
     if(user!=obj[i].user)
        res=res.concat("<button id="+obj[i].id+">Like</button>"+obj[i].likes); 
     res=res.concat(" Комментарий отправлен "+obj[i].data);
     if(user==obj[i].user)
     {
       res=res.concat("&nbsp&nbsp <button id="+obj[i].id+" onclick='delete_click(this);'>Delete</button>");
       res=res.concat("<br><br>");  
     }   
     if(obj[i].img!=null)
     {            
       for(j=0;j<obj[i].img.length;j++)
       {
         res=res.concat("<br><br><img src=img/"+obj[i].img[j]+"  style='height:100px' onmouseover=\"this.style.height='631px'\" onmouseout=\"this.style.height='100px'\"/>");
       }
     }
     if(obj[i].ytb!=null)
     {
       for(j=0;j<obj[i].ytb.length;j++)
       {
         res=res.concat("<br><br><iframe width='420' height='315' src="+obj[i].ytb[j]+" frameborder='0' allowfullscreen></iframe>");
       }
     }
     if(obj[i].http!=null)
     {
       for(j=0;j<obj[i].http.length;j++)
       {
         res=res.concat("<br><a href='"+obj[i].http[j]+"'>"+obj[i].http[j]+"</a>");    
       }
     }
     res=res.concat("</div>");
     $("#commentBlock").append(res);     
   }
   window.scrollTo(0,document.body.scrollHeight);
}

function delete_click(item)
{
  var message={"action":"delete","id":item.id};
  send_ajax(JSON.stringify(message));
  
}

function delete_message(obj)
{
  if(obj.res="ok")
    $("#"+obj.id).remove();
}
 
  </script>
  
<?php
include "send_form.php";
?>
