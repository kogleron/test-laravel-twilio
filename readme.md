# Twilio Test App

## Install

```bash
composer update
```

```bash
cp .env.example .env
```

```bash
gulp
```

Fill in mysql database credentials in .env

Fill in twilio credentials and other settings in .env
 - TWILIO_ACCOUNT_SID - account sid
 - TWILIO_AUTH_TOKEN - auth token
 - TWILIO_COUNTRIES_NUM - how many flags to show
 - TWILIO_DIAL_NUMBER - where to redirect incoming calls
 - TWILIO_THANKS_IN - in how many minutes send sms-thanks
 - TWILIO_THANKS_SHADE - after how many minutes we have to send another sms-thanks

In case you use selfsigned certificate run command

```bash
mv resources/php/TinyHttp.php vendor/twilio/sdk/Services/Twilio/
```