<!DOCTYPE html>
<html>
<head>
    <title>Twilio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="<?= url('/styles.css') ?>" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="<?= url('/scripts.js') ?>"></script>
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
        <div id="loader"><img src="<?= url('/ajax-loader.gif') ?>"></div>
    </div>
</div>
</body>
</html>
