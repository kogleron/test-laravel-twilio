$(function () {

    var loader       = function (state) {

        if (state) {
            $('#real-content').hide();
            $('#loader').show();
        } else {
            $('#real-content').show();
            $('#loader').hide();
        }
    };
    var errorHandler = function (resp) {

        var msg = 'Some error';
        console.log(resp);
        if (resp.responseJSON && resp.responseJSON.errors)
            msg = resp.responseJSON.errors;

        $('#countries').hide();
        $('#phone-number').html(msg).show();
        loader(false);
    };

    loader(true);
    $.getJSON(urlCountries).success(function (countries) {

        for (var i = countries.length - 1; i >= 0; i--) {

            $('<div><a data-country=' + countries[i] + ' href="#' + countries[i] + '"><img src="' +
                'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.2.0/flags/4x3/' +
                countries[i] + '.svg"></a></div>').appendTo('#countries');
        }

        $('#countries a').click(function (e) {
                e.preventDefault();

                loader(true);
                $.getJSON(urlPhone + '/' + $(this).data('country')).success(function (resp) {

                    $('#countries').hide();
                    $('#phone-number').html(resp).show();
                    console.log(resp);
                    loader(false);
                }).error(errorHandler);

                return false;
            }
        );
        loader(false);
    }).error(errorHandler);
});
