<!DOCTYPE html>
<html>
<head>
    <title>Twilio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link href="{{ asset(elixir("css/app.css")) }}" media="all" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="{{ asset(elixir("js/app.js")) }}"></script>

    <script>
        var urlPhone = "<?=url('/api/phone')?>";
        var urlCountries = "<?=url('/api/countries')?>";
    </script>
</head>
<body>
<div class="container">
    <div class="content">
        <div id="real-content">
            <div id="countries"></div>
            <div class="title" id="phone-number"></div>
        </div>
        <div id="loader"><img src="{{ asset('images/ajax-loader.gif') }}"></div>
    </div>
</div>
</body>
</html>