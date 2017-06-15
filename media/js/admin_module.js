$(function(){
    $('.my-switch').switcher({copy: {en: {yes: '', no: ''}}}).on('change', function(){
        var checkbox = $(this);
        checkbox.switcher('setDisabled', true);

        $.getJSON(checkbox.data('link') + '/' + (checkbox.is(':checked') ? 'on' : 'off') + '/' + checkbox.data('id') + '?slug=' + checkbox.data('slug'), function(response){
            if(response.result === 'error'){
                alert(response.error);
            }
            if(checkbox.data('reload')){
                location.reload();
            }else{
                checkbox.switcher('setDisabled', false);
            }
        });
    });

});