$(document).ready(function () {
    $(window).load(function () {
        tabWidthControl();
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            if (typeof tabWidthControl2 === "function") {
                tabWidthControl2();
            }
        }
    });

    $(window).resize(function () {
        tabWidthControl();
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            if (typeof tabWidthControl2 === "function") {
                tabWidthControl2();
            }
        }
    });
    var Progress = function (progress) {
        $('#circle').circleProgress({
            value: progress,
            size: 40,
            fill: {
                gradient: ["green"]
            }
        });

    }
    $(document).on('click', '.btn-loading', function () {
        var $btn = $(this);
        $btn.button('loading');
        setTimeout(function () {
            $btn.button('reset');
        }, 1000);
    })
    // Tab width
    function tabWidthControl() {
        var tabWidth = 0;
        var lastLi = $('.profileNav ul li:last-child').innerWidth();
        $('.profileNav ul li').each(function () {
            tabWidth += $(this).innerWidth();
        });
        $('.profileNav ul').css({"width": tabWidth + 10});
    }

    // Remove data from hiddden modal
    $('body').on('hidden.bs.modal', '.modal', function () {
        $(this).removeData('bs.modal');
    });

    // Ajax modal window loading
    $(document).on('click', '.ajax-load-modal', function (event) {
        event.preventDefault();
        var URL = $(this).data('href');

        if (URL == '') {
            return false;
        }

        $.get(URL, function (response) {
            $('.modal-content').html(response.html)
            $('#napby-modal').modal({show: true});
        });
    });

    // Get the states by given country.
    $(document).on('change', '#user-country', function () {
        var country_id = $(this).val();

        $.get(NAPBY.base_url + '/states', {country_id: country_id}, function (response) {
            var states = response.states;
            var options = '';

            states.forEach(function (state) {
                options += '<option value="' + state.id + '">' + state.name + '</option>';
            });

            $('#user-state').empty().append(options);
        });
    });

    //File Upload
    $(document).on('click', '#fileupload', function (event) {
        jQuery('#fileupload').fileupload({
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            formData: {_token: $('#csrf_token').val(), folder: $('#fileupload').data('folder'), type: $('#fileupload').data('type')},
            url: $('#fileupload').data('link'),
            done: function (e, data) {
                //$('#circle').hide();
                $("#circle").fadeOut("slow");
                $('.btn-loading').removeAttr('disabled');
                var result = data.result;

                $(this).closest('.upload-holder').find('#thumbnail-preview').attr('src', result.thumb);
                $(this).closest('.upload-holder').find('#image-id').val(result.name);
            },
            progress: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $("#circle").show();
                Progress(progress);

            },
            change: function (e, data) {
                $('.btn-loading').attr('disabled', 'disabled');
            }
        })
    });
});