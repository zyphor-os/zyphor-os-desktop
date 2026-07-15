<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile — Settings</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
:root{
  --bg:            #090c16;
  --bg-2:          #0d1122;
  --panel:         #121629;
  --panel-2:       #171c33;
  --stroke:        #262c4a;
  --text:          #eef1fb;
  --muted:         #8c93b8;
  --faint:         #545c80;
  --accent:        #3fd6e8;   /* logo cyan */
  --accent-blue:   #3f6fe0;   /* logo blue */
  --accent-violet: #7c5ce8;   /* logo violet */
  --accent-dim:    #3fd6e82e;
  --danger:        #ef5b7a;
  --warn:          #e0a45c;
  --radius:        10px;
}

*{ box-sizing:border-box; }

body{
  background:
    radial-gradient(1100px 550px at 88% -8%, #3fd6e814, transparent 60%),
    radial-gradient(900px 500px at 8% 108%, #7c5ce81a, transparent 55%),
    var(--bg);
  color:var(--text);
  font-family:'Inter', sans-serif;
  min-height:100vh;
  -webkit-font-smoothing:antialiased;
}

.mono{ font-family:'JetBrains Mono', monospace; }
.brand-font{ font-family:'Space Grotesk', sans-serif; }

/* ---------- title bar (ties to zyphor desktop chrome) ---------- */
.titlebar{
  height:38px;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:0 14px;
  background:#070912;
  border-bottom:1px solid var(--stroke);
  -webkit-app-region: drag;
  user-select:none;
}
.titlebar .dots{ display:flex; gap:8px; -webkit-app-region:no-drag; }
.titlebar .dot{ width:11px; height:11px; border-radius:50%; }
.dot.red{ background:#e5586b; } .dot.yellow{ background:#e0a45c; } .dot.green{ background:#3ecf8e; }
.titlebar .brand{
  display:flex; align-items:center; gap:9px;
  -webkit-app-region:no-drag;
}
.brand-mark{
  width:20px; height:20px; flex-shrink:0;
}
.brand-word{
  font-size:13px; font-weight:700; letter-spacing:.5px;
  color:var(--text);
}
.brand-word span{ font-weight:500; color:var(--muted); }
.titlebar .crumb{
  font-size:11.5px; color:var(--faint); letter-spacing:.3px;
}
.titlebar .crumb .path{ color:var(--muted); }
.titlebar .crumb .path::before{ content:"/"; color:var(--faint); margin:0 2px; }

/* ---------- shell layout ---------- */
.shell{
  display:flex;
  justify-content:center;
  min-height:calc(100vh - 38px);
}

/* ---------- content ---------- */
.content{
  width:100%;
  padding:34px 42px 60px;
  max-width:720px;
}
.page-head{ margin-bottom:28px; }
.page-eyebrow{
  font-size:11px; letter-spacing:1.5px; text-transform:uppercase;
  color:var(--accent); font-weight:600; margin-bottom:6px;
}
.page-title{ font-size:24px; font-weight:700; letter-spacing:-.2px; font-family:'Space Grotesk', sans-serif; }
.page-sub{ color:var(--muted); font-size:13.5px; margin-top:4px; }

.panel{
  background:var(--panel);
  border:1px solid var(--stroke);
  border-radius:var(--radius);
  padding:26px;
  margin-bottom:20px;
}
.panel-title{
  font-size:14px; font-weight:600; margin-bottom:2px;
  display:flex; align-items:center; gap:8px;
}
.panel-title i{ color:var(--accent); font-size:13px; }
.panel-desc{ color:var(--faint); font-size:12.5px; margin-bottom:18px; }

/* avatar block */
.avatar-row{ display:flex; align-items:center; gap:20px; }
.avatar-big{
  width:76px; height:76px; border-radius:50%;
  background:linear-gradient(135deg, var(--accent-blue), var(--accent) 55%, var(--accent-violet));
  display:flex; align-items:center; justify-content:center;
  font-weight:700; font-size:26px; color:#080a14;
  position:relative; overflow:hidden; flex-shrink:0;
  border:2px solid var(--stroke);
  font-family:'Space Grotesk', sans-serif;
}
.avatar-big img{ width:100%; height:100%; object-fit:cover; }
.avatar-actions .btn{ margin-right:8px; }

/* form styling */
label.field-label{
  font-size:12px; color:var(--muted); font-weight:600;
  margin-bottom:6px; display:block;
}
.form-control, .form-select{
  background:var(--panel-2);
  border:1px solid var(--stroke);
  color:var(--text);
  font-size:13.5px;
  padding:9px 12px;
  border-radius:8px;
}
.form-control:focus, .form-select:focus{
  background:var(--panel-2);
  border-color:var(--accent);
  box-shadow:0 0 0 3px var(--accent-dim);
  color:var(--text);
}
.form-control::placeholder{ color:var(--faint); }
.form-control[readonly]{ color:var(--faint); }
textarea.form-control{ resize:vertical; min-height:80px; }

.hint{ font-size:11.5px; color:var(--faint); margin-top:5px; }
.hint.err{ color:var(--danger); }
.field-ok{ border-color:#3ecf8e !important; }
.field-bad{ border-color:var(--danger) !important; }

.btn-accent{
  background:linear-gradient(120deg, var(--accent-blue), var(--accent));
  border:1px solid transparent;
  color:#080a14; font-weight:700; font-size:13px;
  padding:8px 18px; border-radius:8px;
  transition:filter .15s ease;
}
.btn-accent:hover{ filter:brightness(1.1); color:#080a14; }
.btn-ghost{
  background:transparent; border:1px solid var(--stroke);
  color:var(--muted); font-size:13px; font-weight:500;
  padding:8px 16px; border-radius:8px;
}
.btn-ghost:hover{ background:var(--panel-2); color:var(--text); border-color:var(--faint); }
.btn-danger-ghost{
  background:transparent; border:1px solid #e5586b4d;
  color:var(--danger); font-size:13px; font-weight:500;
  padding:8px 16px; border-radius:8px;
}
.btn-danger-ghost:hover{ background:#e5586b1a; }

/* toast */
#toastHost{ position:fixed; bottom:22px; right:22px; z-index:2000; }
.zy-toast{
  background:var(--panel); border:1px solid var(--stroke); border-left:3px solid var(--accent);
  color:var(--text); padding:12px 16px; border-radius:8px; font-size:13px;
  box-shadow:0 10px 30px rgba(0,0,0,.4); margin-top:10px; min-width:240px;
  display:flex; align-items:center; gap:10px;
}
.zy-toast i{ color:var(--accent); }
.zy-toast.err{ border-left-color:var(--danger); }
.zy-toast.err i{ color:var(--danger); }

@media (max-width: 860px){
  .content{ padding:24px; }
}
</style>
</head>
<body>

<div class="titlebar">
  <div class="dots">
    <div class="dot red"></div><div class="dot yellow"></div><div class="dot green"></div>
  </div>
  <div class="brand">
    <div class="brand-word">ZYPHOR<span>OS</span></div>
  </div>
  <div class="crumb mono">settings<span class="path">profile</span></div>
</div>

<div class="shell">

  <!-- ================= CONTENT ================= -->
  <main class="content">

    <!-- ---------- PROFILE ---------- -->
    <section id="tab-profile">
      <div class="page-head">
        <div class="page-eyebrow mono">01 · Identity</div>
        <div class="page-title">Profile</div>
        <div class="page-sub">This is how you appear across the app and to your team.</div>
      </div>

      <div class="panel">
        <div class="panel-title"><i class="fa-solid fa-circle-user"></i> Avatar</div>
        <div class="panel-desc">PNG or JPG, square, up to 2MB.</div>
        <div class="avatar-row">
          <div class="avatar-big mono" id="avatarPreview">MJ</div>
          <div class="avatar-actions">
            <input type="file" id="avatarInput" accept="image/png, image/jpeg" hidden>
            <button class="btn btn-accent" id="btnUpload"><i class="fa-solid fa-upload me-1"></i>Upload</button>
            <button class="btn btn-ghost" id="btnRemoveAvatar">Remove</button>
          </div>
        </div>
      </div>

      <form id="profileForm">
        <div class="panel">
          <div class="panel-title"><i class="fa-solid fa-address-card"></i> Details</div>
          <div class="panel-desc">Your name and bio are visible to other Zyphor OS contributors.</div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="field-label">Display name</label>
              <input type="text" class="form-control" id="fName" value="Mark Jason" maxlength="40">
            </div>
            <div class="col-md-6">
              <label class="field-label">Username</label>
              <input type="text" class="form-control mono" id="fHandle" value="markjason" maxlength="24">
              <div class="hint">Used in commit signatures and CLI prompts.</div>
            </div>
            <div class="col-md-6">
              <label class="field-label">Email</label>
              <input type="email" class="form-control" id="fEmail" value="mark@zyphor-os.dev">
            </div>
            <div class="col-md-6">
              <label class="field-label">Role</label>
              <select class="form-select" id="fRole">
                <option selected>Lead Maintainer</option>
                <option>Contributor</option>
                <option>Security Tester</option>
                <option>Graphic Artist</option>
              </select>
            </div>
            <div class="col-12">
              <label class="field-label">Bio</label>
              <textarea class="form-control" id="fBio" placeholder="Say something about what you build...">Building Zyphor OS — a Debian-based distro with a custom CLI and package manager.</textarea>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-ghost" id="btnDiscard">Discard</button>
          <button type="submit" class="btn btn-accent">Save changes</button>
        </div>
      </form>
    </section>

  </main>
</div>

<div id="toastHost"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script>
$(function () {

  /* ---------- toast helper ---------- */
  function toast(msg, type) {
    var icon = type === 'err' ? 'fa-circle-exclamation' : 'fa-circle-check';
    var $t = $('<div class="zy-toast ' + (type === 'err' ? 'err' : '') + '"><i class="fa-solid ' + icon + '"></i><span>' + msg + '</span></div>');
    $('#toastHost').append($t);
    setTimeout(function () { $t.fadeOut(200, function () { $(this).remove(); }); }, 2600);
  }

  /* ---------- persistence (local to this device) ---------- */
  var STORE_KEY = 'zyphor_profile_v1';
  function loadStore() {
    try { return JSON.parse(localStorage.getItem(STORE_KEY)) || {}; }
    catch (e) { return {}; }
  }
  function saveStore(data) {
    var current = loadStore();
    localStorage.setItem(STORE_KEY, JSON.stringify($.extend(current, data)));
  }

  function initials(name) {
    return name.trim().split(/\s+/).map(function (w) { return w[0]; }).join('').substring(0, 2).toUpperCase();
  }

  function refreshIdentity() {
    var name = $('#fName').val().trim() || 'Unnamed';
    var avatarSrc = loadStore().avatar;
    if (!avatarSrc) {
      $('#avatarPreview').text(initials(name));
    }
  }

  /* apply saved state on load */
  (function hydrate() {
    var s = loadStore();
    if (s.name) $('#fName').val(s.name);
    if (s.handle) $('#fHandle').val(s.handle);
    if (s.email) $('#fEmail').val(s.email);
    if (s.role) $('#fRole').val(s.role);
    if (s.bio) $('#fBio').val(s.bio);
    if (s.avatar) {
      $('#avatarPreview').html('<img src="' + s.avatar + '">');
    }
    refreshIdentity();
  })();

  /* ---------- avatar upload ---------- */
  $('#btnUpload').on('click', function () { $('#avatarInput').trigger('click'); });

  $('#avatarInput').on('change', function (e) {
    var file = e.target.files[0];
    if (!file) return;
    if (file.size > 2 * 1024 * 1024) { toast('Image must be under 2MB', 'err'); return; }
    var reader = new FileReader();
    reader.onload = function (ev) {
      var src = ev.target.result;
      $('#avatarPreview').html('<img src="' + src + '">');
      saveStore({ avatar: src });
      toast('Avatar updated');
    };
    reader.readAsDataURL(file);
  });

  $('#btnRemoveAvatar').on('click', function () {
    saveStore({ avatar: null });
    refreshIdentity();
    toast('Avatar removed');
  });

  /* ---------- profile form ---------- */
  $('#profileForm').on('submit', function (e) {
    e.preventDefault();
    var email = $('#fEmail').val().trim();
    var emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    $('#fEmail').toggleClass('field-bad', !emailOk).toggleClass('field-ok', emailOk);
    if (!emailOk) { toast('Enter a valid email address', 'err'); return; }

    saveStore({
      name: $('#fName').val().trim(),
      handle: $('#fHandle').val().trim(),
      email: email,
      role: $('#fRole').val(),
      bio: $('#fBio').val().trim()
    });
    refreshIdentity();
    toast('Profile saved');
  });

  $('#btnDiscard').on('click', function () {
    location.reload();
  });

});
</script>
</body>
</html>
