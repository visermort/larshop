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

    };

    //запросы на удадение и добавление словарей
    //на удаление
    $('.dict-del').on('click',function(e){
        var button = $(e.target),
            id = button.attr('data-id'),
            item= button.parents('.dictlist__value-item'),//элемент списка, для удаления
            token = $('#csrftoken').attr('data-token');
      //  console.log('del ',id,token);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token
            }
        });
        $.ajax({
            type:"POST",
            dataType: "json",
            url:"/manager/dict/del",
            data: { 'id' : id }
        }).done(function(e){
                // console.log(e);
            if (e.status) {
                //если всё хорошо, то удаляем элемент
                $(item).remove();
            } else {
                console.log(e);
            }
        }).fail(function(e){
            console.log(e);
        });
    });

    //на вставку
    $('.dict-add').on('click',function(e){
        var button = $(e.target),
            table=button.attr('data-table'),
            field=button.attr('data-field'),
            list=button.parents('.dictlist__panels-item-panel').find('.dictlist__panels-item-list'),//список, куда добавить элемент
            value=button.parents('.dictlist__formgroup').find('.dictlist__input-dict').val().trim(),//новое значение словаря
            token = $('#csrftoken').attr('data-token');
        //console.log('add ',table,field,value,token);
        if (value !== '') {
            $.ajaxSetup({ //оказалось - просто вот так, как в руководстве
                headers: {
                    'X-CSRF-TOKEN': token
                }
            });
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/dict/add",
                data: {'table': table, 'field': field, 'value': value}
            }).done(function (e) {
              //  console.log(e);
                if (e.status) {
                    //если всё получилось, то добавляем элемент
                    list.append('<li class="dictlist__value-item">'+ e.id+' : '+value+'</li>');
                } else {
                    console.log(e);
                }
            }).fail(function (e) {
                console.log(e);
            });
        }
    });

    //переключение табов
    $('.dictlist__tabs-item').on('click',function(e){
        var tab=$(e.target),
            key=tab.attr('data-tab'),
            panel= tab.parents('.dictlist').find('.panel-'+key),
            panels = tab.parents('.dictlist').find('.dictlist__panels-item-panel'),
            tabs = tab.parents('.dictlist__tabs').find('.dictlist__tabs-item');
        panels.removeClass('active');
        panel.addClass('active');
        tabs.removeClass('active');
        tab.addClass('active');
    });


})(jQuery);
