$(document).ready(function () {

    // jQuery validator global settings
    $.validator.setDefaults({
        errorElement: "label",
        errorPlacement: function (error, element) {
            error.addClass("help-block");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.closest('.form-group'));
            }
        },
        highlight: function (element, errorClass, validClass) {
            $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            $(element).parents(".formFld").addClass("has-error").removeClass("has-success");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
            $(element).parents(".formFld").addClass("has-success").removeClass("has-error");
        }
    });
    var user_status = false;
    // Override the default error message
    jQuery.extend(jQuery.validator.messages, {
        alphabets_space_validation: 'Please enter valid information.',
        country_code_validation: 'Please enter valid information.',
        required: 'This field is mandatory.',
        email: 'Please enter valid email.',
        number: 'Please enter valid information.',
        digits: 'Please enter valid information.',
        accept: 'Please accept the terms and conditions.',
        username: 'Username has already been taken'
    });

    // Alphabets space validation rule.
    jQuery.validator.addMethod("alphabets_space_validation", function (value, element) {

        return this.optional(element) || /^[a-zA-Z\u0080-\u024F\s\/\-\)\(\`\.\"\']+$/i.test(jQuery.trim(value));

    }, "");
    jQuery.validator.addMethod("username", function (value, element) {
        var status = checkUsername(jQuery.trim(value), $('#username').data('url'));
        if (status) {
            return true;
        } else {
            return false;
        }

    }, "Username has already been taken");
    jQuery.validator.addMethod("valid-email", function (value, element) {
        var status = checkEmail(jQuery.trim(value), $('#email').data('url'));
        if (status) {
            return true;
        } else {
            return false;
        }

    }, "Email has already been taken");

    

    //Check for duplicate username
    var checkUsername = function (value, url) {
        var status = false;
        $.ajax({
            async: false,
            type: "GET",
            data: {username: value},
            url: url,
            dataType: "html",
            success: function (data) {
                if (data == 1) {
                    status = true;
                } else {
                    status = false;
                }
            },
            error: function (data) {
                alert("There was an error processing your request. Please refresh and try again.");
            }
        });
        return status;
    }
    //Check for duplicate email
    var checkEmail = function (value, url) {
        var status = false;
        $.ajax({
            async: false,
            type: "GET",
            data: {email: value},
            url: url,
            dataType: "html",
            success: function (data) {
                if (data == 1) {
                    status = true;
                } else {
                    status = false;
                }
            },
            error: function (data) {
                alert("There was an error processing your request. Please refresh and try again.");
            }


        });
        return status;
    }
    // Sign in
    $('#form-signin').validate({
        submitHandler: function (form) {
            form.submit();
        }
    });

    // Sign up
    $('#form-signup').validate({
        submitHandler: function (form) {
            form.submit();
        },
    });

    // Forgot password
    $('#form-forgot-password').validate({
        submitHandler: function (form) {
            form.submit();
        }
    });

    // Reset password
    $('#form-reset-password').validate({
        submitHandler: function (form) {
            form.submit();
        }
    });

    // Update password
    $('#form-update-profile').validate({
        submitHandler: function (form) {
            form.submit();
        },
    });




});