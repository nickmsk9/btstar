var loading = "<img src=\"pic/upload.gif\" alt=\"Загрузка..\" />";

jQuery(function() {
    jQuery(".aprove").click ( function(){
			var id = jQuery(this).attr("id");
			jQuery(this).parent().empty();
            jQuery(".loading > span."+id).html(loading);
            jQuery.post("fajax2.php",{"id":id,"act":"aprove"},function (response) {
                jQuery(".loading > span."+id).empty();
				jQuery(".answer > span."+id).html(response);
            });
        }
    );
	jQuery(".deny").click ( function(){
			var id = jQuery(this).attr("id");
			jQuery(this).parent().empty();
            jQuery(".loading > span."+id).html(loading);
            jQuery.post("fajax2.php",{"id":id,"act":"deny"},function (response) {
                jQuery(".loading > span."+id).empty();
				jQuery(".answer > span."+id).html(response);
           });
        }
    );
});