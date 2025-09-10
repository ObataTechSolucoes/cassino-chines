<style>
  /*
   * Admin Panel Design System (baseline)
   * - Non-invasive: relies on container classes used by Filament (fi-*)
   * - Focus: spacing, contrast, surfaces, rounded corners, subtle shadows
   */

  :root {
    --adm-bg: #0b1220;           /* page background */
    --adm-surface: #0f162a;      /* sidebar / header surface */
    --adm-card: #121a31;         /* cards and widgets */
    --adm-border: #1f2a44;       /* subtle borders */
    --adm-text: #e5e7eb;         /* primary text */
    --adm-muted: #9ca3af;        /* secondary text */
    --adm-primary: #6366f1;      /* primary accents */
    --adm-accent: #22d3ee;       /* accent (interactive) */
    --adm-radius-lg: 14px;       /* large radius for shells */
    --adm-radius-md: 10px;       /* medium radius for cards */
    --adm-shadow: 0 10px 30px rgba(2, 8, 23, 0.35);
  }

  /* Page background */
  body {
    background-color: var(--adm-bg);
  }

  /* Topbar styling */
  .fi-topbar {
    background: linear-gradient(180deg, rgba(15,22,42,.9), rgba(15,22,42,.85));
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border-bottom: 1px solid var(--adm-border);
  }

  /* Sidebar shell */
  .fi-sidebar {
    background: linear-gradient(180deg, #0f162a 0%, #0b1220 100%);
    border-right: 1px solid var(--adm-border);
  }
  .fi-sidebar .fi-nav > ul > li a,
  .fi-sidebar nav a {
    border-radius: 10px;
  }
  .fi-sidebar nav a:hover {
    background: rgba(99, 102, 241, 0.08);
  }

  /* Main content area */
  .fi-main {
    background: transparent;
  }

  /* Generic cards / panels (Filament uses various wrappers; we keep it defensive) */
  .fi-section, .fi-panel, .fi-widget, .fi-card, .fi-resource-relation-managers, .fi-fo-component, .fi-ta-table-container {
    background: var(--adm-card);
    border: 1px solid var(--adm-border);
    border-radius: var(--adm-radius-md);
    box-shadow: var(--adm-shadow);
  }

  /* Tables */
  .fi-ta-table thead th {
    background: rgba(31, 42, 68, 0.6);
    color: var(--adm-text);
  }
  .fi-ta-table tbody tr:hover td {
    background: rgba(99, 102, 241, 0.05);
  }

  /* Buttons: emphasize primary */
  .fi-btn, .fi-ac-action {
    border-radius: 10px !important;
  }
  .fi-btn-primary, .fi-ac-action[data-color="primary"] {
    background: var(--adm-primary) !important;
    border-color: var(--adm-primary) !important;
  }
  .fi-btn-primary:hover, .fi-ac-action[data-color="primary"]:hover {
    filter: brightness(1.05);
  }

  /* Forms */
  .fi-fo-field-wrp, .fi-fo-component {
    border-radius: 10px;
  }
  .fi-fo-input, .fi-fo-select, .fi-fo-textarea {
    background: #0e1527;
    border-color: var(--adm-border);
    color: var(--adm-text);
  }
  .fi-fo-input:focus, .fi-fo-select:focus, .fi-fo-textarea:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(99,102,241,.25);
    border-color: var(--adm-primary);
  }

  /* Stats Overview widget (improves icon contrast) */
  .filament-stats-overview-widget-card .filament-stats-overview-widget-card-description-icon {
    color: white !important;
    text-shadow: 0 1px 2px rgba(0,0,0,.55);
  }
  .filament-stats-overview-widget-card .filament-stats-overview-widget-card-description-icon svg {
    color: inherit !important;
    filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.5));
  }

  /* Vendor: filament-page-with-sidebar tweaks (topbar as segmented control) */
  .filament-page-with-sidebar-topbar nav {
    border-radius: 12px;
    background: var(--adm-card) !important;
    border: 1px solid var(--adm-border) !important;
    /* hide native scrollbar for a cleaner look */
    -ms-overflow-style: none; /* IE/Edge */
    scrollbar-width: none;    /* Firefox */
  }
  .filament-page-with-sidebar-topbar nav::-webkit-scrollbar { display: none; }
  .filament-page-with-sidebar-topbar nav ul li a,
  .filament-page-with-sidebar-topbar .fi-topbar-item {
    border-radius: 10px !important;
  }
  .filament-page-with-sidebar-topbar nav a:hover,
  .filament-page-with-sidebar-topbar .fi-topbar-item:hover {
    background: rgba(99, 102, 241, 0.08) !important;
  }
</style>
