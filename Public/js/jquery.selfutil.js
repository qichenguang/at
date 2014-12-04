;(function($) {
    $.extend({
        jAlert: function(options) {
            options = $.extend({title:"警告",content:""},options);
            var html = "<div title='" + options.title + "'><p>" + options.content + "</p></div>";
            var dialogOpts = {
                buttons: {
                    "OK": function() {
                        $(this).dialog('close');
                    }
                }
            };
            $(html).dialog(dialogOpts);
        }
    });

    //
    jAlert = function(content,title){
        $.jAlert({title:title,content:content});
    }
})(jQuery);