function show_tags(category){
    jQuery("#loading-layer").show("fast");
    jQuery.post("showtags.php",{"cat":category},function (response) {
        jQuery("#tags").empty();
        jQuery("#tags").append(response);
        jQuery("#loading-layer").hide();
    });
}

function showspoiler(id){
    var text = document.getElementById(id);
    var pic = document.getElementById('pic' + id);
    if(text.style.display == 'none')
    {
        text.style.display = 'block';
        pic.src = 'pic/minus.gif';
        pic.title = 'Скрыть';
    }
    else
    {
        text.style.display = 'none';
        pic.src = 'pic/plus.gif';
        pic.title = 'Показать';
    }
} 

function karma(id, type, act) {
jQuery.post("karma.php",{"id":id,"act":act,"type":type},function (response) {
jQuery("#karma" + id).empty();
jQuery("#karma" + id).append(response);
});
}

   
   jQuery(document).ready(function(){
     var t_load   = '<img src="pic/upload.gif" title="Загрузка" />';
     var t_img_p  = '<img src="pic/plus.gif" />';
     var t_img_m  = '<img src="pic/minus.gif" />';
      
                 jQuery('#show_thanks').toggle(
                        function() {
                            jQuery('#thanks_body').slideDown( 'fast' ); 
                            jQuery('#thanks_body').empty();
                            jQuery('#thanks_body').append(t_load + '&nbsp;'); 
        jQuery.post('thanks_new.php',{'tid':jQuery('#torrentid').val(),'do':'show_thanks'},
                        function(data,status)
                        {
                            if(status != 'error')
                            {
                                jQuery('#thanks_body').empty();
                                jQuery('#thanks_body').append(data);
                            }
                            else
                            {
                                jQuery('#thanks_body').empty();
                                alert( 'Произошла ошибка' );
                                  return false;
                            }
                            
                        },'html');
                            jQuery(this).html(t_img_m + '&nbsp;&nbsp;Последние поблагодарившие');
                        },
                        function() {
                            jQuery('#thanks_body').slideUp( 'fast' );
                            jQuery(this).html(t_img_p + '&nbsp;&nbsp;Последние поблагодарившие');
                        }
                    );
             
     function SendThanks()
     {
         jQuery('#send_thanks').get(0).style.display = 'none';
         jQuery.post('thanks_new.php',{'tid':jQuery('#torrentid').val(),'do':'send_thanks'},
                         function(response)
                         {
                                 jQuery('#thanks_msg').html(response).fadeOut(3000);
                         });
        
     }
     jQuery('#send_thanks').click(SendThanks);
  });