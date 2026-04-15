<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PMO-LNI | Sign In</title>
  <link rel="stylesheet" href="asset/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="asset/css/adminlte.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Serif+Display&display=swap" rel="stylesheet">
  <link rel="shortcut icon" href="asset/img/logo1.png" type="image/x-icon">

  <style>
    /* ─────────────────────────────────────────────
       DESIGN TOKENS  (shared with the rest of the app)
    ───────────────────────────────────────────── */
    :root {
      --ink:        #0f172a;
      --ink-2:      #334155;
      --ink-3:      #64748b;
      --ink-4:      #94a3b8;
      --surface:    #f8fafc;
      --surface-2:  #f1f5f9;
      --border:     #e2e8f0;
      --white:      #ffffff;
      --blue:       #2563eb;
      --blue-dk:    #1d4ed8;
      --blue-lt:    #dbeafe;
      --blue-bg:    #eff6ff;
      --emerald:    #059669;
      --emerald-dk: #065f46;
      --emerald-lt: #d1fae5;
      --emerald-bg: #ecfdf5;
      --red:        #dc2626;
      --red-lt:     #fee2e2;
      --radius-sm:  8px;
      --radius:     12px;
      --radius-lg:  16px;
      --radius-xl:  24px;
      --shadow-xs:  0 1px 2px rgba(15,23,42,.04);
      --shadow-sm:  0 2px 8px rgba(15,23,42,.06);
      --shadow:     0 4px 24px rgba(15,23,42,.08);
      --shadow-lg:  0 12px 48px rgba(15,23,42,.12);
      --font:       'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      --font-head:  'DM Serif Display', serif;
    }

    /* ── BASE ── */
    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: var(--font);
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #134e3a 0%, #059669 50%, #0d9488 100%);
      position: relative;
      overflow: hidden;
      -webkit-font-smoothing: antialiased;
    }

    /* Subtle pattern overlay (matches app header) */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      pointer-events: none;
      z-index: 0;
    }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: none; }
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .anim-in  { animation: fadeUp .5s cubic-bezier(.25,.46,.45,.94) both; }
    .anim-d1  { animation-delay: .08s; }
    .anim-d2  { animation-delay: .16s; }
    .anim-d3  { animation-delay: .24s; }
    .anim-d4  { animation-delay: .32s; }

    /* ── LOGIN CARD ── */
    .login-card {
      position: relative;
      z-index: 1;
      width: 100%;
      max-width: 420px;
      margin: 24px;
    }

    .login-card-inner {
      background: var(--white);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg), 0 0 0 1px rgba(255,255,255,.1);
      overflow: hidden;
    }

    /* ── CARD HEADER ── */
    .login-header {
      text-align: center;
      padding: 36px 32px 24px;
    }

    .login-logo {
      width: 80px;
      height: 80px;
      margin: 0 auto 20px;
      border-radius: 50%;
      background: var(--emerald-bg);
      border: 2px solid var(--emerald-lt);
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .login-logo img {
      width: 56px;
      height: 56px;
      object-fit: contain;
    }

    .login-header h1 {
      font-family: var(--font-head);
      font-size: 1.65rem;
      color: var(--ink);
      margin: 0 0 6px;
      line-height: 1.2;
    }

    .login-header p {
      font-size: 13.5px;
      color: var(--ink-3);
      margin: 0;
      font-weight: 400;
    }

    /* ── FORM ── */
    .login-body {
      padding: 4px 32px 32px;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: var(--ink-2);
      margin-bottom: 6px;
    }

    .form-input-wrap {
      position: relative;
    }

    .form-input-wrap .input-icon {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--ink-4);
      font-size: 14px;
      pointer-events: none;
      transition: color .2s;
    }

    .form-input {
      width: 100%;
      padding: 11px 14px 11px 42px;
      font-family: var(--font);
      font-size: 14px;
      color: var(--ink);
      background: var(--surface);
      border: 1.5px solid var(--border);
      border-radius: var(--radius);
      outline: none;
      transition: border-color .2s, box-shadow .2s, background .2s;
    }

    .form-input::placeholder {
      color: var(--ink-4);
    }

    .form-input:hover {
      border-color: #cbd5e1;
    }

    .form-input:focus {
      border-color: var(--emerald);
      box-shadow: 0 0 0 3px rgba(5,150,105,.12);
      background: var(--white);
    }

    .form-input:focus ~ .input-icon {
      color: var(--emerald);
    }

    /* Password field with toggle */
    .password-wrap {
      position: relative;
    }

    .password-wrap .form-input {
      padding-right: 46px;
    }

    .password-toggle {
      position: absolute;
      right: 4px;
      top: 50%;
      transform: translateY(-50%);
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: transparent;
      border: none;
      border-radius: var(--radius-sm);
      color: var(--ink-4);
      cursor: pointer;
      transition: color .2s, background .2s;
    }

    .password-toggle:hover {
      color: var(--ink-2);
      background: var(--surface-2);
    }

    /* ── ERROR ALERT ── */
    .login-alert {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding: 12px 14px;
      background: var(--red-lt);
      border: 1px solid #fecaca;
      border-radius: var(--radius);
      margin-bottom: 18px;
      font-size: 13px;
      color: var(--red);
      line-height: 1.5;
    }

    .login-alert i {
      margin-top: 2px;
      flex-shrink: 0;
    }

    /* ── SUBMIT BUTTON ── */
    .btn-login {
      width: 100%;
      padding: 12px 20px;
      font-family: var(--font);
      font-size: 14.5px;
      font-weight: 600;
      color: var(--white);
      background: linear-gradient(135deg, #059669 0%, #0d9488 100%);
      border: none;
      border-radius: var(--radius);
      cursor: pointer;
      transition: box-shadow .2s, transform .15s;
      position: relative;
      overflow: hidden;
    }

    .btn-login:hover {
      box-shadow: 0 4px 16px rgba(5,150,105,.35);
      transform: translateY(-1px);
    }

    .btn-login:active {
      transform: translateY(0);
      box-shadow: var(--shadow-sm);
    }

    /* Loading state */
    .btn-login.is-loading {
      pointer-events: none;
      color: transparent;
    }

    .btn-login.is-loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      margin: -10px 0 0 -10px;
      border: 2.5px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: var(--white);
      animation: spin .6s linear infinite;
    }

    /* ── CARD FOOTER ── */
    .login-footer {
      padding: 16px 32px;
      background: var(--surface);
      border-top: 1px solid var(--border);
      text-align: center;
    }

    .login-footer p {
      margin: 0;
      font-size: 12px;
      color: var(--ink-4);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 480px) {
      .login-body { padding: 4px 24px 24px; }
      .login-header { padding: 28px 24px 20px; }
      .login-footer { padding: 14px 24px; }
    }
  </style>
</head>
<body>

  <div class="login-card anim-in">
    <div class="login-card-inner">

      <!-- Header -->
      <div class="login-header anim-in anim-d1">
        <div class="login-logo">
          <img src="asset/img/PPA1.png" alt="PMO-LNI Logo">
        </div>
        <h1>PMO-LNI</h1>
        <p>Sign in to your account to continue</p>
      </div>

      <!-- Form -->
      <div class="login-body">
        <form action="check-login.php" method="post" id="loginForm">

          <?php if (isset($_GET['error'])) { ?>
          <div class="login-alert anim-in">
            <i class="fas fa-exclamation-circle"></i>
            <span><?= htmlspecialchars($_GET['error']) ?></span>
          </div>
          <?php } ?>

          <!-- Username -->
          <div class="form-group anim-in anim-d2">
            <label for="username">Username</label>
            <div class="form-input-wrap">
              <input
                type="text"
                class="form-input"
                name="username"
                id="username"
                autocomplete="off"
                placeholder="Enter your username"
                required
              >
              <i class="fas fa-user input-icon"></i>
            </div>
          </div>

          <!-- Password -->
          <div class="form-group anim-in anim-d3">
            <label for="password">Password</label>
            <div class="form-input-wrap password-wrap">
              <input
                type="password"
                class="form-input"
                name="password"
                id="password"
                autocomplete="off"
                placeholder="Enter your password"
                required
              >
              <i class="fas fa-lock input-icon"></i>
              <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <!-- Submit -->
          <div class="anim-in anim-d4">
            <button type="submit" class="btn-login" id="btnLogin">
              Sign In
            </button>
          </div>

        </form>
      </div>

      <!-- Footer -->
      <div class="login-footer">
        <p>&copy; <?= date('Y') ?> PMO-LNI &middot; Land Asset Management</p>
      </div>

    </div>
  </div>

  <script>
    const passwordInput = document.getElementById('password');
    const toggleBtn     = document.getElementById('togglePassword');
    const loginForm     = document.getElementById('loginForm');
    const btnLogin      = document.getElementById('btnLogin');

    // Toggle password visibility
    toggleBtn.addEventListener('click', function () {
      const isPassword = passwordInput.type === 'password';
      passwordInput.type = isPassword ? 'text' : 'password';
      toggleBtn.querySelector('i').classList.toggle('fa-eye', !isPassword);
      toggleBtn.querySelector('i').classList.toggle('fa-eye-slash', isPassword);
    });

    // Submit with loading state
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      btnLogin.classList.add('is-loading');
      btnLogin.textContent = '';
      setTimeout(function () {
        loginForm.submit();
      }, 800);
    });
  </script>

</body>
</html>
