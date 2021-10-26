@ECHO OFF
cd ./
echo Enter server port (default 8000)
set Data=8000
set /p Data="Port: "
CLS
php -S 127.0.0.1:%Data% -t public/