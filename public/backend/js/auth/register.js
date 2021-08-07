$(function() {
    $(".btn-register").click(function(e) {
        e.preventDefault();
        var params = {};
        var flag = true;
        $('form[name="register-admin"] input, form[name="register-admin"] select').each(function() {
            var element = $(this)[0];
            var value = element.value;
            if (value !== "") {
                params[element.name] = value;
            } else {
                $.notify({
                    message: "The data field must not be empty"
                },{
                    type: "danger"
                }
                );
                $(this).focus();
                flag = false;
                return false;
            }
        });
        if(flag){
            $.ajax({
                type: "post",
                url: register_link,
                data: params,
                error: function(message) {
                    var errors = message.responseJSON;
                    errHtml = "";
                    $.each(errors.errors, function(index, msg) {
                        errHtml += "<li>" + msg[0] + "</li>";
                    });
                    $.notify({
                            message: errHtml
                        },{
                            type: "danger"
                        }
                    );
                },
                success: function(data) {
                    $.notify({
                            message: "Account registration successful"
                        },{
                            type: "success"
                        }
                    );
                }
            });
        }
    });
});
