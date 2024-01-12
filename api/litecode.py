import time
import os
import re
from gtts import gTTS
import pygame
import random
import speech_recognition as sr
import sys
sys.setrecursionlimit(10000000)
os.system('cls')
loop = "false"
directory = os.getcwd()
first_directory=directory
variables={'lc_version':'0.0.1',
           'i':'0',
           'term_startup':'0',
           'i':'0',
           'directory':directory
}
ifkosul="false"

def listen():
    global listenn
    recognizer = sr.Recognizer()
    with sr.Microphone() as source:
        print("Mikrofon dinleniyor...")
        recognizer.adjust_for_ambient_noise(source, duration=1)
        audio = recognizer.listen(source)
        try:
            print("Metin çeviriliyor...")
            text = recognizer.recognize_google(audio, language="tr-TR")
            listenn = text
        except sr.UnknownValueError:
            print("Hata: Ses anlasilmadi!")
            listenn = "HATA"
        except sr.RequestError as e:
            print(f"Hata oluştu; {e}")
            listenn = "HATA"

def cd(target_folder):
    try:
        os.chdir(target_folder)
        print("Changed to:", os.getcwd())
    except FileNotFoundError:
        print(f"Klasor '{target_folder}' mevcut degil.")
def lrun(filel):
    litecode.run(filel)

def argsdo():
    global variables, args
    degiskenler = re.findall(r"\$([a-zA-Z_]\w*)", args)
    for degisken in degiskenler:
        deger = variables.get(degisken, "Değer bulunamadı")
        args = args.replace(f"${degisken}", str(deger))

def text_to_speech(text):
    kelimeler = text.split()
    sanie = len(kelimeler)
    tts = gTTS(text=text, lang='tr')
    tts.save("output.mp3")
    pygame.init()
    pygame.mixer.music.load("output.mp3")
    pygame.mixer.music.play()
    pygame.mixer.music.set_endevent(pygame.USEREVENT)
    pygame.event.wait()
    time.sleep(sanie)
    pygame.mixer.music.stop()
    pygame.mixer.quit()
    pygame.quit()
    os.remove("output.mp3")

def setrunner(filename):
    global ffile, runner,readyrunner, loop
    ffile = filename
    runner = 'runner.lc'
    with open(ffile, 'r') as ffile:
        icerik = ffile.read()
    icerik = '\n'.join(line if '||' not in line else line.replace('||', '\n') for line in icerik.split('\n') if line.strip() and not line.strip().startswith('#'))
    with open(runner, 'w') as runner:
        runner.write(icerik)

class litecode():
    def run(file):
        global args, variables, ifkosul, readyrunner
        directory = os.getcwd()
        variables['directory']=directory
        setrunner(file)
        readyrunner = "runner.lc"
        file = open(readyrunner, "r")
        for satir in file:
            if satir.startswith(" "):
                if not eval(ifkosul):
                    continue
            satir = satir.split()
            komut = satir[0]
            arg = satir[1:]
            args = " ".join(satir[1:])
            ttime = time.localtime()
            variables["time_sec"]=ttime.tm_sec
            variables["time_min"]=ttime.tm_min
            variables["time_hour"]=ttime.tm_hour
            variables["time_mday"]=ttime.tm_mday
            variables["time_mon"]=ttime.tm_mon
            variables["time_year"]=ttime.tm_year


            #Standart
            if komut == "print" or komut == "echo" or komut == "write":
                argsdo()
                print(args)
            if komut == "wait":
                time.sleep(int(args))
            if komut == "run":
                if args == "":
                    print("Hata: (Run) eksik parametre!")
                else:
                    argsdo()
                    litecode.run(args)
            if komut == "set":
                argsdo()
                setarg=args.split("=")
                variables[setarg[0].strip()] = setarg[1].strip()
            if komut == "if":
                cumle = args
                basina_eklenecek_harfler = "variables['"
                sonuna_eklenecek_harfler = "']"
                kelimeler = cumle.split()
                yeni_cumle = ""
                for kelime in kelimeler:
                    if kelime.startswith("$"):
                        yeni_cumle += basina_eklenecek_harfler + kelime[1:] + sonuna_eklenecek_harfler + " "
                    else:
                       if kelime != "not" and kelime != "and" and kelime != "or" and kelime != "==" and kelime != "!=" and kelime != "<" and kelime != ">" and kelime != "<=" and kelime != ">=":
                            yeni_cumle += f'"{kelime}" '
                       else:
                            yeni_cumle += f'{kelime} '
                yeni_cumle = yeni_cumle.strip()
                ifkosul = yeni_cumle
            if komut=="math":
                argsdo()
                variables["math"]=eval(args)
            if komut=="input":
                inp=input(args)
                variables["input"]=inp
            if komut=="split":
                spdegi=arg[0][1:]
                sp=variables[spdegi].split()
                for i in range(len(sp)):
                    variables[f"{spdegi}{i+1}"] = sp[i]
            if komut=="clear" or komut =="cls":
                os.system('cls')
            if komut=="cd":
                argsdo()
                cd(args)
            if komut=="loop":
                loop = args
                if args == "true":
                    loop_dir = os.getcwd()
            if komut =="exit":
                exit()
            if komut =="random":
                args = f"{satir[1]} {satir[2]}"
                argsdo()
                rtemp=args.split()
                rand = random.randint(int(rtemp[0]), int(rtemp[1]))
                variables["random"]=rand
            #Text To Speak
            if komut=="speak":
                argsdo()
                text_to_speech(args)
            if komut =="listen":
                listen()
                variables["listen"]=listenn

if loop == "true":
    litecode.run('litecode.lc')
else:
    litecode.run('litecode.lc')
