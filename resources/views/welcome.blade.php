<!DOCTYPE html>
<html>
<head>
    <title>Laravel</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 100;
            font-family: 'Lato';
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: middle;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 96px;
        }

        #countries div {
            display: inline-block;
            margin-right: 10px;
            border: 1px solid #000000;
        }

        #countries img {
            width: 50px;
        }
    </style>

    <script>
        var appSettings = {
            flags_url: 'https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/2.2.0/flags/4x3/'
        };
    </script>

    <script>
        $(function () {

            var countries = ['us', 'ru', 'cr']

            for (var i = countries.length - 1; i >= 0; i--){

                $('<div><a href="#' + countries[i] + '"><img src="' + appSettings.flags_url + countries[i] + '.svg"></a></div>').appendTo('#countries');
            }

            $('#countries a').click(
                function(){

                }
            );

        });
    </script>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">Laravel 5</div>
        <div id="countries"></div>
    </div>
</div>
</body>
</html>
