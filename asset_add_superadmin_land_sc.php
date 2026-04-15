<?php
session_start();
if ($_SESSION["role"] !== "super saiyan") {
    header("Location: ../index.php");
    exit;
}
if (!isset($_SESSION['name'])) {
    echo "Session variable 'name' not set.";
    exit;
}

$currentUsername = $_SESSION['name'];
include("db_conn.php");
include("design&js.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PMO-LNI | Add Land Asset</title>
  <link rel="stylesheet" href="asset/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="asset/css/adminlte.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Serif+Display&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <script src="asset/jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

  <style>
    /* ─────────────────────────────────────────────
       DESIGN TOKENS
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
      --amber:      #d97706;
      --amber-dk:   #92400e;
      --amber-lt:   #fef3c7;
      --amber-bg:   #fffbeb;
      --red:        #dc2626;
      --red-dk:     #991b1b;
      --red-lt:     #fee2e2;
      --red-bg:     #fef2f2;
      --green:      #16a34a;
      --green-lt:   #f0fdf4;
      --green-bd:   #bbf7d0;
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
      --font-mono:  'DM Mono', 'SF Mono', 'Fira Code', monospace;
    }

    /* ── BASE ── */
    *, *::before, *::after { box-sizing: border-box; }
    body {
      font-family: var(--font) !important;
      font-size: 14px;
      background: var(--surface) !important;
      color: var(--ink);
      -webkit-font-smoothing: antialiased;
      margin: 0; padding: 0;
    }

    /* ── ANIMATIONS ── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: none; }
    }
    .anim-in  { animation: fadeUp .5s cubic-bezier(.25,.46,.45,.94) both; }
    .anim-d1  { animation-delay: .06s; }
    .anim-d2  { animation-delay: .12s; }
    .anim-d3  { animation-delay: .18s; }
    .anim-d4  { animation-delay: .24s; }
    .anim-d5  { animation-delay: .30s; }

    /* ── PAGE HEADER ── */
    .pmo-page-header {
      background: linear-gradient(135deg, #134e3a 0%, #059669 50%, #0d9488 100%);
      padding: 36px 40px 56px;
      display: flex; align-items: flex-end; justify-content: space-between;
      flex-wrap: wrap; gap: 16px;
      position: relative; overflow: hidden;
    }
    .pmo-page-header::before {
      content: ''; position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      pointer-events: none;
    }
    .pmo-page-header::after {
      content: ''; position: absolute; bottom: -1px; left: 0; right: 0;
      height: 28px; background: var(--surface); border-radius: 28px 28px 0 0;
    }
    .page-eyebrow {
      font-size: 11px; font-weight: 700;
      letter-spacing: .12em; text-transform: uppercase;
      color: rgba(255,255,255,.6); margin-bottom: 6px; position: relative;
    }
    .pmo-page-header h1 {
      font-family: var(--font-head); font-size: 2rem;
      color: var(--white); margin: 0; line-height: 1.15; position: relative;
    }
    .pmo-page-header h1 span { color: #86efac; }
    .pmo-breadcrumb {
      display: flex; align-items: center; gap: 8px;
      font-size: 12.5px; color: rgba(255,255,255,.5); position: relative;
    }
    .pmo-breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; transition: color .2s; }
    .pmo-breadcrumb a:hover { color: var(--white); }
    .pmo-breadcrumb i.fa-chevron-right { font-size: 8px; opacity: .5; }

    /* ── FORM CARD ── */
    .pmo-form-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      max-width: 960px;
      margin: -28px auto 40px;
      position: relative; z-index: 2;
      overflow: hidden;
    }
    .pmo-form-card:hover { box-shadow: var(--shadow); }

    .pmo-form-card-header {
      padding: 24px 32px;
      border-bottom: 1px solid var(--surface-2);
      display: flex; align-items: center; gap: 14px;
    }
    .pmo-form-card-header-icon {
      width: 46px; height: 46px; border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; flex-shrink: 0;
    }
    .pmo-form-card-header h2 {
      font-size: 17px; font-weight: 800; color: var(--ink); margin: 0;
    }
    .pmo-form-card-header small {
      font-size: 12.5px; color: var(--ink-3); display: block; margin-top: 2px;
    }

    .pmo-form-card-body {
      padding: 28px 32px;
      display: flex; flex-direction: column; gap: 20px;
    }

    .pmo-form-card-footer {
      padding: 18px 32px;
      border-top: 1px solid var(--border);
      background: var(--surface);
      display: flex; align-items: center; justify-content: flex-end; gap: 10px;
    }

    /* ── CLASSIFICATION PILL ── */
    .pmo-class-pill {
      display: inline-flex; align-items: center; gap: 8px;
      background: linear-gradient(135deg, var(--blue-bg) 0%, var(--blue-lt) 100%);
      border: 1.5px solid #bfdbfe; border-radius: var(--radius);
      padding: 9px 16px; font-size: 13px; font-weight: 700; color: #1e40af;
    }

    /* ── SECTION BLOCKS ── */
    .pmo-section {
      background: var(--white); border: 1px solid var(--border);
      border-radius: var(--radius-lg); overflow: hidden;
      border-left: 4px solid var(--blue);
      box-shadow: var(--shadow-xs); transition: box-shadow .2s;
    }
    .pmo-section:hover { box-shadow: var(--shadow-sm); }
    .pmo-section-head {
      background: linear-gradient(180deg, var(--surface) 0%, var(--surface-2) 100%);
      border-bottom: 1px solid var(--border);
      padding: 12px 18px;
      font-size: 11.5px; font-weight: 800; color: var(--blue);
      text-transform: uppercase; letter-spacing: .06em;
      display: flex; align-items: center; gap: 7px;
    }
    .pmo-section-body {
      padding: 18px 20px;
      display: grid; grid-template-columns: 1fr 1fr; gap: 16px;
    }
    .pmo-section-body.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
    .pmo-section-body.cols-1 { grid-template-columns: 1fr; }

    .pmo-section.land { border-left-color: var(--emerald); }
    .pmo-section.land .pmo-section-head {
      background: linear-gradient(180deg, var(--emerald-bg) 0%, #d1fae5 100%);
      border-bottom-color: var(--green-bd); color: #15803d;
    }
    .pmo-section.acct { border-left-color: var(--amber); }
    .pmo-section.acct .pmo-section-head {
      background: linear-gradient(180deg, var(--amber-bg) 0%, var(--amber-lt) 100%);
      border-bottom-color: #fde68a; color: var(--amber-dk);
    }
    .pmo-section.vehicle { border-left-color: var(--red); }
    .pmo-section.vehicle .pmo-section-head {
      background: linear-gradient(180deg, var(--red-bg) 0%, var(--red-lt) 100%);
      border-bottom-color: #fecaca; color: var(--red-dk);
    }

    /* ── FORM FIELDS ── */
    .pmo-field { display: flex; flex-direction: column; gap: 5px; }
    .pmo-field.span2 { grid-column: span 2; }
    .pmo-field.span3 { grid-column: span 3; }
    .pmo-label {
      font-size: 11.5px; font-weight: 700; color: var(--ink-3);
      text-transform: uppercase; letter-spacing: .05em;
    }
    .pmo-label .req { color: var(--red); font-weight: 800; }
    .pmo-input, .pmo-select, .pmo-textarea {
      border: 1.5px solid var(--border); border-radius: var(--radius-sm);
      background: var(--surface); padding: 9px 14px;
      font-size: 13px; font-family: var(--font);
      color: var(--ink); width: 100%; outline: none;
      transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .pmo-input:focus, .pmo-select:focus, .pmo-textarea:focus {
      border-color: var(--blue); background: var(--white);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .pmo-input::placeholder, .pmo-textarea::placeholder { color: var(--ink-4); }
    .pmo-textarea { resize: vertical; min-height: 72px; }

    .pmo-input-prefix { display: flex; }
    .pmo-prefix-text {
      padding: 9px 13px;
      border: 1.5px solid var(--border); border-right: none;
      border-radius: var(--radius-sm) 0 0 var(--radius-sm);
      background: linear-gradient(180deg, var(--surface-2), #e8ecf1);
      font-size: 13px; font-weight: 700; color: var(--ink-3); white-space: nowrap;
    }
    .pmo-input-prefix .pmo-input { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }

    /* ── BUTTONS ── */
    .pmo-btn {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 10px 20px; border-radius: var(--radius);
      font-size: 13px; font-weight: 600; font-family: var(--font);
      border: 1.5px solid var(--border); background: var(--white);
      color: var(--ink-2); cursor: pointer;
      transition: all .2s ease; text-decoration: none; white-space: nowrap;
    }
    .pmo-btn:hover { background: var(--surface-2); border-color: #cbd5e1; transform: translateY(-1px); box-shadow: var(--shadow-xs); }
    .pmo-btn-primary {
      background: linear-gradient(135deg, var(--emerald) 0%, #047857 100%);
      color: var(--white); border-color: var(--emerald);
    }
    .pmo-btn-primary:hover {
      background: linear-gradient(135deg, #047857 0%, var(--emerald-dk) 100%);
      border-color: #047857; color: var(--white);
      box-shadow: 0 4px 16px rgba(5,150,105,.25);
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
      .pmo-page-header { padding: 28px 20px 48px; }
      .pmo-form-card { margin: -28px 16px 30px; }
      .pmo-form-card-body { padding: 20px; }
      .pmo-section-body { grid-template-columns: 1fr; }
      .pmo-section-body.cols-3 { grid-template-columns: 1fr; }
      .pmo-field.span2, .pmo-field.span3 { grid-column: span 1; }
    }

    /* ── SCROLLBAR ── */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--surface); }
    ::-webkit-scrollbar-thumb { background: var(--ink-4); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--ink-3); }
  </style>
</head>

<body>

  <!-- ── Hero Page Header ── -->
  <div class="pmo-page-header anim-in">
    <div>
      <div class="page-eyebrow"><i class="fas fa-plus-circle me-1"></i> New Land Asset</div>
      <h1>Add <span>Asset</span></h1>
    </div>
    <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;position:relative;">
      <div class="pmo-breadcrumb">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="superadmin_land_sc.php">Land Assets</a>
        <i class="fas fa-chevron-right"></i>
        <span style="color:rgba(255,255,255,.9);font-weight:600;">Add Asset</span>
      </div>
    </div>
  </div>

  <!-- ── Form Card ── -->
  <div class="pmo-form-card anim-in anim-d1">
    <form method="post" action="superadmin_add_asset_process_land_sc.php">
      <input type="hidden" name="a_location_id" value="40">

      <div class="pmo-form-card-header">
        <div class="pmo-form-card-header-icon" style="background:var(--emerald-lt);border:1.5px solid var(--green-bd);color:var(--emerald);">
          <i class="fas fa-plus-circle"></i>
        </div>
        <div>
          <h2>Fixed Asset Information</h2>
          <small>Fill in the details below and click Add Asset to create a new record</small>
        </div>
      </div>

      <div class="pmo-form-card-body">

        <!-- Section 1: Classification -->
        <div class="pmo-section anim-in anim-d2">
          <div class="pmo-section-head"><i class="fas fa-tags"></i> Asset Group &amp; Classification</div>
          <div class="pmo-section-body">
            <div class="pmo-field">
              <label class="pmo-label">Group Classification</label>
              <select class="pmo-select" name="land_id">
                <?php
                include("db_conn.php");
                $res = mysqli_query($conn, "SELECT land_id, land_name FROM land_asset_location WHERE land_id != 1");
                while ($r = mysqli_fetch_assoc($res)) {
                  $selected = ($r['land_id'] == 24) ? "selected" : "";
                  echo "<option value='" . htmlspecialchars($r['land_id']) . "' $selected>" . htmlspecialchars($r['land_name']) . "</option>";
                }
                mysqli_close($conn);
                ?>
              </select>
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Classification of Asset</label>
              <?php
              include("db_conn.php");
              $res = mysqli_query($conn, "SELECT category_id, category_name FROM category WHERE category_id = 18");
              if ($r = mysqli_fetch_assoc($res)) {
                echo "<div class='pmo-class-pill'><i class='fas fa-tag'></i> " . htmlspecialchars($r['category_name']) . "</div>";
                echo "<input type='hidden' name='category_id' value='" . htmlspecialchars($r['category_id']) . "'>";
              }
              mysqli_close($conn);
              ?>
            </div>
          </div>
        </div>

        <!-- Section 2: Land Data -->
        <div class="pmo-section land anim-in anim-d3">
          <div class="pmo-section-head"><i class="fas fa-map-marked-alt"></i> Land Asset Data</div>
          <div class="pmo-section-body">
            <div class="pmo-field">
              <label class="pmo-label">Property Name / Article <span class="req">*</span></label>
              <input type="text" class="pmo-input" name="product_name" placeholder="e.g. Vacant Lot, Agricultural Land" required>
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Asset Number</label>
              <input type="text" class="pmo-input" name="asset_no" placeholder="e.g. LA-2024-001">
            </div>
            <div class="pmo-field span2">
              <label class="pmo-label">Description</label>
              <textarea class="pmo-textarea" name="specific_description" placeholder="Enter specific land / property description…"></textarea>
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Acquisition Date</label>
              <input type="text" class="pmo-input" name="product_received" placeholder="YYYY-MM-DD">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Unit Value</label>
              <div class="pmo-input-prefix">
                <span class="pmo-prefix-text">₱</span>
                <input type="text" class="pmo-input currency-input" name="unit_value" placeholder="0.00">
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Additional Details -->
        <div class="pmo-section anim-in anim-d4">
          <div class="pmo-section-head"><i class="fas fa-list-alt"></i> Additional Asset Details</div>
          <div class="pmo-section-body">
            <div class="pmo-field">
              <label class="pmo-label">Balance Card (Qty)</label>
              <input type="number" class="pmo-input" name="balance_card" placeholder="0">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Quantity On Hand</label>
              <input type="number" class="pmo-input" name="onhand_percount" placeholder="0">
            </div>
          </div>
        </div>

        <!-- Section 4: Accountability -->
        <div class="pmo-section acct anim-in anim-d5">
          <div class="pmo-section-head"><i class="fas fa-user-shield"></i> Accountability Information</div>
          <div class="pmo-section-body">
            <div class="pmo-field">
              <label class="pmo-label">Accountable Officer</label>
              <input type="text" class="pmo-input" name="accountable_officer" id="accountable_officer" placeholder="Full name of person in charge">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Assigned End User</label>
              <input type="text" class="pmo-input" name="enduser" id="enduser" placeholder="Full name of end user">
            </div>
            <div class="pmo-field span2">
              <label class="pmo-label">Accountable Office</label>
              <textarea class="pmo-textarea" name="whereabouts" placeholder="Office / department where asset is located…"></textarea>
            </div>
            <div class="pmo-field span2">
              <label class="pmo-label">Remarks</label>
              <textarea class="pmo-textarea" name="remarks" placeholder="Any additional notes…"></textarea>
            </div>
          </div>
        </div>

        <!-- Vehicle Details (hidden by default, shown for MOTOR VEHICLES) -->
        <div class="pmo-section vehicle anim-in" id="vehicle_form" style="display:none;">
          <div class="pmo-section-head"><i class="fas fa-car"></i> Vehicle Details</div>
          <div class="pmo-section-body cols-3">
            <div class="pmo-field">
              <label class="pmo-label">Unit of Measurement</label>
              <input type="text" class="pmo-input" name="unit_of_measurement" placeholder="set / unit">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Color</label>
              <input type="text" class="pmo-input" name="color" placeholder="Asset Color">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Conduction Sticker</label>
              <input type="text" class="pmo-input" name="conduction_sticker" placeholder="Conduction Sticker">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Plate Number</label>
              <input type="text" class="pmo-input" name="plate_no" placeholder="Plate Number">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Chassis Number</label>
              <input type="text" class="pmo-input" name="chassis_no" placeholder="Chassis Number">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Engine Number</label>
              <input type="text" class="pmo-input" name="engine_no" placeholder="Engine Number">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Year Model</label>
              <input type="text" class="pmo-input" name="year_model" placeholder="Year Model">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Property Number</label>
              <input type="text" class="pmo-input" name="property_no" placeholder="Lot / Property / Batch Number">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Condition</label>
              <input type="text" class="pmo-input" name="product_condition" placeholder="Good / Fair / Damaged">
            </div>
            <div class="pmo-field">
              <label class="pmo-label">Shortage/Overage (Qty)</label>
              <input type="number" class="pmo-input" name="so_quantity" placeholder="0">
            </div>
            <div class="pmo-field span2">
              <label class="pmo-label">Shortage/Overage (Value)</label>
              <div class="pmo-input-prefix">
                <span class="pmo-prefix-text">₱</span>
                <input type="text" class="pmo-input currency-input" name="so_value" placeholder="0.00">
              </div>
            </div>
          </div>
        </div>

      </div>

      <div class="pmo-form-card-footer">
        <a href="superadmin_land_sc.php" class="pmo-btn"><i class="fas fa-times"></i> Cancel</a>
        <button type="submit" class="pmo-btn pmo-btn-primary" name="add"><i class="fas fa-plus-circle"></i> Add Asset</button>
      </div>

    </form>
  </div>

<script>
  // Toggle vehicle form based on category
  function toggleVehicleForm() {
    var sel = document.getElementById('category_id');
    if (!sel) return;
    var name = sel.options[sel.selectedIndex].text;
    document.getElementById('vehicle_form').style.display = (name === 'MOTOR VEHICLES') ? 'block' : 'none';
  }
  if (document.getElementById('category_id')) {
    toggleVehicleForm();
    document.getElementById('category_id').addEventListener('change', toggleVehicleForm);
  }

  // Currency formatter
  document.querySelectorAll('.currency-input').forEach(function(el) {
    el.addEventListener('input', function(e) {
      var v = e.target.value.replace(/[^\d.]/g, '');
      var parts = v.split('.');
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
      if (parts[1] !== undefined) parts[1] = parts[1].slice(0, 2);
      e.target.value = parts.join('.');
    });
  });

  // Copy accountable officer to end user
  document.getElementById('accountable_officer').addEventListener('input', function() {
    document.getElementById('enduser').value = this.value;
  });
</script>
</body>
</html>
