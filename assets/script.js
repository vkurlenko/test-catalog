$(document).ready(function() {

    $('li').each(function() {
        if($(this).find('ul').length) {
            $(this).prepend('<i>+</i>');
            $(this).find('span').first().addClass('toggled');
        }
    });

    $('li span.toggled').on('click', function() {
        var obj = $(this).parent().find('ul').first();
        $(obj).toggle();

        var i = $(this).prev('i').eq(0);

        var val = ($(i).text() == '+') ? '-' : '+';
        $(i).text(val);
    });

    /* Отправка ID товара на обработку в контроллер.
     * Если ID уже есть в сессии, то он будет удален из корзины,
     * иначе товар будет добавлен в корзину
     * */
    $('.item').on('click', function() {
        var id = $(this).attr('id');

        $.ajax({
            type: "POST",
            url: "ajax.php",
            context: $(this),
            data: {
                id: id
            },
            success: function(){
                $(this).toggleClass('in-cart').text('в корзину');

                if($(this).hasClass('in-cart')) {
                    $(this).removeClass('btn-success');
                    $(this).addClass('btn-danger');
                    $(this).text('удалить из корзины');
                } else {
                    $(this).removeClass('btn-danger');
                    $(this).addClass('btn-success');
                    $(this).text('в корзину');
                }
            }
        }).done(function( msg ) {
            //console.log(msg);
        });

        return false;
    });

    /* Запрос на формирование заказа */
    $('.order-button').on('click', function() {

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: {
                action: 'set-order'
            },
            success: function(order_id){

                if(order_id) {
                    $('tbody').html('');
                    $('.order-button').remove();
                    $('table').after('<div class="alert alert-success" role="alert">Спасибо за заказ! Номер Вашего заказа: '+order_id+'</div>')
                }

            }
        }).done(function( msg ) {
            //console.log(msg);
        });
    })
})