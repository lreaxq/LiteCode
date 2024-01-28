@echo off
color 07
cls
if /I not "%cd%" EQU "C:\Windows\System32" (
    echo Guncelleme isleminin devam etmesi icin Lutfen guncelleme dosyasini Yonetici olarak baslatin!
    powershell -Command "Start-Process -FilePath \"%~nx0\" -Verb RunAs"
    exit
)
set "cdd=C:\Program Files (x86)\"
mkdir liteCode
set "cdd=C:\Program Files (x86)\liteCode\"
cd %cdd%
echo LiteCode Derleyiciniz guncelleniyor...
del "litecode.exe"
powershell -Command "(New-Object System.Net.WebClient).DownloadFile('https://github.com/lreaxq/LiteCode/raw/main/files/runner/litecode.exe', 'C:\Program Files (x86)\liteCode\litecode.exe')"
echo Derleyiniz Guncellendi!
echo LiteShell Guncelleniyor...
del "%USERPROFILE%\Desktop\litecode.lc"
powershell -Command "(New-Object System.Net.WebClient).DownloadFile('https://raw.githubusercontent.com/lreaxq/LiteCode/main/files/shell/litecode.lc', '%USERPROFILE%\Desktop\litecode.lc')"
powershell -Command "(New-Object System.Net.WebClient).DownloadFile('https://raw.githubusercontent.com/lreaxq/LiteCode/main/files/shell/litecode.lc', 'litecode.lc')"
echo Shell Guncellendi!
powershell -Command "(New-Object System.Net.WebClient).DownloadFile('https://github.com/lreaxq/LiteCode/blob/main/files/Updater.bat', 'C:\Program Files (x86)\liteCode\updater.bat')"
echo Son ayarlamalar yapiliyor..
echo derleyici windows'a tanitiliyor...
set "lcPath=%cd%\litecode.exe"  
echo litecode uzantisi windows'a tanitiliyor...
reg add HKEY_CURRENT_USER\Software\Classes\.lc /ve /d "litecodefile" /f
reg add HKEY_CURRENT_USER\Software\Classes\litecodefile\shell\open\command /ve /d "\"%lcPath%\" \"%%1\"" /f
echo ".lc dosyasi icin kayit defteri girdisi olusturuldu."
echo LiteCode Basariyla Guncellendi!
pause
