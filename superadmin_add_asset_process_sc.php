<?php
session_start();
if ($_SESSION["role"] !== "super saiyan") {
  header("Location: ../index.php");
  exit;
}

$username = $_SESSION['username'];
$username = $_SESSION['name'];
include("db_conn.php");

// ── Handle delete BEFORE any HTML output ──
if (isset($_GET['asset_id'])) {
  $assetId = $_GET['asset_id'];
  $sql  = "DELETE FROM asset WHERE asset_id = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $assetId);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
  $basePath = strtok($_SERVER["REQUEST_URI"], '?');
  header("Location: " . $basePath . "?deleted=true");
  exit;
}

if (isset($_POST['add'])) {
  $_SESSION['product_added'] = true;
  $basePath = strtok($_SERVER["REQUEST_URI"], '?');
  header("Location: " . $basePath . "?success=true");
  exit;
}

include("design&js.php");
include("Parts/superadmin/sidebar.php");
include("Parts/navbar.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PMO-LNI | Land Asset</title>
  <link rel="stylesheet" href="asset/fontawesome/css/all.min.css">
  <link rel="stylesheet" href="asset/css/adminlte.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=DM+Serif+Display&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
  <script src="asset/jquery/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>

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
      --violet:     #7c3aed;
      --violet-dk:  #4c1d95;
      --violet-lt:  #ede9fe;
      --violet-bg:  #f5f3ff;
      --red:        #dc2626;
      --red-dk:     #991b1b;
      --red-lt:     #fee2e2;
      --red-bg:     #fef2f2;
      --sky:        #0284c7;
      --sky-lt:     #e0f2fe;
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

    /* ─────────────────────────────────────────────
       BASE RESET
    ───────────────────────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; }
    body {
      font-family: var(--font) !important;
      font-size: 14px;
      background: var(--surface) !important;
      color: var(--ink);
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    /* ─────────────────────────────────────────────
       ANIMATIONS
    ───────────────────────────────────────────── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: none; }
    }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .6; } }
    .anim-in  { animation: fadeUp .5s cubic-bezier(.25,.46,.45,.94) both; }
    .anim-d1  { animation-delay: .06s; }
    .anim-d2  { animation-delay: .12s; }
    .anim-d3  { animation-delay: .18s; }
    .anim-d4  { animation-delay: .24s; }
    .anim-d5  { animation-delay: .30s; }
    .anim-d6  { animation-delay: .36s; }

    /* ─────────────────────────────────────────────
       HERO PAGE HEADER
    ───────────────────────────────────────────── */
    .pmo-page-header {
      background: linear-gradient(135deg, #134e3a 0%, #059669 50%, #0d9488 100%);
      padding: 36px 40px 56px;
      margin-bottom: 0;
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 16px;
      position: relative;
      overflow: hidden;
    }
    .pmo-page-header::before {
      content: '';
      position: absolute; inset: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      pointer-events: none;
    }
    .pmo-page-header::after {
      content: '';
      position: absolute; bottom: -1px; left: 0; right: 0;
      height: 28px;
      background: var(--surface);
      border-radius: 28px 28px 0 0;
    }
    .page-eyebrow {
      font-size: 11px; font-weight: 700;
      letter-spacing: .12em; text-transform: uppercase;
      color: rgba(255,255,255,.6); margin-bottom: 6px;
      position: relative;
    }
    .pmo-page-header h1 {
      font-family: var(--font-head);
      font-size: 2rem;
      color: var(--white);
      margin: 0; line-height: 1.15;
      position: relative;
    }
    .pmo-page-header h1 span { color: #86efac; }
    .pmo-breadcrumb {
      display: flex; align-items: center; gap: 8px;
      font-size: 12.5px; color: rgba(255,255,255,.5);
      position: relative;
    }
    .pmo-breadcrumb a { color: rgba(255,255,255,.7); text-decoration: none; transition: color .2s; }
    .pmo-breadcrumb a:hover { color: var(--white); }
    .pmo-breadcrumb i.fa-chevron-right { font-size: 8px; opacity: .5; }
    .header-btns { display: flex; gap: 10px; flex-wrap: wrap; position: relative; }

    /* ─────────────────────────────────────────────
       STAT CARDS
    ───────────────────────────────────────────── */
    .pmo-stats-row {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 16px;
      padding: 0 32px;
      margin-top: -32px;
      margin-bottom: 24px;
      position: relative;
      z-index: 2;
    }
    .pmo-stat {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      display: flex;
      align-items: center;
      gap: 16px;
      box-shadow: var(--shadow-sm);
      transition: transform .2s, box-shadow .2s;
    }
    .pmo-stat:hover { transform: translateY(-3px); box-shadow: var(--shadow); }
    .pmo-stat-icon {
      width: 50px; height: 50px;
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 21px; flex-shrink: 0;
    }
    .pmo-stat-icon.blue   { background: var(--blue-lt); color: var(--blue); }
    .pmo-stat-icon.green  { background: var(--emerald-lt); color: var(--emerald); }
    .pmo-stat-icon.amber  { background: var(--amber-lt); color: var(--amber); }
    .pmo-stat-icon.violet { background: var(--violet-lt); color: var(--violet); }
    .pmo-stat-value {
      font-size: 1.5rem; font-weight: 800;
      color: var(--ink); line-height: 1;
    }
    .pmo-stat-label {
      font-size: 11.5px; font-weight: 600;
      color: var(--ink-3); text-transform: uppercase;
      letter-spacing: .06em; margin-top: 3px;
    }

    /* ─────────────────────────────────────────────
       TOOLBAR
    ───────────────────────────────────────────── */
    .pmo-toolbar {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 14px 22px;
      margin: 0 32px 16px;
      box-shadow: var(--shadow-xs);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 12px;
    }
    .pmo-toolbar-left  { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .pmo-toolbar-right { display: flex; align-items: center; gap: 8px; }

    .pmo-search-wrap { position: relative; }
    .pmo-search-wrap i {
      position: absolute; left: 12px; top: 50%;
      transform: translateY(-50%);
      font-size: 12px; color: var(--ink-4);
    }
    .pmo-search-input {
      border: 1.5px solid var(--border);
      background: var(--surface);
      border-radius: var(--radius);
      padding: 9px 14px 9px 34px;
      font-size: 13px; font-family: var(--font);
      color: var(--ink); width: 260px; outline: none;
      transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .pmo-search-input:focus {
      border-color: var(--blue);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }

    .pmo-filter-select {
      border: 1.5px solid var(--border);
      background: var(--surface);
      border-radius: var(--radius);
      padding: 9px 14px;
      font-size: 13px; font-family: var(--font);
      color: var(--ink-2); outline: none; cursor: pointer;
      transition: border-color .2s;
    }
    .pmo-filter-select:focus { border-color: var(--blue); }

    /* ─────────────────────────────────────────────
       BUTTONS
    ───────────────────────────────────────────── */
    .pmo-btn {
      display: inline-flex; align-items: center; gap: 7px;
      padding: 10px 20px; border-radius: var(--radius);
      font-size: 13px; font-weight: 600; font-family: var(--font);
      border: 1.5px solid var(--border); background: var(--white);
      color: var(--ink-2); cursor: pointer;
      transition: all .2s ease; text-decoration: none; white-space: nowrap;
    }
    .pmo-btn:hover { background: var(--surface-2); border-color: #cbd5e1; transform: translateY(-1px); box-shadow: var(--shadow-xs); }
    .pmo-btn:active { transform: translateY(0); }
    .pmo-btn-primary {
      background: linear-gradient(135deg, var(--emerald) 0%, #047857 100%);
      color: var(--white); border-color: var(--emerald);
    }
    .pmo-btn-primary:hover {
      background: linear-gradient(135deg, #047857 0%, var(--emerald-dk) 100%);
      border-color: #047857; color: var(--white);
      box-shadow: 0 4px 16px rgba(5,150,105,.25);
    }
    .pmo-btn-blue {
      background: linear-gradient(135deg, var(--blue) 0%, var(--blue-dk) 100%);
      color: var(--white); border-color: var(--blue);
    }
    .pmo-btn-blue:hover {
      background: linear-gradient(135deg, var(--blue-dk) 0%, #1e40af 100%);
      border-color: var(--blue-dk); color: var(--white);
      box-shadow: 0 4px 16px rgba(37,99,235,.25);
    }
    .pmo-btn-sm { padding: 6px 14px; font-size: 12px; }

    /* ─────────────────────────────────────────────
       TABLE CARD
    ───────────────────────────────────────────── */
    .pmo-table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      margin: 0 32px 40px;
      transition: box-shadow .3s;
    }
    .pmo-table-card:hover { box-shadow: var(--shadow); }
    .pmo-table-card .card-header-bar {
      padding: 16px 24px;
      border-bottom: 1px solid var(--surface-2);
      display: flex; align-items: center; justify-content: space-between;
    }
    .pmo-table-card .card-header-bar h6 {
      font-size: 14.5px; font-weight: 700; color: var(--ink); margin: 0;
      display: flex; align-items: center; gap: 8px;
    }
    .pmo-table-card .card-header-bar h6 i { color: var(--emerald); }
    .table-responsive { overflow-x: auto; }

    #assetTable {
      width: 100% !important;
      border-collapse: separate;
      border-spacing: 0;
      font-size: 13px;
    }
    #assetTable thead th {
      background: linear-gradient(180deg, var(--surface-2) 0%, #edf2f7 100%) !important;
      color: var(--ink-2) !important;
      font-size: 11px !important; font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: .06em !important;
      border: none !important;
      border-bottom: 2px solid var(--border) !important;
      padding: 14px 16px !important;
      white-space: nowrap;
      position: sticky; top: 0; z-index: 2;
    }
    #assetTable tbody tr {
      border-bottom: 1px solid var(--surface-2);
      transition: background .15s;
    }
    #assetTable tbody tr:last-child { border-bottom: none; }
    #assetTable tbody tr:hover { background: var(--blue-bg) !important; }
    #assetTable tbody tr:nth-child(even) { background: rgba(248,250,252,.5); }
    #assetTable tbody tr:nth-child(even):hover { background: var(--blue-bg) !important; }
    #assetTable td {
      padding: 13px 16px;
      color: var(--ink);
      vertical-align: middle;
      border-bottom: 1px solid var(--surface-2);
      transition: background .15s;
    }
    #assetTable tbody tr:last-child td { border-bottom: none; }

    /* DataTables overrides */
    .dataTables_wrapper { padding: 18px 22px; }
    #assetTable_wrapper .dataTables_filter label,
    #assetTable_wrapper .dataTables_length label {
      font-size: 13px; color: var(--ink-2); font-weight: 500;
    }
    #assetTable_wrapper .dataTables_filter input {
      border: 1.5px solid var(--border) !important;
      border-radius: var(--radius) !important;
      padding: 8px 16px !important; font-size: 13px !important;
      font-family: var(--font) !important; color: var(--ink) !important;
      background: var(--white) !important; outline: none !important;
      transition: border-color .2s, box-shadow .2s !important;
      min-width: 240px;
    }
    #assetTable_wrapper .dataTables_filter input:focus {
      border-color: var(--blue) !important;
      box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important;
    }
    #assetTable_wrapper .dataTables_length select {
      border: 1.5px solid var(--border) !important;
      border-radius: var(--radius) !important;
      padding: 6px 12px !important; font-size: 13px !important;
      font-family: var(--font) !important; color: var(--ink) !important;
      background: var(--white) !important; outline: none !important;
    }
    .dataTables_info, .dataTables_paginate {
      font-size: 12.5px; color: var(--ink-3); margin-top: 14px;
    }
    .dataTables_paginate .paginate_button {
      border-radius: var(--radius-sm) !important;
      padding: 6px 13px !important; margin: 0 2px !important;
      font-size: 12px !important; font-weight: 600 !important;
      font-family: var(--font) !important;
      transition: all .15s !important;
    }
    .dataTables_paginate .paginate_button.current {
      background: var(--emerald) !important;
      border-color: var(--emerald) !important;
      color: var(--white) !important;
    }
    .dataTables_paginate .paginate_button:hover:not(.current) {
      background: var(--surface-2) !important;
      border-color: var(--border) !important;
      color: var(--ink) !important;
    }

    /* ─────────────────────────────────────────────
       BADGES
    ───────────────────────────────────────────── */
    .pmo-badge {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 5px 12px; border-radius: 20px;
      font-size: 11px; font-weight: 700;
      letter-spacing: .02em;
      border: 1px solid transparent;
    }
    .pmo-badge::before {
      content: ''; width: 5px; height: 5px;
      border-radius: 50%; flex-shrink: 0;
    }
    .pmo-badge-blue   { background: var(--blue-bg); color: #1e40af; border-color: #bfdbfe; }
    .pmo-badge-blue::before { background: var(--blue); }
    .pmo-badge-green  { background: var(--green-lt); color: #166534; border-color: var(--green-bd); }
    .pmo-badge-green::before { background: var(--green); }
    .pmo-badge-amber  { background: var(--amber-bg); color: var(--amber-dk); border-color: #fde68a; }
    .pmo-badge-amber::before { background: var(--amber); }
    .pmo-badge-gray   { background: var(--surface); color: var(--ink-2); border-color: var(--border); }
    .pmo-badge-gray::before { background: var(--ink-4); }

    /* ─────────────────────────────────────────────
       ACTION BUTTONS IN TABLE
    ───────────────────────────────────────────── */
    .pmo-action-wrap { display: flex; align-items: center; gap: 6px; }
    .pmo-action-btn {
      display: inline-flex; align-items: center; gap: 5px;
      padding: 6px 13px; border-radius: 20px;
      font-size: 11.5px; font-weight: 600;
      font-family: var(--font);
      border: 1.5px solid transparent; cursor: pointer;
      transition: all .2s; white-space: nowrap; background: var(--white);
      text-decoration: none;
    }
    .pmo-action-btn:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
    .pmo-action-edit  { color: var(--blue); border-color: #93c5fd; background: var(--blue-bg); }
    .pmo-action-edit:hover { background: var(--blue-lt); }
    .pmo-action-print { color: var(--emerald); border-color: #6ee7b7; background: var(--emerald-bg); }
    .pmo-action-print:hover { background: var(--emerald-lt); }
    .pmo-action-del   { color: var(--red); border-color: #fca5a5; background: var(--red-bg); }
    .pmo-action-del:hover { background: var(--red-lt); }
    .pmo-action-delete { color: var(--red); border-color: #fca5a5; background: var(--red-bg); }
    .pmo-action-delete:hover { background: var(--red-lt); }

    /* ─────────────────────────────────────────────
       MONO TEXT
    ───────────────────────────────────────────── */
    .pmo-mono {
      font-family: var(--font-mono);
      font-size: 12px; font-weight: 500;
      color: var(--ink-3); letter-spacing: .03em;
    }

    /* Value styling */
    .col-value { font-weight: 800; color: var(--emerald-dk); font-size: 13px; }

    /* ─────────────────────────────────────────────
       MODAL — SHARED
    ───────────────────────────────────────────── */
    .modal-content {
      border: none !important;
      border-radius: var(--radius-xl) !important;
      box-shadow: var(--shadow-lg) !important;
      font-family: var(--font);
      overflow: hidden;
    }
    .pmo-modal-header {
      background: var(--white);
      border-bottom: 1px solid var(--border);
      padding: 22px 28px;
      display: flex; align-items: center; gap: 14px;
    }
    .pmo-modal-header-icon {
      width: 46px; height: 46px;
      border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; flex-shrink: 0;
    }
    .pmo-modal-header .modal-title {
      font-size: 17px; font-weight: 800; color: var(--ink);
    }
    .pmo-modal-header small {
      font-size: 12.5px; color: var(--ink-3);
      display: block; margin-top: 2px;
    }
    .pmo-modal-header .btn-close {
      margin-left: auto;
      width: 34px; height: 34px;
      border-radius: 50%; border: 1.5px solid var(--border);
      background: var(--white); opacity: 1;
      transition: all .2s;
    }
    .pmo-modal-header .btn-close:hover {
      background: var(--red-lt); border-color: var(--red);
      transform: rotate(90deg);
    }

    .pmo-modal-body {
      background: var(--surface);
      padding: 28px 30px;
      max-height: 75vh;
      overflow-y: auto;
      display: flex; flex-direction: column; gap: 18px;
      position: relative;
    }
    .pmo-modal-body::-webkit-scrollbar { width: 6px; }
    .pmo-modal-body::-webkit-scrollbar-thumb { background: var(--ink-4); border-radius: 3px; }

    .pmo-modal-footer {
      background: var(--white);
      border-top: 1px solid var(--border);
      padding: 16px 28px;
      display: flex; align-items: center;
      justify-content: flex-end; gap: 10px;
    }

    /* ─────────────────────────────────────────────
       SECTION BLOCKS INSIDE MODALS
    ───────────────────────────────────────────── */
    .pmo-section {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      border-left: 4px solid var(--blue);
      box-shadow: var(--shadow-xs);
      transition: box-shadow .2s;
    }
    .pmo-section:hover { box-shadow: var(--shadow-sm); }
    .pmo-section-head {
      background: linear-gradient(180deg, var(--surface) 0%, var(--surface-2) 100%);
      border-bottom: 1px solid var(--border);
      padding: 12px 18px;
      font-size: 11.5px; font-weight: 800;
      color: var(--blue);
      text-transform: uppercase;
      letter-spacing: .06em;
      display: flex; align-items: center; gap: 7px;
    }
    .pmo-section-body {
      padding: 18px 20px;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
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

    /* ─────────────────────────────────────────────
       FORM FIELDS
    ───────────────────────────────────────────── */
    .pmo-field { display: flex; flex-direction: column; gap: 5px; }
    .pmo-field.span2 { grid-column: span 2; }
    .pmo-field.span3 { grid-column: span 3; }

    .pmo-label {
      font-size: 11.5px; font-weight: 700;
      color: var(--ink-3);
      text-transform: uppercase;
      letter-spacing: .05em;
    }
    .pmo-label .req { color: var(--red); font-weight: 800; }

    .pmo-input,
    .pmo-select,
    .pmo-textarea {
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      background: var(--surface);
      padding: 9px 14px;
      font-size: 13px; font-family: var(--font);
      color: var(--ink); width: 100%; outline: none;
      transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .pmo-input:focus,
    .pmo-select:focus,
    .pmo-textarea:focus {
      border-color: var(--blue);
      background: var(--white);
      box-shadow: 0 0 0 3px rgba(37,99,235,.1);
    }
    .pmo-input::placeholder,
    .pmo-textarea::placeholder { color: var(--ink-4); }
    .pmo-textarea { resize: vertical; min-height: 72px; }

    .pmo-input-prefix { display: flex; }
    .pmo-prefix-text {
      padding: 9px 13px;
      border: 1.5px solid var(--border); border-right: none;
      border-radius: var(--radius-sm) 0 0 var(--radius-sm);
      background: linear-gradient(180deg, var(--surface-2), #e8ecf1);
      font-size: 13px; font-weight: 700;
      color: var(--ink-3); white-space: nowrap;
    }
    .pmo-input-prefix .pmo-input { border-radius: 0 var(--radius-sm) var(--radius-sm) 0; }

    /* Identity strip inside edit modal */
    .pmo-identity-strip {
      background: linear-gradient(135deg, var(--blue-bg) 0%, var(--blue-lt) 100%);
      border: 1.5px solid #bfdbfe;
      border-radius: var(--radius);
      padding: 14px 18px;
      display: flex; align-items: center;
      gap: 10px; flex-wrap: wrap;
    }
    .pmo-chip {
      background: var(--white);
      border: 1px solid #bfdbfe;
      border-radius: var(--radius-sm);
      padding: 5px 14px;
      font-size: 12px; color: #1e40af; font-weight: 600;
      box-shadow: var(--shadow-xs);
    }
    .pmo-chip span { color: #93c5fd; margin-right: 4px; font-weight: 500; }

    /* Classification pill */
    .pmo-class-pill {
      display: inline-flex; align-items: center; gap: 8px;
      background: linear-gradient(135deg, var(--blue-bg) 0%, var(--blue-lt) 100%);
      border: 1.5px solid #bfdbfe; border-radius: var(--radius);
      padding: 9px 16px; font-size: 13px; font-weight: 700;
      color: #1e40af;
    }

    /* Loading overlay */
    .pmo-loading-overlay {
      position: absolute; inset: 0;
      background: rgba(248,250,252,.92);
      backdrop-filter: blur(4px);
      z-index: 10;
      display: flex; justify-content: center; align-items: center;
      border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    }

    /* Detail cards in confirm modal */
    .pmo-detail-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      border-left: 3px solid var(--emerald);
      padding: 12px 15px;
      box-shadow: var(--shadow-xs);
      transition: transform .15s;
    }
    .pmo-detail-card:hover { transform: translateX(3px); }
    .pmo-detail-card small {
      font-size: 10.5px; font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .06em; color: var(--ink-4);
    }
    .pmo-detail-card p {
      margin: 3px 0 0; font-weight: 700;
      font-size: 13.5px; color: var(--ink);
    }

    /* ─────────────────────────────────────────────
       SUCCESS / ERROR MODALS
    ───────────────────────────────────────────── */
    .pmo-result-icon {
      width: 72px; height: 72px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 30px; margin: 0 auto 14px;
    }
    .pmo-result-icon.success { background: var(--emerald-lt); color: var(--emerald); }
    .pmo-result-icon.error   { background: var(--red-lt); color: var(--red); }

    /* ─────────────────────────────────────────────
       TOAST NOTIFICATION
    ───────────────────────────────────────────── */
    .pmo-toast {
      position: fixed; top: 24px; right: 24px; z-index: 9999;
      background: var(--white); border: 1px solid var(--border);
      border-radius: var(--radius-lg); box-shadow: var(--shadow-lg);
      padding: 18px 22px; display: flex; align-items: center; gap: 14px;
      min-width: 320px; max-width: 400px;
      animation: toastIn .4s cubic-bezier(.34,1.56,.64,1);
      overflow: hidden; pointer-events: auto;
    }
    .pmo-toast::after {
      content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
      background: var(--emerald);
      animation: toastProgress 3.5s linear forwards;
    }
    @keyframes toastProgress { from { width: 100%; } to { width: 0%; } }
    @keyframes toastIn { from { opacity: 0; transform: translateX(40px) scale(.95); } to { opacity: 1; transform: none; } }
    .pmo-toast.hiding { animation: toastOut .3s ease forwards; }
    @keyframes toastOut { to { opacity: 0; transform: translateX(40px) scale(.95); } }
    .toast-icon {
      width: 42px; height: 42px; border-radius: var(--radius);
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .toast-title { font-weight: 800; font-size: 14px; color: var(--ink); }
    .toast-sub { font-size: 12px; color: var(--ink-3); margin-top: 2px; }

    /* ─────────────────────────────────────────────
       SCROLL-TO-TOP
    ───────────────────────────────────────────── */
    .scroll-top-btn {
      position: fixed; bottom: 28px; right: 28px; z-index: 999;
      width: 46px; height: 46px; border-radius: 50%;
      background: linear-gradient(135deg, var(--emerald), #047857);
      color: var(--white); border: none;
      box-shadow: 0 4px 16px rgba(5,150,105,.3);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; cursor: pointer;
      opacity: 0; transform: translateY(10px);
      transition: opacity .3s, transform .3s, background .2s;
      pointer-events: none;
    }
    .scroll-top-btn.visible { opacity: 1; transform: none; pointer-events: auto; }
    .scroll-top-btn:hover { background: linear-gradient(135deg, #047857, var(--emerald-dk)); transform: translateY(-2px); }

    /* Empty state */
    .pmo-empty-state {
      text-align: center; padding: 56px 24px; color: var(--ink-4);
    }
    .pmo-empty-state i { font-size: 48px; margin-bottom: 14px; display: block; color: var(--ink-4); opacity: .5; }
    .pmo-empty-state p { font-size: 15px; font-weight: 500; margin: 0; }

    /* Vehicle section in edit modal */
    .pmo-section.vehicle { border-left-color: var(--red); }
    .pmo-section.vehicle .pmo-section-head {
      background: linear-gradient(180deg, var(--red-bg) 0%, var(--red-lt) 100%);
      border-bottom-color: #fecaca; color: var(--red-dk);
    }

    /* ─────────────────────────────────────────────
       RESPONSIVE
    ───────────────────────────────────────────── */
    @media (max-width: 992px) {
      .pmo-page-header { padding: 28px 20px 48px; }
      .pmo-stats-row { padding: 0 16px; grid-template-columns: repeat(2, 1fr); }
      .pmo-toolbar, .pmo-table-card { margin-left: 16px; margin-right: 16px; }
    }
    @media (max-width: 768px) {
      .pmo-stats-row { grid-template-columns: 1fr 1fr; }
      .pmo-section-body.cols-3 { grid-template-columns: 1fr 1fr; }
      .pmo-page-header h1 { font-size: 1.5rem; }
      .header-btns { width: 100%; }
    }
    @media (max-width: 576px) {
      .pmo-page-header { padding: 24px 16px 44px; }
      .pmo-page-header h1 { font-size: 1.3rem; }
      .pmo-stats-row { grid-template-columns: 1fr; gap: 10px; padding: 0 10px; }
      .pmo-toolbar { margin: 0 10px 12px; padding: 12px 14px; flex-direction: column; align-items: flex-start; }
      .pmo-table-card { margin: 0 10px 30px; border-radius: var(--radius-lg); }
      .pmo-section-body { grid-template-columns: 1fr; }
      .pmo-section-body .pmo-field.span2,
      .pmo-section-body .pmo-field.span3 { grid-column: span 1; }
      .pmo-search-input { width: 100%; }
    }

    /* ─────────────────────────────────────────────
       CUSTOM SCROLLBAR
    ───────────────────────────────────────────── */
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: var(--surface); }
    ::-webkit-scrollbar-thumb { background: var(--ink-4); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--ink-3); }
  </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
  <div class="content-wrapper" style="padding: 0;">

    <!-- ── Hero Page Header ── -->
    <div class="pmo-page-header anim-in">
      <div>
        <div class="page-eyebrow"><i class="fas fa-map-marked-alt me-1"></i> Land Asset Registry</div>
        <h1>Land <span>Assets</span></h1>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:10px;position:relative;">
        <div class="pmo-breadcrumb">
          <a href="index.php"><i class="fas fa-home"></i> Home</a>
          <i class="fas fa-chevron-right"></i>
          <span style="color:rgba(255,255,255,.9);font-weight:600;">Land Assets</span>
        </div>
        <div class="header-btns">
          <button type="button" class="pmo-btn" id="openAddModalBtn" style="background:var(--white);color:var(--emerald);border-color:var(--white);font-weight:700;">
            <i class="fas fa-plus-circle"></i> Add Land Asset
          </button>
        </div>
      </div>
    </div>

    <!-- ── Stats ── -->
    <div class="pmo-stats-row">
      <div class="pmo-stat anim-in anim-d1">
        <div class="pmo-stat-icon blue"><i class="fas fa-map"></i></div>
        <div>
          <div class="pmo-stat-value" id="statTotal">—</div>
          <div class="pmo-stat-label">Total Records</div>
        </div>
      </div>
      <div class="pmo-stat anim-in anim-d2">
        <div class="pmo-stat-icon green"><i class="fas fa-peso-sign"></i></div>
        <div>
          <div class="pmo-stat-value" id="statValue" style="font-size:1.25rem;">—</div>
          <div class="pmo-stat-label">Total Value</div>
        </div>
      </div>
      <div class="pmo-stat anim-in anim-d3">
        <div class="pmo-stat-icon amber"><i class="fas fa-file-circle-check"></i></div>
        <div>
          <div class="pmo-stat-value" id="statActive">—</div>
          <div class="pmo-stat-label">Active Records</div>
        </div>
      </div>
      <div class="pmo-stat anim-in anim-d4">
        <div class="pmo-stat-icon violet"><i class="fas fa-layer-group"></i></div>
        <div>
          <div class="pmo-stat-value">Land</div>
          <div class="pmo-stat-label">Asset Category</div>
        </div>
      </div>
    </div>

    <!-- ── Toolbar ── -->
    <div class="pmo-toolbar anim-in anim-d4">
      <div class="pmo-toolbar-left">
        <div class="pmo-search-wrap">
          <i class="fas fa-search"></i>
          <input type="text" class="pmo-search-input" id="tableSearch" placeholder="Search land assets…">
        </div>
        <select class="pmo-filter-select" id="groupFilter">
          <option value="">All groups</option>
          <?php
          include("db_conn.php");
          $res = mysqli_query($conn, "SELECT DISTINCT land_name FROM land_asset_location WHERE land_id != 1 ORDER BY land_name");
          while ($r = mysqli_fetch_assoc($res)) {
            echo "<option value='" . htmlspecialchars($r['land_name']) . "'>" . htmlspecialchars($r['land_name']) . "</option>";
          }
          mysqli_close($conn);
          ?>
        </select>
      </div>
      <div class="pmo-toolbar-right">
        <button class="pmo-btn pmo-btn-sm"><i class="fas fa-download"></i> Export</button>
        <button class="pmo-btn pmo-btn-sm"><i class="fas fa-print"></i> Print All</button>
      </div>
    </div>

    <!-- ── Table ── -->
    <div class="pmo-table-card anim-in anim-d5">
      <div class="card-header-bar">
        <h6><i class="fas fa-table"></i> Asset Records</h6>
        <span id="recordCount" style="font-size:12px;color:var(--ink-4);font-weight:600;"></span>
      </div>
      <div class="table-responsive">
        <table id="assetTable" class="table" style="width:100%;">
          <thead>
            <tr>
              <th>Edit</th>
              <th>Asset Number</th>
              <th>Classification</th>
              <th>Accountable Office</th>
              <th>Article</th>
              <th>Group</th>
              <th>Description</th>
              <th>Acquisition Date</th>
              <th>Unit Value</th>
              <th style="text-align:center;">Bal. Card</th>
              <th style="text-align:center;">On Hand</th>
              <th>Remarks</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
<?php
include("db_conn.php");
$sql = "SELECT a.*, c.category_name, al.location_name, lal.land_id, lal.land_name
        FROM asset a
        INNER JOIN category c ON a.category_id = c.category_id
        LEFT JOIN asset_location al ON a.a_location_id = al.a_location_id
        LEFT JOIN land_asset_location lal ON a.land_id = lal.land_id
        WHERE a.category_id = 18 AND a.sc_value = 1";
$result = mysqli_query($conn, $sql);
if (!$result) die("Error fetching data: " . mysqli_error($conn));

$totalValue = 0;
$totalCount = 0;
$activeCount = 0;

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    if (in_array($row['sp_type'], ['SPLV', 'SPHV'])) continue;
    $totalCount++;
    $totalValue += floatval(str_replace(',', '', $row['unit_value']));
    if (!empty($row['remarks']) && $row['remarks'] !== '—') $activeCount++;

    $desc = "";
    $desc .= !empty($row['brand'])                ? "<strong>Brand:</strong> "            . htmlspecialchars($row['brand'])                . "<br>" : "";
    $desc .= !empty($row['serial_no'])            ? "<strong>Serial No:</strong> "        . htmlspecialchars($row['serial_no'])            . "<br>" : "";
    $desc .= !empty($row['specific_description']) ? "<strong>Land Description:</strong> " . htmlspecialchars($row['specific_description']) . "<br>" : "";
    $desc .= !empty($row['model'])                ? "<strong>Model:</strong> "            . htmlspecialchars($row['model'])               . "<br>" : "";
    $desc .= !empty($row['chassis_no'])           ? "<strong>Chassis:</strong> "          . htmlspecialchars($row['chassis_no'])          . "<br>" : "";
    $desc .= !empty($row['engine_no'])            ? "<strong>Engine:</strong> "           . htmlspecialchars($row['engine_no'])           . "<br>" : "";
    $desc .= !empty($row['plate_no'])             ? "<strong>Plate:</strong> "            . htmlspecialchars($row['plate_no'])            . "<br>" : "";
    $desc .= !empty($row['color'])                ? "<strong>Color:</strong> "            . htmlspecialchars($row['color'])               . "<br>" : "";
    $desc .= !empty($row['year_model'])           ? "<strong>Year:</strong> "             . htmlspecialchars($row['year_model'])          . "<br>" : "";

    echo "<tr>";
    echo "<td>
            <button class='pmo-action-btn pmo-action-edit editBtn'
              data-id='"           . $row['asset_id']                                     . "'
              data-asset-no='"     . htmlspecialchars($row['asset_no'])                   . "'
              data-property-no='"  . htmlspecialchars($row['property_no'])                . "'
              data-product='"      . htmlspecialchars($row['product_name'])               . "'
              data-category='"     . htmlspecialchars($row['category_name'])              . "'
              data-office='"       . htmlspecialchars($row['whereabouts'])                . "'
              data-brand='"        . htmlspecialchars($row['brand'] ?? '')                . "'
              data-serial='"       . htmlspecialchars($row['serial_no'] ?? '')            . "'
              data-value='"        . htmlspecialchars($row['unit_value'])                 . "'
              data-land='"         . htmlspecialchars($row['land_name'] ?? '')            . "'
              data-description='"  . htmlspecialchars($row['specific_description'] ?? '') . "'>
              <i class='fas fa-edit'></i> Edit
            </button>
          </td>";
    echo "<td><span class='pmo-mono'>" . htmlspecialchars($row['asset_no']) . "</span></td>";
    echo "<td><span class='pmo-badge pmo-badge-blue'>" . htmlspecialchars($row['category_name']) . "</span></td>";
    echo "<td style='font-size:12.5px;color:var(--ink-2);max-width:160px;white-space:normal;'>" . htmlspecialchars($row['whereabouts']) . "</td>";
    echo "<td style='font-weight:600;color:var(--ink);'>" . htmlspecialchars($row['product_name']) . "</td>";
    echo "<td><span class='pmo-badge pmo-badge-green'>" . htmlspecialchars($row['land_name'] ?? '—') . "</span></td>";
    echo "<td style='font-size:12px;color:var(--ink-3);max-width:200px;white-space:normal;line-height:1.65;'>" . (!empty($desc) ? $desc : "—") . "</td>";
    echo "<td style='font-size:12.5px;color:var(--ink-3);white-space:nowrap;'>" . htmlspecialchars($row['product_received']) . "</td>";
    echo "<td><span class='col-value'>₱" . number_format(floatval(str_replace(',','',$row['unit_value'])),2) . "</span></td>";
    echo "<td style='text-align:center;'><span style='background:var(--surface-2);border-radius:var(--radius-sm);padding:4px 12px;font-weight:700;font-size:12.5px;display:inline-block;min-width:38px;'>" . htmlspecialchars($row['balance_card']) . "</span></td>";
    echo "<td style='text-align:center;'><span style='background:var(--surface-2);border-radius:var(--radius-sm);padding:4px 12px;font-weight:700;font-size:12.5px;display:inline-block;min-width:38px;'>" . htmlspecialchars($row['onhand_percount']) . "</span></td>";
    echo "<td style='font-size:12px;color:var(--ink-3);'>" . htmlspecialchars($row['remarks']) . "</td>";
    echo "<td>
            <div class='pmo-action-wrap'>
              <button class='pmo-action-btn pmo-action-print print-btn'
                data-asset-id='"     . $row['asset_id']                                          . "'
                data-product-name='" . htmlspecialchars($row['product_name'], ENT_QUOTES)        . "'
                data-description='"  . htmlspecialchars($row['specific_description'], ENT_QUOTES) . "'
                data-property-no='"  . htmlspecialchars($row['property_no'], ENT_QUOTES)          . "'>
                <i class='fas fa-print'></i> Print
              </button>
              <button class='pmo-action-btn pmo-action-delete deleteBtn'
                data-asset-id='" . $row['asset_id'] . "'
                data-product-name='" . htmlspecialchars($row['product_name'], ENT_QUOTES) . "'>
                <i class='fas fa-trash-alt'></i> Delete
              </button>
            </div>
          </td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='13'><div class='pmo-empty-state'><i class='fas fa-map'></i><p>No land asset records found</p></div></td></tr>";
}

// Inject stats via JS
echo "<script>
  document.getElementById('statTotal').textContent  = '$totalCount';
  document.getElementById('statValue').textContent  = '₱" . number_format($totalValue/1000000, 1) . "M';
  document.getElementById('statActive').textContent = '$activeCount';
  document.getElementById('recordCount').textContent = '$totalCount records';
</script>";

mysqli_close($conn);
?>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- /.content-wrapper -->


  <!-- ════════════════════════════════
       ADD ASSET MODAL
  ════════════════════════════════ -->
  <div class="modal fade" id="addAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="pmo-modal-header modal-header">
          <div class="pmo-modal-header-icon" style="background:var(--emerald-lt);border:1.5px solid var(--green-bd);color:var(--emerald);"><i class="fas fa-plus-circle"></i></div>
          <div>
            <div class="modal-title">Add Land Asset Record</div>
            <small>Fill in the details to register a new land asset</small>
          </div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
        </div>

        <div class="pmo-modal-body modal-body">
          <form id="addAssetForm" method="post" action="superadmin_add_land_asset_process.php">
            <input type="hidden" name="a_location_id" value="40">

            <!-- Section 1: Classification -->
            <div class="pmo-section">
              <div class="pmo-section-head"><i class="fas fa-tags"></i> Asset Group &amp; Classification</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Group Classification <span class="req">*</span></label>
                  <select class="pmo-select" name="land_id" required>
                    <?php
                    include("db_conn.php");
                    $res = mysqli_query($conn, "SELECT land_id, land_name FROM land_asset_location WHERE land_id != 1");
                    while ($r = mysqli_fetch_assoc($res)) {
                      $sel = ($r['land_id'] == 24) ? "selected" : "";
                      echo "<option value='" . htmlspecialchars($r['land_id']) . "' $sel>" . htmlspecialchars($r['land_name']) . "</option>";
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
                    echo "<div class='pmo-class-pill'><i class='fas fa-layer-group'></i> " . htmlspecialchars($r['category_name']) . "</div>";
                    echo "<input type='hidden' name='category_id' value='" . htmlspecialchars($r['category_id']) . "'>";
                  }
                  mysqli_close($conn);
                  ?>
                </div>
              </div>
            </div>

            <!-- Section 2: Land Data -->
            <div class="pmo-section land">
              <div class="pmo-section-head"><i class="fas fa-map-marked-alt"></i> Land Asset Data</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Property Name / Article <span class="req">*</span></label>
                  <input type="text" class="pmo-input" name="product_name" placeholder="Property / Article Name" required>
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Asset Number</label>
                  <input type="text" class="pmo-input" name="asset_no" placeholder="e.g. LA-2024-001">
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Description</label>
                  <textarea class="pmo-textarea" name="specific_description" placeholder="Specific land / property description…"></textarea>
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
            <div class="pmo-section">
              <div class="pmo-section-head"><i class="fas fa-list-alt"></i> Additional Asset Details</div>
              <div class="pmo-section-body cols-3">
                <div class="pmo-field">
                  <label class="pmo-label">Property Number</label>
                  <input type="text" class="pmo-input" name="property_no" placeholder="Lot / Batch No.">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Unit of Measurement</label>
                  <input type="text" class="pmo-input" name="unit_of_measurement" placeholder="set / unit">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Condition</label>
                  <input type="text" class="pmo-input" name="product_condition" placeholder="Good / Fair / Damaged">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Balance Card (Qty)</label>
                  <input type="number" class="pmo-input" name="balance_card" placeholder="0">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Quantity On Hand</label>
                  <input type="number" class="pmo-input" name="onhand_percount" placeholder="0">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Shortage / Overage (Qty)</label>
                  <input type="number" class="pmo-input" name="so_quantity" placeholder="0">
                </div>
                <div class="pmo-field span3">
                  <label class="pmo-label">Shortage / Overage (Value)</label>
                  <div class="pmo-input-prefix">
                    <span class="pmo-prefix-text">₱</span>
                    <input type="text" class="pmo-input currency-input" name="so_value" placeholder="0.00">
                  </div>
                </div>
              </div>
            </div>

            <!-- Section 4: Accountability -->
            <div class="pmo-section acct">
              <div class="pmo-section-head"><i class="fas fa-user-shield"></i> Accountability Information</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Accountable Officer</label>
                  <input type="text" class="pmo-input" name="accountable_officer" id="add_accountable_officer" placeholder="Full name">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Assigned End User</label>
                  <input type="text" class="pmo-input" name="enduser" id="add_enduser" placeholder="Full name">
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Accountable Office</label>
                  <textarea class="pmo-textarea" name="whereabouts" placeholder="Office / department / location…"></textarea>
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Remarks</label>
                  <textarea class="pmo-textarea" name="remarks" placeholder="Any additional notes…"></textarea>
                </div>
              </div>
            </div>

          </form>
        </div>

        <div class="pmo-modal-footer modal-footer">
          <button type="button" class="pmo-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
          <button type="button" class="pmo-btn pmo-btn-primary" id="submitAddBtn"><i class="fas fa-plus-circle"></i> Add Asset</button>
        </div>

      </div>
    </div>
  </div>


  <!-- ════════════════════════════════
       CONFIRM EDIT MODAL (Step 1)
  ════════════════════════════════ -->
  <div class="modal fade" id="confirmEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">

        <div class="pmo-modal-header modal-header">
          <div class="pmo-modal-header-icon" style="background:var(--amber-lt);border:1.5px solid #fde68a;color:var(--amber);"><i class="fas fa-info-circle"></i></div>
          <div>
            <div class="modal-title">Asset Summary</div>
            <small>Review details before editing</small>
          </div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body" style="background:var(--surface);padding:24px;">
          <div class="row g-2" id="confirmEditDetails"></div>
          <div style="background:var(--blue-bg);border:1.5px solid #bfdbfe;border-radius:var(--radius);padding:14px 18px;font-size:13px;color:#1e40af;font-weight:500;display:flex;align-items:center;gap:10px;margin-top:16px;">
            <i class="fas fa-pencil-alt"></i>
            Review the asset details above. Click <strong style="margin:0 3px;">Open Edit Form</strong> to make changes.
          </div>
        </div>

        <div class="pmo-modal-footer modal-footer">
          <button type="button" class="pmo-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
          <button type="button" class="pmo-btn pmo-btn-blue" id="openFullEditBtn"><i class="fas fa-edit"></i> Open Edit Form</button>
        </div>

      </div>
    </div>
  </div>


  <!-- ════════════════════════════════
       FULL EDIT MODAL (Step 2)
  ════════════════════════════════ -->
  <div class="modal fade" id="editAssetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">

        <div class="pmo-modal-header modal-header">
          <div class="pmo-modal-header-icon" style="background:var(--green-lt);border:1.5px solid var(--green-bd);color:var(--green);"><i class="fas fa-edit"></i></div>
          <div>
            <div class="modal-title">Edit Land Asset Record</div>
            <small>Update the fields below and save</small>
          </div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
        </div>

        <div class="pmo-modal-body modal-body" id="editModalBody" style="position:relative;">

          <!-- Loading -->
          <div id="editLoadingOverlay" class="pmo-loading-overlay">
            <div class="text-center">
              <div class="spinner-border" style="width:3rem;height:3rem;color:var(--emerald);" role="status"></div>
              <p class="mt-3" style="font-size:13px;color:var(--ink-3);font-weight:600;">Loading asset data…</p>
            </div>
          </div>

          <form id="editAssetForm" method="post" action="superadmin_edit_asset_process_land.php">
            <input type="hidden" name="asset_id" id="edit_asset_id">

            <!-- Identity strip -->
            <div class="pmo-identity-strip">
              <i class="fas fa-map-marked-alt" style="color:var(--blue);font-size:18px;"></i>
              <div class="pmo-chip"><span>Asset No:</span><span id="strip_asset_no">—</span></div>
              <div class="pmo-chip"><span>Property No:</span><span id="strip_property_no">—</span></div>
              <div class="pmo-chip"><span>Article:</span><span id="strip_product_name">—</span></div>
            </div>

            <!-- Section 1: Classification -->
            <div class="pmo-section">
              <div class="pmo-section-head"><i class="fas fa-tags"></i> Asset Group &amp; Classification</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Classification of Asset</label>
                  <select class="pmo-select" name="category_id" id="edit_category_id">
                    <?php
                    include("db_conn.php");
                    $res = mysqli_query($conn, "SELECT category_id, category_name FROM category");
                    while ($r = mysqli_fetch_assoc($res)) {
                      echo "<option value='" . htmlspecialchars($r['category_id']) . "'>" . htmlspecialchars($r['category_name']) . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                  </select>
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Group Classification</label>
                  <select class="pmo-select" name="land_id" id="edit_land_id">
                    <?php
                    include("db_conn.php");
                    $res = mysqli_query($conn, "SELECT land_id, land_name FROM land_asset_location WHERE land_id != 1");
                    while ($r = mysqli_fetch_assoc($res)) {
                      echo "<option value='" . htmlspecialchars($r['land_id']) . "'>" . htmlspecialchars($r['land_name']) . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                  </select>
                </div>
              </div>
            </div>

            <!-- Section 2: Land Data -->
            <div class="pmo-section land">
              <div class="pmo-section-head"><i class="fas fa-map-marked-alt"></i> Land Asset Data</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Property Name / Article <span class="req">*</span></label>
                  <input type="text" class="pmo-input" name="product_name" id="edit_product_name" required>
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Asset Number</label>
                  <input type="text" class="pmo-input" name="asset_no" id="edit_asset_no">
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Description</label>
                  <textarea class="pmo-textarea" name="specific_description" id="edit_specific_description"></textarea>
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Acquisition Date</label>
                  <input type="text" class="pmo-input" name="product_received" id="edit_product_received" placeholder="YYYY-MM-DD">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Unit Value</label>
                  <div class="pmo-input-prefix">
                    <span class="pmo-prefix-text">₱</span>
                    <input type="text" class="pmo-input currency-input" name="unit_value" id="edit_unit_value" placeholder="0.00">
                  </div>
                </div>
              </div>
            </div>

            <!-- Section 3: Vehicle Details (hidden unless MOTOR VEHICLES) -->
            <div class="pmo-section vehicle" id="edit_vehicle_section" style="display:none;">
              <div class="pmo-section-head">
                <i class="fas fa-car"></i> Additional Vehicle Details
              </div>
              <div class="pmo-section-body cols-3">
                <div class="pmo-field">
                  <label class="pmo-label">Vehicle Group</label>
                  <select class="pmo-select" name="a_location_id" id="edit_a_location_id">
                    <?php
                    include("db_conn.php");
                    $res = mysqli_query($conn, "SELECT a_location_id, location_name FROM asset_location");
                    while ($r = mysqli_fetch_assoc($res)) {
                      echo "<option value='" . htmlspecialchars($r['a_location_id']) . "'>" . htmlspecialchars($r['location_name']) . "</option>";
                    }
                    mysqli_close($conn);
                    ?>
                  </select>
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Brand</label>
                  <input type="text" class="pmo-input" name="brand" id="edit_brand">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Model</label>
                  <input type="text" class="pmo-input" name="model" id="edit_model">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Serial Number</label>
                  <input type="text" class="pmo-input" name="serial_no" id="edit_serial_no">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Conduction Sticker</label>
                  <input type="text" class="pmo-input" name="conduction_sticker" id="edit_conduction_sticker">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Plate Number</label>
                  <input type="text" class="pmo-input" name="plate_no" id="edit_plate_no">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Chassis Number</label>
                  <input type="text" class="pmo-input" name="chassis_no" id="edit_chassis_no">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Engine Number</label>
                  <input type="text" class="pmo-input" name="engine_no" id="edit_engine_no">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Year Model</label>
                  <input type="text" class="pmo-input" name="year_model" id="edit_year_model">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Color</label>
                  <input type="text" class="pmo-input" name="color" id="edit_color">
                </div>
              </div>
            </div>

            <!-- Section 4: Additional Details -->
            <div class="pmo-section">
              <div class="pmo-section-head"><i class="fas fa-list-alt"></i> Additional Asset Details</div>
              <div class="pmo-section-body cols-3">
                <div class="pmo-field">
                  <label class="pmo-label">Property Number</label>
                  <input type="text" class="pmo-input" name="property_no" id="edit_property_no">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Unit of Measurement</label>
                  <input type="text" class="pmo-input" name="unit_of_measurement" id="edit_unit_of_measurement">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Condition</label>
                  <input type="text" class="pmo-input" name="product_condition" id="edit_product_condition">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Balance Card (Qty)</label>
                  <input type="number" class="pmo-input" name="balance_card" id="edit_balance_card">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Quantity On Hand</label>
                  <input type="number" class="pmo-input" name="onhand_percount" id="edit_onhand_percount">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Shortage / Overage (Qty)</label>
                  <input type="number" class="pmo-input" name="so_quantity" id="edit_so_quantity">
                </div>
                <div class="pmo-field span3">
                  <label class="pmo-label">Shortage / Overage (Value)</label>
                  <div class="pmo-input-prefix">
                    <span class="pmo-prefix-text">₱</span>
                    <input type="text" class="pmo-input currency-input" name="so_value" id="edit_so_value" placeholder="0.00">
                  </div>
                </div>
              </div>
            </div>

            <!-- Section 5: Accountability -->
            <div class="pmo-section acct">
              <div class="pmo-section-head"><i class="fas fa-user-shield"></i> Accountability Information</div>
              <div class="pmo-section-body">
                <div class="pmo-field">
                  <label class="pmo-label">Accountable Officer</label>
                  <input type="text" class="pmo-input" name="accountable_officer" id="edit_accountable_officer">
                </div>
                <div class="pmo-field">
                  <label class="pmo-label">Assigned End User</label>
                  <input type="text" class="pmo-input" name="enduser" id="edit_enduser">
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Accountable Office</label>
                  <input type="text" class="pmo-input" name="whereabouts" id="edit_whereabouts">
                </div>
                <div class="pmo-field span2">
                  <label class="pmo-label">Remarks</label>
                  <textarea class="pmo-textarea" name="remarks" id="edit_remarks"></textarea>
                </div>
              </div>
            </div>

          </form>
        </div>

        <div class="pmo-modal-footer modal-footer">
          <button type="button" class="pmo-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
          <button type="button" class="pmo-btn pmo-btn-primary" id="submitEditBtn"><i class="fas fa-save"></i> Update Asset</button>
        </div>

      </div>
    </div>
  </div>


  <!-- Success Modal -->
  <div id="addSuccessModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content" style="text-align:center;">
        <div class="modal-body" style="padding:36px 28px;">
          <div class="pmo-result-icon success"><i class="fas fa-check"></i></div>
          <p style="font-weight:800;font-size:16px;margin:0;color:var(--ink);">Success!</p>
          <p style="font-size:13px;color:var(--ink-3);margin-top:6px;">Asset added / updated successfully.</p>
          <button class="pmo-btn pmo-btn-primary mt-3" data-bs-dismiss="modal" style="width:100%;justify-content:center;">Done</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Error Modal -->
  <div id="errorModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content" style="text-align:center;">
        <div class="modal-body" style="padding:36px 28px;">
          <div class="pmo-result-icon error"><i class="fas fa-times"></i></div>
          <p style="font-weight:800;font-size:16px;margin:0;color:var(--ink);">Duplicate Entry</p>
          <p style="font-size:13px;color:var(--ink-3);margin-top:6px;">Property / Asset Number already exists.</p>
          <button class="pmo-btn" style="border-color:var(--red);color:var(--red);width:100%;justify-content:center;" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

</div><!-- /.wrapper -->

<!-- ════════════════════════════════
     DELETE CONFIRMATION MODAL
════════════════════════════════ -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="pmo-modal-header modal-header">
        <div class="pmo-modal-header-icon" style="background:var(--red-bg);border-color:var(--red-lt);color:var(--red);"><i class="fas fa-trash-alt"></i></div>
        <div>
          <div class="modal-title">Delete Asset</div>
          <small>This action cannot be undone</small>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="text-align:center;padding:28px 30px;">
        <p style="font-size:14px;color:var(--ink-2);margin:0;">Are you sure you want to delete this asset?</p>
      </div>
      <div class="pmo-modal-footer modal-footer" style="justify-content:center;">
        <button type="button" class="pmo-btn" data-bs-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
        <a href="#" id="deleteConfirmBtn" class="pmo-btn" style="background:var(--red);color:var(--white);text-decoration:none;border-color:var(--red);"><i class="fas fa-trash-alt"></i> Delete</a>
      </div>
    </div>
  </div>
</div>

<!-- ══ DELETE TOAST ══ -->
<?php if (isset($_GET['deleted']) && $_GET['deleted'] === 'true'): ?>
<div class="pmo-toast" id="deleteToast">
  <div class="toast-icon" style="background:var(--emerald-lt);color:var(--emerald);"><i class="fas fa-check-circle"></i></div>
  <div>
    <div style="font-size:14px;font-weight:700;color:var(--ink);">Deleted!</div>
    <div style="font-size:12px;color:var(--ink-3);">Asset record has been removed.</div>
  </div>
</div>
<?php endif; ?>

<!-- Scroll to top -->
<button class="scroll-top-btn" id="scrollTopBtn" title="Back to top"><i class="fas fa-arrow-up"></i></button>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="asset/js/adminlte.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="asset/tables/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {

  // DataTable
  $('#assetTable').DataTable({
    responsive: true,
    pageLength: 25,
    order: [[1, 'asc']],
    language: {
      search: '<i class="fas fa-search" style="color:var(--ink-4);margin-right:6px;"></i>',
      searchPlaceholder: 'Search records…',
      lengthMenu: 'Show _MENU_ records',
      info: 'Showing _START_ &ndash; _END_ of _TOTAL_ assets'
    },
    drawCallback: function() {
      $('#assetTable tbody tr').each(function(i) {
        $(this).css({ opacity: 0, transform: 'translateY(8px)' });
        var row = $(this);
        setTimeout(function() {
          row.css({
            transition: 'opacity .25s ease, transform .25s ease',
            opacity: 1, transform: 'none'
          });
        }, i * 15);
      });
    }
  });

  // Scroll to top
  var $scrollBtn = $('#scrollTopBtn');
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 300) $scrollBtn.addClass('visible');
    else $scrollBtn.removeClass('visible');
  });
  $scrollBtn.on('click', function () { $('html, body').animate({ scrollTop: 0 }, 400); });

  // Custom search binding
  $('#tableSearch').on('keyup', function () {
    $('#assetTable').DataTable().search($(this).val()).draw();
  });

  // Group filter
  $('#groupFilter').on('change', function () {
    var val = $(this).val();
    $('#assetTable').DataTable().column(5).search(val).draw();
  });

  // URL param modals
  var params = new URLSearchParams(window.location.search);
  if (params.get('success')) new bootstrap.Modal(document.getElementById('addSuccessModal')).show();
  if (params.get('error'))   new bootstrap.Modal(document.getElementById('errorModal')).show();

  // ── Add Modal ──────────────────────────────────────
  $('#openAddModalBtn').on('click', function () {
    document.getElementById('addAssetForm').reset();
    new bootstrap.Modal(document.getElementById('addAssetModal')).show();
  });

  $('#submitAddBtn').on('click', function () { $('#addAssetForm').submit(); });

  $(document).on('input', '#add_accountable_officer', function () {
    $('#add_enduser').val($(this).val());
  });

  // ── Confirm Edit (Step 1) ──────────────────────────
  var activeAssetId = null;

  $(document).on('click', '.editBtn', function () {
    var btn = $(this);
    activeAssetId = btn.data('id');

    var fields = [
      ['Asset Number',      btn.data('asset-no')    || '—'],
      ['Property Number',   btn.data('property-no') || '—'],
      ['Product / Article', btn.data('product')     || '—'],
      ['Classification',    btn.data('category')    || '—'],
      ['Office',            btn.data('office')      || '—'],
      ['Land Group',        btn.data('land')        || '—'],
      ['Brand',             btn.data('brand')       || '—'],
      ['Serial No',         btn.data('serial')      || '—'],
      ['Unit Value',        btn.data('value') ? '₱' + btn.data('value') : '—'],
      ['Description',       btn.data('description') || '—']
    ];

    var html = '';
    fields.forEach(function (f) {
      html += '<div class="col-md-4 col-6 mb-2">'
            +   '<div class="pmo-detail-card">'
            +     '<small>' + f[0] + '</small>'
            +     '<p>' + f[1] + '</p>'
            +   '</div>'
            + '</div>';
    });

    $('#confirmEditDetails').html(html);
    new bootstrap.Modal(document.getElementById('confirmEditModal')).show();
  });

  // ── Full Edit (Step 2) ─────────────────────────────
  $('#openFullEditBtn').on('click', function () {
    bootstrap.Modal.getInstance(document.getElementById('confirmEditModal')).hide();

    document.getElementById('confirmEditModal').addEventListener('hidden.bs.modal', function handler() {
      this.removeEventListener('hidden.bs.modal', handler);
      $('#editLoadingOverlay').show();
      new bootstrap.Modal(document.getElementById('editAssetModal')).show();

      $.getJSON('get_asset_ajax.php', { asset_id: activeAssetId }, function (d) {
        if (!d || d.error) {
          $('#editLoadingOverlay').hide();
          alert('Failed to load asset data.');
          return;
        }

        $('#strip_asset_no').text(d.asset_no    || '—');
        $('#strip_property_no').text(d.property_no || '—');
        $('#strip_product_name').text(d.product_name || '—');

        $('#edit_asset_id').val(d.asset_id);
        $('#edit_category_id').val(d.category_id);
        $('#edit_land_id').val(d.land_id);
        $('#edit_a_location_id').val(d.a_location_id);
        $('#edit_product_name').val(d.product_name);
        $('#edit_asset_no').val(d.asset_no);
        $('#edit_specific_description').val(d.specific_description);
        $('#edit_product_received').val(d.product_received !== '0000-00-00' ? d.product_received : '');
        $('#edit_unit_value').val(d.unit_value);
        $('#edit_brand').val(d.brand);
        $('#edit_model').val(d.model);
        $('#edit_serial_no').val(d.serial_no);
        $('#edit_conduction_sticker').val(d.conduction_sticker);
        $('#edit_plate_no').val(d.plate_no);
        $('#edit_chassis_no').val(d.chassis_no);
        $('#edit_engine_no').val(d.engine_no);
        $('#edit_year_model').val(d.year_model);
        $('#edit_color').val(d.color);
        $('#edit_property_no').val(d.property_no);
        $('#edit_unit_of_measurement').val(d.unit_of_measurement);
        $('#edit_balance_card').val(d.balance_card);
        $('#edit_onhand_percount').val(d.onhand_percount);
        $('#edit_so_quantity').val(d.so_quantity);
        $('#edit_so_value').val(d.so_value);
        $('#edit_product_condition').val(d.product_condition);
        $('#edit_accountable_officer').val(d.accountable_officer);
        $('#edit_enduser').val(d.enduser);
        $('#edit_whereabouts').val(d.whereabouts);
        $('#edit_remarks').val(d.remarks);

        toggleEditVehicle();
        $('#editLoadingOverlay').hide();
      });
    });
  });

  function toggleEditVehicle() {
    var name = $('#edit_category_id option:selected').text();
    $('#edit_vehicle_section').toggle(name === 'MOTOR VEHICLES');
  }
  $(document).on('change', '#edit_category_id', toggleEditVehicle);

  $(document).on('input', '#edit_accountable_officer', function () {
    $('#edit_enduser').val($(this).val());
  });

  $('#submitEditBtn').on('click', function () { $('#editAssetForm').submit(); });

  // ── Delete modal handler ────────────────────────────────────
  $(document).on('click', '.deleteBtn', function() {
    var assetId = $(this).data('asset-id');
    var name = $(this).data('product-name') || 'this asset';
    $('#deleteModal .modal-body p').text('Are you sure you want to delete "' + name + '"?');
    $('#deleteConfirmBtn').attr('href', window.location.pathname + '?asset_id=' + assetId);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
  });

  // ── Toast auto-dismiss ──────────────────────────────────────
  var $toast = $('#deleteToast');
  if ($toast.length) {
    setTimeout(function() {
      $toast.addClass('hiding');
      setTimeout(function() { $toast.remove(); }, 320);
    }, 3500);
  }

  // Currency formatter
  $(document).on('input', '.currency-input', function () {
    var v     = $(this).val().replace(/[^\d.]/g, '');
    var parts = v.split('.');
    parts[0]  = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    if (parts[1] !== undefined) parts[1] = parts[1].slice(0, 2);
    $(this).val(parts.join('.'));
  });

});
</script>
</body>
</html>
