$(function () {
    // event.type должен быть keypress

    function eternalmotion(div, duration) {

        a = $(div).animate({top: $(div).position().top - 1}, duration, 'linear',
            function () {
                if ($(div).find('.active').offset().top < $(div).parent().offset().top) {
                    if ($(div).find('span:nth-of-type(1)').hasClass('active')) {
                        $(div).find('.active').removeClass('active');
                        $(div).find('div.item:nth-of-type(2)').addClass('active');
                        $(div).css('top', '0');
                        $.ajax({
                            type: "POST",
                            url: "/ajax/update_user_list",
                            data: ({}),
                            dataType: 'json',

                            success: function (data) {
                                $(div).find('.update_user_item1').removeClass('update_user_item1');
                                $(div).find('div:nth-last-of-type(1)').after(data.html1);
                                $(div).find('span:nth-last-of-type(1)').after(data.html2);
                            }
                        });
                    }
                    else {
                        if ($(div).find('div.active').hasClass('update_user_item1')){
                            $(div).find('.item').not('.update_user_item1').remove();
                            $(div).find('.update_user_item1').removeClass('update_user_item1');
                            $(div).css('top', '0');
                            $(div).find('.active').removeClass('active').next().addClass('active');
                        }else{
                            $(div).find('.active').removeClass('active').next().addClass('active');
                        }

                    }

                    /*if ($('*').is('.update_user_item1') && $(div).find('.update_user_item1').offset().top <= $(div).parent().offset().top) {
                        $(div).find('.item').not('.update_user_item1').remove();
                    }*/
                }


                eternalmotion(div, duration);
            }
        );
    }

    if ($("div").is(".tmn_wrap")) {
        startAll();
    }

    function startAll() {
        eternalmotion('.line1', 40);
        eternalmotion('.line2', 50);
        eternalmotion('.line3', 65);
        eternalmotion('.line4', 45);
        eternalmotion('.line5', 42);
    }

    function stopAll() {
        $('.tmn_wrap').stop();
    }

    var this_line;
    $('.tmn_wrap').hover(function () {
        $(this).stop();
    }, function () {
        if (!$('body').hasClass('modal-open')) {
            eternalmotion($(this), (Math.floor((Math.random() * 30) + 20)));
        } else {
            this_line = $(this);
        }
    });

    $(document).on('click', '.tmn_wrap .item', function () {
        user_id = $(this).data('id');
        $.ajax({
            url: '/ajax/getuser1',
            type: "POST",
            data: ({user_id: user_id}),
            success: function (data) {
                $('#form_1').html(data);
            }
        });
        $('#myModal1').modal('show');
    });

    $('#myModal1').on('hidden.bs.modal', function (e) {
        if (this_line) {
            eternalmotion(this_line, (Math.floor((Math.random() * 30) + 20)));
        }

    })
    $(document).on('submit', '#form_1', function () {
        data = $(this).serialize();
        th = $(this);
        var btn = th.find('.submit');
        btn.button('loading');
        $('#myModal1 .status').text('');
        $.ajax({
            url: '/ajax/pickup',
            type: "POST",
            data: data,
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if (data.status=='employment'){
                    btn.button('reset');
                    $('#myModal1 .status').addClass('text-danger').text(data.message);
                }else{
                    custom_timer1();
                }
            }
        });
        return false;
    });
    var checkPickupTimer;

    function checkPickup() {
        checkPickupTimer = setInterval(function () {
            $.ajax({
                url: '/ajax/check-pickup',
                type: "POST",
                data: ({}),
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    if (data.status == 'new_pickup') {
                        $('#myModal2 .modal-body').html(data.html);
                        $('#myModal2').modal('show');

                    } else if (data.status == 'approve_pickup_sender') {
                        custom_timr1_stop();
                        $('#myModal1').modal('hide');
                        $('#myModal2').modal('hide');
                        $('#myModal4').modal('hide');
                        $('#myModal3 .modal-body').html(data.html);
                        three_min_timer();
                        $('#myModal3').modal('show');
                    } else if (data.status == 'approve_pickup_recipient') {
                        custom_timr1_stop();
                        $('#myModal1').modal('hide');
                        $('#myModal2').modal('hide');
                        $('#myModal4').modal('hide');
                        $('#myModal3 .modal-body').html(data.html);
                        three_min_timer();
                        $('#myModal3').modal('show');
                    } else if (data.status == 'canceled_pickup') {
                        custom_timr1_stop();
                        $('#myModal1 .status').addClass('text-danger').text(data.message);
                        $('#myModal1 .submit').remove();
                    }
                    else if (data.status == 'canceled_sender') {
                        custom_timr1_stop();
                        $('#myModal2 .status').addClass('text-danger').text(data.message);
                        $('#myModal2 .approve').remove();
                        $('#myModal2 .canceled').removeClass('canceled');

                        $('#myModal3 .status').addClass('text-danger').text(data.message);
                        $('#myModal3 #message_input').remove();
                        $('#myModal3 .submit_message').remove();
                    } else if (data.status == 'new_message_sender') {
                        $('.message_block').append(data.html).scrollTop(9999999);
                    }
                    else if (data.status == 'chat_end') {
                        $('#myModal3 .status').addClass('text-danger').text(data.message);
                        $('#myModal3 .submit_message,#myModal3 #message_input').remove();
                    } else if (data.status == 'time_end') {
                        $('#myModal3 .status').addClass('text-danger').text(data.message);
                        $('#myModal3 #message_input').remove();
                        $('#myModal3 .buttons_block').html(data.buttons);
                        three_min_timer_stop();
                        $('.three_minutes_timer>p').html('0:00');
                    } else if (data.status == 'approve_friend') {
                        $('#myModal3 .status').removeClass('text-danger').addClass('text-success').text(data.message);
                        $('#myModal3 .buttons_block').html(data.buttons);
                        $('.contacts_wrap').html(data.contacts);
                    } else if (data.status == 'cancel_friend') {
                        $('#myModal3 .status').removeClass('text-success').addClass('text-danger').text(data.message);
                        $('#myModal3 .buttons_block').html(data.buttons);
                    }else if (data.status == 'obryv') {
                        $('#myModal3 .status').removeClass('text-success').addClass('text-danger').text(data.message);
                        $('#myModal3 #message_input').remove();
                        $('#myModal3 .buttons_block').html(data.buttons);

                        $('#myModal2 .status').addClass('text-danger').text(data.message);
                        $('#myModal2 .approve').remove();
                        $('#myModal2 .canceled').removeClass('canceled');
                    }else if (data.status == 'pickup_time_end' || data.status == 'obryv') {
                        $('#myModal1 .status').addClass('text-danger').text(data.message);
                        $('#myModal1 .submit').remove();
                    }else if (data.status == 'approve_friend_time_end') {
                        $('#myModal3 .status').addClass('text-danger').text(data.message);
                        $('#myModal3 .buttons_block').html(data.buttons);
                    }
                }
            });
        }, 3000);
    }

    function stopCheckPickup() {
        clearInterval(checkPickupTimer);
    }

    $(document).on('click', '#myModal2 .approve', function () {
        th = $(this);
        var btn = th;
        btn.button('loading');
        $.ajax({
            type: "POST",
            url: "/ajax/approve",
            data: ({}),
            success: function (data) {
            }
        });
        return false;
    });
    $(document).on('click', '#myModal2 .canceled', function () {
        th = $(this);
        $.ajax({
            type: "POST",
            url: "/ajax/canceled_recipient",
            data: ({}),
            success: function (data) {
                console.log(data);
                $('.modal').modal('hide');
            }
        });
        return false;
    });
    $(document).on('click', '#myModal3 .approve_friend', function () {
        th = $(this);
        var btn = th;
        btn.button('loading');
        $.ajax({
            type: "POST",
            url: "/ajax/approve_friend",
            data: ({}),
            success: function (data) {
                custom_timer2();
            }
        });
        return false;
    });
    $(document).on('click', '#myModal3 .cancel_friend', function () {
        $.ajax({
            type: "POST",
            url: "/ajax/cancel_friend",
            data: ({}),
            dataType: 'json',
            success: function (data) {
                $('#myModal3 .status').removeClass('text-success').addClass('text-danger').text(data.message);
                $('#myModal3 .buttons_block').html(data.buttons);
            }
        });
        return false;
    });

    $(document).on('click', '#myModal1 .canceled', function () {
        custom_timr1_stop();
        th = $(this);
        $.ajax({
            type: "POST",
            url: "/ajax/canceled_sender",
            data: ({}),
            success: function (data) {
                console.log(data);
                $('.modal').modal('hide');
            }
        });
        return false;
    });

    $("#myModal2").on('hidden.bs.modal', function (e) {
        checkPickup();
    });
    $("#myModal3").on('shown.bs.modal', function (e) {
        $("#message_input").val('').focus();
        stopAll();
    });
    $("#myModal3").on('hidden.bs.modal', function (e) {
        startAll();
    });
    $("#myModal4").on('shown.bs.modal', function (e) {
        $("#myModal4  #message_input").val('').focus();
        $('#myModal4  .message_block').scrollTop(9999999);
        checkMessagesFriend();
    });
    $("#myModal4").on('hidden.bs.modal', function (e) {
        stopCheckMessagesFriend();
    });
    $(document).on('click', '#myModal3 .submit_message', function () {
        if ($("#myModal3  #message_input").val().length > 0) {
            var text = $('#myModal3  #message_input').val();
            $.ajax({
                type: "POST",
                url: "/ajax/submit_message",
                data: ({message: text}),
                success: function (data) {
                    $('#myModal3  .message_block').append('<div class="my_message_wrap"><div class="my_message">' + text + '</div></div>');
                    $("#myModal3  #message_input").val('').focus();
                    $('#myModal3  .message_block').scrollTop(9999999);
                }
            });
        }

        return false;
    });
    $(document).on('keypress', '#myModal3  #message_input', function (e) {
        if (e.keyCode == 13) {
            $('#myModal3  .submit_message').trigger('click');
            e.preventDefault();
            return false;
        }

    });

    $(document).on('click', '.destroy_dialog', function () {
        $.ajax({
            type: "POST",
            url: "/ajax/destroy_dialog",
            data: ({}),
            success: function (data) {
            }
        });
        return false;
    });

    checkPickup();
    var three;
    function three_min_timer() {
        var countdown = 3 * 60 * 1000;
        three = setInterval(function () {
            countdown -= 1000;
            var min = Math.floor(countdown / (60 * 1000));
            var sec = Math.floor((countdown - (min * 60 * 1000)) / 1000);
            if (sec.toString().length<2){
                sec = '0'+sec.toString();
            }

            if (countdown < 0) {
                clearInterval(three);
            } else {
                $(".three_minutes_timer>p").html(min + " : " + sec);
            }

        }, 1000); //1000ms. = 1sec.
    }

    function three_min_timer_stop() {
        clearInterval(three);
    }
    var custom__timer1;
    function custom_timer1() {
        var countdown = 4;
        custom__timer1 = setInterval(function () {
            countdown -= 1;
            if (countdown < 1) {
                clearInterval(custom__timer1);
                $.ajax({
                    type: "POST",
                    url: "/ajax/pickup_time_end",
                    data: ({}),
                    success: function (response) {
                        console.log(response)

                    }
                });
            }
            console.log(countdown);
        }, 1000); //1000ms. = 1sec.
    }

    function custom_timr1_stop() {
        clearInterval(custom__timer1);
    }

    var custom__timer2;
    function custom_timer2() {
        var countdown = 15;
        custom__timer2 = setInterval(function () {
            countdown -= 1;
            if (countdown < 1) {
                clearInterval(custom__timer2);
                $.ajax({
                    type: "POST",
                    url: "/ajax/approve_friend_time_end",
                    data: ({}),
                    success: function (response) {

                    }
                });
            }
            console.log(countdown);
        }, 1000); //1000ms. = 1sec.
    }

    function custom_timer2_stop() {
        clearInterval(custom__timer1);
    }

    $(document).on('click', '.contacts>li>a', function () {
        th = $(this);
        $.ajax({
            type: "POST",
            url: "/ajax/get_contact",
            data: ({id: th.data('id')}),
            dataType: 'json',
            success: function (data) {
                $('#myModal4 .photo img').attr('src', data.photo);
                $('#myModal4 h2').text(data.username);
                $('#myModal4 input.recipient_id').val(th.data('id'));
                $('#myModal4 .message_block').html(data.messages);
                $('#myModal4').modal('show');
            }
        });
        return false;
    });

    $(document).on('submit', '#myModal4_form', function () {
        if ($("#myModal4  #message_input").val().length > 0) {
            var form = $(this).serialize();
            var text = $('#myModal4  #message_input').val();
            $.ajax({
                type: "POST",
                url: "/ajax/submit_message2",
                data: (form),
                success: function (data) {
                    console.log(data);
                    $('#myModal4  .message_block').append('<div class="my_message_wrap"><div class="my_message">' + text + '</div></div>');
                    $("#myModal4  #message_input").val('').focus();
                    $('#myModal4  .message_block').scrollTop(9999999);
                }
            });
        }

        return false;
    });

    var checkMessagesFriendTimer;

    function checkMessagesFriend() {
        checkMessagesFriendTimer = setInterval(function () {
            $.ajax({
                url: '/ajax/check-messages-friend',
                type: "POST",
                data: ({recipient_id: $('#myModal4 .recipient_id').val()}),
                dataType: 'json',
                success: function (data) {
                    console.log(data.status);
                    if (data.status == 'new_message_sender') {
                        $('#myModal4 .message_block').append(data.html).scrollTop(9999999);
                    }
                }
            });
        }, 3000);
    }

    function stopCheckMessagesFriend() {
        clearInterval(checkMessagesFriendTimer);
    }

    var updateContactListTimer;
    function checkContactList() {
        updateContactListTimer = setInterval(function () {
            $.ajax({
                url: '/ajax/update-contact-list',
                type: "POST",
                data: ({}),
                dataType: 'json',
                success: function (data) {
                   $('.contacts_wrap').html(data.html);
                }
            });
        }, 3000);
    }

    function stopUpdateContactList() {
        clearInterval(updateContactListTimer);
    }
    checkContactList();

})