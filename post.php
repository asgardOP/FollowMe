<?php
include('config.php');
$current = isset($_COOKIE['username']) ? $_COOKIE['username'] : '';
if(!$current){ header("Location: index.html"); exit; }

$flash = '';
$flashClass = '';

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $uploadDir = "posts/";
    if(!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $file = $_FILES['image'];
    if($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        
        if(!in_array($ext, $allowed)) {
            $flash = 'Invalid image type';
            $flashClass = 'error';
        } else {
            $safeName = time() . "_" . bin2hex(random_bytes(6)) . "." . $ext;
            $destination = $uploadDir . $safeName;
            
            if(move_uploaded_file($file['tmp_name'], $destination)) {
                $caption = isset($_POST['caption']) ? $_POST['caption'] : '';
                $sql = "INSERT INTO posts (username, image, caption) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($connect, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $current, $destination, $caption);
                
                if(mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: feed.php");
                    exit;
                } else {
                    $flash = 'Database error';
                    $flashClass = 'error';
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>New Post — Following Project</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Page-specific styles (do not modify navbar) */
        .page-wrap {
            max-width: 600px;  /* Changed from 900px */
            margin: 90px auto 60px;
            padding: 20px;
            display: block;  /* Changed from grid */
        }

        @media (max-width: 880px) {
            .page-wrap { margin: 80px 16px 40px; }
        }

        .post-card {
            background: #ebebebff;  /* Changed gradient to solid color */
            border-radius: 14px;
            padding: 24px;  /* Increased padding */
            color: #fff;
            border: 2px solid rgba(255,255,255,0.1);
            color: #000000ff;
        }

        .post-card h2 {
            margin: 0 0 12px 0;
            font-size: 20px;
            letter-spacing: 0.2px;
            color: #000000ff;
        }

        .dropzone {
            border: 2px dashed rgba(255,255,255,0.06);
            border-radius: 12px;
            background: rgba(71, 0, 61, 0.12);
            height: 360px;
            display:flex;
            align-items:center;
            justify-content:center;
            flex-direction:column;
            gap:12px;
            transition: border-color .18s, background .18s, transform .12s;
            cursor: pointer;
            overflow: hidden;
            color: black;
        }


        .dropzone.dragover {
            border-color: rgba(255,159,242,0.9);
            background: linear-gradient(180deg, rgba(255,159,242,0.03), rgba(255,159,242,0.02));
            transform: translateY(-4px);
        }

        .dz-icon {
            width:72px;height:72px;display:block;color:rgba(33, 33, 33, 0.85);
        }

        .dz-hint {
            color: rgba(23, 23, 23, 0.75);
            font-size:14px;
            text-align:center;
            max-width: 86%;
        }

        .side-card {
            background: #111111ff;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 8px 26px rgba(0,0,0,0.08);
            color: #111;
        }

        /* Updated textarea styling */
        .caption {
            width: 95%;
            min-height: 120px;
            resize: vertical;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            font-family: "Segoe UI", system-ui, -apple-system;
            font-size: 15px;
            color: #000000ff;
            background: rgba(255,255,255,0.05);
            transition: all 0.2s ease;
            margin: 8px 0;
            line-height: 1.5;
        }

        .caption::placeholder { 
            color: rgba(32, 32, 32, 0.4); 
            font-style: italic; 
        }

        .caption:focus {
            border-color: #ff9ff2;
            background: rgba(255,255,255,0.08);
            outline: none;
        }

        .meta-row {
            display:flex;
            gap:10px;
            align-items:center;
            justify-content:space-between;
            margin-top:10px;
        }

        /* Update button styles */
        .btn {
            background: #ff9ff2;
            border: none;
            color: #000;
            padding: 12px 20px;
            border-radius: 7px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn:hover {
            background: black;
            color: #ff9ff2;
        }

        .btn.ghost {
            background: transparent;
            color: #121212ff;
        }

        .btn.ghost:hover {
            opacity: 0.5;
        }

        .info {
            font-size:13px;
            color:#ccc;
        }

        .alert {
            margin-top:12px;
            padding:10px 12px;
            border-radius:8px;
            font-size:14px;
        }
        .alert.error { background: rgba(255,80,80,0.12); color: #ff8080; border:1px solid rgba(255,80,80,0.16); }
        .alert.success { background: rgba(50,200,120,0.08); color: #8ef0a0; border:1px solid rgba(50,200,120,0.15); }

        .file-meta {
            display:flex;
            gap:8px;
            align-items:center;
            font-size:13px;
            color:#ccc;
        }

        .char-count {
            font-size:12px;
            color:#666;
        }

        #dzContent{
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap: 10px;
        }

        #dropzone:hover {
            background: rgba(255, 101, 232, 0);
            border-color: rgba(255, 101, 232, 0.7);
        }
    </style>
</head>
<body>

    <?php include 'components/navbar.php'; ?>

    <main class="page-wrap">
        <section class="post-card">
            <h2>Create New Post</h2>

            <form id="postForm" method="post" enctype="multipart/form-data" novalidate>
                <label for="imageInput" class="dropzone" id="dropzone">
                    <input id="imageInput" name="image" type="file" accept="image/*" style="display:none">
                    <div id="dzContent">
                        <svg class="dz-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 5 17 10"></polyline>
                            <line x1="12" y1="5" x2="12" y2="21"></line>
                        </svg>
                        <div class="dz-hint">Click or drag & drop an image here to upload</div>
                        <div class="dz-hint" style="font-size:12px;opacity:0.8">Supported: jpg, jpeg, png, gif, webp — max recommended size 10MB</div>
                    </div>
                </label>

                <div style="margin-top:12px; display:flex; gap:8px; align-items:center; justify-content:space-between;">
                    <div class="file-meta" id="fileMeta" style="visibility:hidden;">
                        <strong id="fileName"></strong>
                        <span style="opacity:.6">•</span>
                        <span id="fileSize" style="opacity:.7"></span>
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="button" class="btn ghost" id="clearBtn" style="display:none">Clear</button>
                    </div>
                </div>

                <p style="margin:20px 0 8px 0;color:rgba(30, 30, 30, 0.6);font-size:14px">Write a caption</p>
                <textarea name="caption" id="caption" class="caption" 
                    placeholder="What's on your mind?"></textarea>

                <div class="meta-row">
                    <div class="info">
                        <span class="char-count" id="charCount">0 / 2200</span>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <a href="feed.php" class="btn ghost">Cancel</a>
                        <button class="btn" id="shareBtn" type="submit" disabled>Share Post</button>
                    </div>
                </div>

                <?php if($flash): ?>
                    <div class="alert <?php echo htmlspecialchars($flashClass); ?>">
                        <?php echo htmlspecialchars($flash); ?>
                    </div>
                <?php endif; ?>
            </form>
        </section>
    </main>

    <script>
        const imageInput = document.getElementById('imageInput');
        const dropzone = document.getElementById('dropzone');
        const fileMeta = document.getElementById('fileMeta');
        const fileNameEl = document.getElementById('fileName');
        const fileSizeEl = document.getElementById('fileSize');
        const clearBtn = document.getElementById('clearBtn');
        const shareBtn = document.getElementById('shareBtn');
        const caption = document.getElementById('caption');
        const charCount = document.getElementById('charCount');

        function humanFileSize(size) {
            const i = size === 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B','KB','MB','GB','TB'][i];
        }

        function setFile(file) {
            if(!file) return resetAll();
            fileMeta.style.visibility = 'visible';
            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = humanFileSize(file.size);
            clearBtn.style.display = 'inline-block';
            shareBtn.disabled = false;
        }

        function resetAll() {
            fileMeta.style.visibility = 'hidden';
            fileNameEl.textContent = '';
            fileSizeEl.textContent = '';
            clearBtn.style.display = 'none';
            shareBtn.disabled = true;
            imageInput.value = '';
        }

        dropzone.addEventListener('click', () => imageInput.click());

        imageInput.addEventListener('change', (e) => {
            const f = e.target.files[0];
            if(f) setFile(f);
        });

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('dragover');
        });

        ['dragleave','dragend','drop'].forEach(ev =>
            dropzone.addEventListener(ev, (e) => {
                e.preventDefault();
                dropzone.classList.remove('dragover');
            })
        );

        dropzone.addEventListener('drop', (e) => {
            const f = e.dataTransfer.files[0];
            if(f) {
                imageInput.files = e.dataTransfer.files;
                setFile(f);
            }
        });

        clearBtn.addEventListener('click', resetAll);

        caption.addEventListener('input', () => {
            const max = 2200;
            const len = caption.value.length;
            charCount.textContent = `${len} / ${max}`;
        });

        // initialize
        resetAll();
    </script>
</body>
</html>