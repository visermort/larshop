(function($){

    //нажатие - отправить сообщение: валидация, если прошла,то отправка сообщения,
    //если нет, то сообщение об ошибке, полям присваиваем класс error
    $('.form-message').submit(function(e){
        var form=$(this);
        e.preventDefault();
        //console.log('startsubmit')
        var inputs = $(form).find('.validate'),
            res = true;
        $.each(inputs,function(index,item){
            var content=$(item).val().trim();
            //     console.log(item);
            if (content.length === 0) {
                $(item).addClass('error');
                res = false;
            } else {
                $(item).removeClass('error');
            }

        });
        if (res === true) {
            sendMessage($(form));
        } else {
            $(form).find('.modal__message_error').html('Вы заполнили не все поля');
            $(form).find('.modal__message_success').html('');
        }
    });


    //отмена класса error для полей с валидацией при нажатии клавиши
    $('.validate').on('keydown',function(){
        $(this).removeClass('error');
    });

    //отправка почты через айакс
    var sendMessage=function(form){
        var content=form.serialize();
        //console.log(content);
        $.ajax({
            type:"POST",
            dataType: "json",
            url:"/mail",
            data: content
        }).done(function(ans){
             //console.log('done',ans);
            if (ans.status == true) {
                $(form).find('.modal__message_success').html(ans.message);
                $(form).find('.modal__message_error').html('');
            }else{
                $(form).find('.modal__message_error').html(ans.message);
                $(form).find('.modal__message_success').html('');
            }

        }).fail(function(ans){
            console.log('error',ans);
            $(form).find('.modal__message_error').html(ans.message);
            $(form).find('.modal__message_success').html('');
        });

    }


})(jQuery);
