<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('max_execution_time', 30000);
gc_collect_cycles();
# LiteCode Api
$api_ver = '0.0.1';
$api_package = 'Full';
//$lc_debug = true;
$if_result = false;
$variables = array(
    'math' => 0,
    'key' => 0,
);
$line_exp[0] = 0;
$line_exp[1] = 0;
if ($lc_debug) {
    console_log('LiteCode ' . $api_package . ' ' . $api_ver . ' başlatılıyor..');
}
#Derleyicinin çalışması için gerkli olan fonksiyonlar
function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}
function isLeadingSpace($sentence) {
    // Cümleyi başındaki ve sonundaki boşlukları temizle
    $trimmedSentence = trim($sentence);

    // Temizlenmiş cümlenin başı orijinal cümleyle aynı mı kontrol et
    return $trimmedSentence !== $sentence;
}
function wrapStringsWithQuotes($expression) {
    return preg_replace('/(\b[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\b)/', "'$1'", $expression);
}
function getVariables($var) {
    global $var_result,$variables;
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $var, $matches);
    foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $var_result = str_replace($match, $variables[$variable_name], $var);
        }
    }
}

// Komutlar
function lc_print($arg) {
    global $variables;
    // Değişkenleri eşleştirmek için düzenli ifade
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $arg, $matches);

    foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $arg = str_replace($match, $variables[$variable_name], $arg);
        }
    }

    echo $arg . '<br>';
}
function lc_set($arg) {
    global $variables;
    list($variable, $value) = explode('=', $arg, 2);
    $variable = trim($variable);
    $value = trim($value);

    // Değişkenleri eşleştirmek için düzenli ifade
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $value, $matches);

    foreach ($matches[0] as $match) {
        $match_name = trim($match, '$');
        if (isset($variables[$match_name])) {
            $value = str_replace($match, $variables[$match_name], $value);
        }
    }

    $variables[$variable] = $value;
}
function lc_math($args) {
    global $variables;
    // Değişkenleri eşleştirmek için düzenli ifade
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $args, $matches);
    foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $args = str_replace($match, $variables[$variable_name], $args);
        }
    }
    $result = eval("return $args;");
    $variables['math'] = $result;
}
function lc_if($args) {
    global $if_result;
    global $variables;
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $args, $matches);
    
    foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $args = str_replace($match, $variables[$variable_name], $args);
        }
    }
$wrappedExpression = wrapStringsWithQuotes($args);
   if (eval("return ($wrappedExpression);")) {
        $if_result = true;
    } else {
        $if_result = false;
    }
}
function lc_get($args) {
    global $variables,$arg1;
    if (isset($_POST[$arg1])){
        $variables['ifget'] = 'true'; 
        $post = $_POST[$arg1];
        $variables['get'] = $post;
      }else{
        //echo 'Hata: '.$args.' post bilgisi bulunamadı!<br>';
      }
}
function lc_call($args) {
    global $variables,$arg1;
    run($arg1);
}
function lc_wait($args) {
    global $variables, $arg1;
    sleep($arg1);
}
function lc_mwait($args) {
    global $variables, $arg1;
    usleep($arg1);
}
function lc_exit($args) {
    exit();
}
function lc_form($args) {
    global $arg1;
    if (!isset($arg1)) {
        $arg1 = 'post';
    }
    echo '<form method="'.$arg1.'">';
}
function lc_formEnd($args) {
    echo '</form>';
}
function lc_kill($args) {
    console_log('Program kapatılıyor.');
    unset($_POST);
    exit();
}
function lc_object($args) {
    // object fonksiyonu kapsamlıdır
    // argüman 1 = id
    // argüman 2 = fonksiyon(spawn,kill,in,visible(1/0))
    //      spawn   = obje spawnlar
    //      kill    = var olan objeyi öldürür
    //      in      = içeriğini değiştirir
    //      visible = objenin görünürlüğünü değiştirir
    // argüman 3 = değer

    // object spawn <object>* <name>* <id>* <value>
    // object in <id>* <value>*
    global $arg1, $arg2,$arg4, $variables,$args5, $args2, $args3,$args4, $arg3;
    $func = $arg1;
    if ($func == 'in') {
        $id = $arg2;
        $value = $args3;
        $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
        preg_match_all($pattern, $value, $matches);
        foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $value = str_replace($match, $variables[$variable_name], $value);
        }
    }
        echo "<script>document.getElementById('".$id."').innerHTML = '".$value."'</script>";
    }
    if ($func == 'spawn') {
        $object = $arg2;
        $id = $arg3;
        $value = $args4;
        $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
        preg_match_all($pattern, $value, $matches);
        foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $value = str_replace($match, $variables[$variable_name], $value);
        }
    }

        include('objects/'.$object.'.obj');  
    }
    if ($func == 'kill') {
        echo "<script>var killobj = document.getElementById('".$arg2."');killobj.remove(); </script>";
    }
    if ($func == 'visible') {
        $mode = $arg3;
        $id = $arg2;
        echo '<script>document.getElementById("'.$id.'").style.visibility = "'.$mode.'"; </script>';
    }
    if ($func == 'move') {
    // obj move <id>* <x>* <y>*
    $id = $arg2;
    $x = $arg3;
    $y = $arg4;
    $pattern = '/(\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
    preg_match_all($pattern, $x, $matches);
        foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $x = str_replace($match, $variables[$variable_name], $x);
        }
    }
    preg_match_all($pattern, $y, $matches);
        foreach ($matches[0] as $match) {
        $variable_name = trim($match, '$');
        if (isset($variables[$variable_name])) {
            $y = str_replace($match, $variables[$variable_name], $y);
        }
    }
    echo "
    <script>
    var obje = document.getElementById('".$id."').getBoundingClientRect();
    var top = obje.offsetTop;
    var left = obje.offsetLeft;
    </script>
    ";
    //$objx = "<script>document.writeln(top.obje.x);</script>";
    //$objy = "<script>document.writeln(top.obje.y);</script>";
    echo "
    <script>objj = document.getElementById('".$id."');
    objj.style.position = 'absolute';
    objj.style.top = '".$y."px';
    objj.style.left = '".$x."px';</script>
    ";
}
    if ($func == "getPos") {
    $id = $arg2;
        echo "<script>
    var obje = document.getElementById('".$id."').getBoundingClientRect();
    var x = obje.x
    var y = obje.y
    document.cookie = 'x=' + encodeURIComponent(obje.x);
    document.cookie = 'y=' + encodeURIComponent(obje.y);
    </script>";
    $variables['objx'] = $_COOKIE['x'];
    $variables['objy'] = $_COOKIE['y'];    
    echo '<script>
    function deleteCookie(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }
    deleteCookie("x");    
    </script>
        
        ';
}
}
function lc_getKey($args) {
    global $variables;
    $variables['key'] = file_get_contents("../temp.txt");
}
function lc_delTemp($args) {
    file_put_contents("../temp.txt", '000');
    echo "<script>var scriptTags = document.querySelectorAll('script');
    scriptTags.forEach(function(scriptTag) {
        scriptTag.parentNode.removeChild(scriptTag);
    });
        </script>";
        unset($_POST);
}
function lc_loop($args) {
    global $arg1,$arg2,$breakk,$variables;
    $file = $arg1;
    $loop = true;
    while ($loop) {
        $breakk = false;
        $startTimee = microtime(true);
        run($file);
        $endTimee = microtime(true);
        $executionTime = $endTimee - $startTimee;
        $fps = 1 / $executionTime;
        $variables['loop_fps'] = round($fps);
    }
}
function lc_exitLoop($args) {
    $loop = false;
}
function lc_skip($args) {
    global $breakk,$variables;
    $breakk = true;
}
?>
<input style="visibility: hidden;" id="lc_temp">
<script>
function saveVar(codee) {
    // PHP kodunu içeren bir dize oluştur
    var phpCode = codee

    // fetch API ile HTTP POST isteği yap
    fetch('../runphp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'phpCode=' + encodeURIComponent(phpCode),
    })
    .then(response => response.text())
    .then(data => {
        // Sunucudan gelen yanıtı al ve ekrana yazdır
        console.log(data);
    })
    .catch(error => {
        console.error('Hata:', error);
    });
}
document.addEventListener('keypress', function(event) {
    const key = event.key;
    if (key == ' ') {
        saveVar('space');
    }else{
        saveVar(key);
    };
});


</script>
<?php

if (!empty($_POST)) {
    $variables['ifpost'] = 'true'; 
}else{$variables['ifpost'] = 'false'; }
run('app.lc',true); //start litecode core (app.lc)
$grow = 1;
function run($filename, $ismain = false) {
    $startTime = microtime(true);
    $row = 0;
    global $if_result,$grow, $keyy ,$variables, $arg4, $args5 ,$memoryUsage,$memoryUsageMB ,$args2,$arg3, $args3 ,$lc_debug, $api_package, $api_ver, $arg1, $arg2,$args4;
    $file = fopen($filename, "r");
    if ($file) {
        $fileParts = explode('.', $filename);
        $fname = $fileParts[0];
        $fid = $fileParts[1];
        if ($fid != 'lc') {echo 'Hata: '.$filename.' bir litecode dosyası değildir!';exit();}
        if ($lc_debug) {
            console_log($filename.' Yürütülüyor:');
        }
        
        while (!feof($file)) {
            echo '<script>
            
            </script>
            ';
            $args = ' ';
            $memoryUsage = memory_get_usage();
            $memoryUsageMB = $memoryUsage / (1024 * 1024);
            $variables['lc_ram'] = round($memoryUsageMB, 2);
            $grow = $grow + 1;
            $row = $row + 1;
            $line = rtrim(fgets($file));
            global $breakk;
            if ($breakk == true) {
                continue;
            }
            if (empty($line) || $line[0] == '#') {
                continue;
            }
            $line_exp = explode(' ', $line);
            $command = $line_exp[0];
            if (isset($line_exp[1])) {$arg1 = $line_exp[1];}
            if (isset($line_exp[2])) {$arg2 = $line_exp[2];}
            if (isset($line_exp[3])) {$arg3 = $line_exp[3];}
            if (isset($line_exp[4])) {$arg4 = $line_exp[4];}
            if (isset($line_exp[2])) {$args2 = implode(' ', array_slice($line_exp, 2));};
            if (isset($line_exp[3])) {$args3 = implode(' ', array_slice($line_exp, 3));};
            if (isset($line_exp[4])) {$args4 = implode(' ', array_slice($line_exp, 4));};
            if (isset($line_exp[5])) {$args5 = implode(' ', array_slice($line_exp, 5));};
            if (isset($line_exp[1])) {$args = implode(' ', array_slice($line_exp, 1));};
            if (isLeadingSpace($line)) {
                $if_if = true;
                $line_exp = explode(' ', trim($line));
                $command = $line_exp[0];
            if (isset($line_exp[1])) {$arg1 = $line_exp[1];}
            if (isset($line_exp[2])) {$arg2 = $line_exp[2];}
            if (isset($line_exp[3])) {$arg3 = $line_exp[3];}
            if (isset($line_exp[4])) {$arg4 = $line_exp[4];}
            if (isset($line_exp[2])) {$args2 = implode(' ', array_slice($line_exp, 2));};
            if (isset($line_exp[3])) {$args3 = implode(' ', array_slice($line_exp, 3));};
            if (isset($line_exp[4])) {$args4 = implode(' ', array_slice($line_exp, 4));};
            if (isset($line_exp[5])) {$args5 = implode(' ', array_slice($line_exp, 5));};
            if (isset($line_exp[1])) {$args = implode(' ', array_slice($line_exp, 1));};
            if (empty($line) || $line[0] == '#') {
                continue;
            }
            if ($if_result) {
                if (function_exists('lc_' . $command)) {
                    if ($lc_debug) {
                        console_log($row . '|' . $command . ' ' . $args);
                    }
                    ob_flush();
                    flush();
                    call_user_func('lc_' . $command, $args);
                    ob_flush();
                    flush();
                } else {
                    if ($lc_debug) {
                        console_log($row . '*|'. $command . ' ' . $args);
                        echo 'Hata! Tanımsız fonksiyon, '.$command.'<br>';
                    }
                }
            }
            continue;
            }
            if (function_exists('lc_' . $command)) {
                if ($lc_debug) {
                    console_log($row . '|' . $command . ' ' . $args);
                }
                ob_flush();
                flush();
                call_user_func('lc_' . $command, $args);
                ob_flush();
                flush();
            } else {
                if ($lc_debug) {
                    console_log($row . '*|'. $command . ' ' . $args);
                    echo 'Hata! Tanımsız fonksiyon, '.$command.'<br>';
                }
            }
        }
        $breakk = false;
        fclose($file);
        if ($lc_debug&&$ismain == true) {
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime);
            console_log('LiteCode '.$grow.' Satır kod ' . $executionTime . ' saniyede işlendi!');
        }
    } else {
        echo 'Hata: '.$filename.' bulunamadı!';
    }
    
}
?>