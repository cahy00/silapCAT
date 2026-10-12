<style>
    /* =======================================================================
       SILAP-CAT ULTRA MODERN THEME (EXECUTIVE MIDNIGHT SLATE & ELECTRIC INDIGO)
       Engineered to top-tier modern product design standards (Linear / Vercel / Stripe)
       ======================================================================= */

    :root {
        --app-font: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    html {
        font-family: var(--app-font);
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    html.dark {
        --app-canvas: #090d16;
        --app-surface: #0e1424;
        --app-card: #121a2d;
        --app-card-elevated: #162038;
        --app-hover: #1a2744;
        --app-border: rgba(255, 255, 255, 0.08);
        --app-border-hover: rgba(99, 102, 241, 0.35);
        --app-indigo-primary: #6366f1;
        --app-indigo-glow: rgba(99, 102, 241, 0.15);
        --app-text-head: #ffffff;
        --app-text-body: #cbd5e1;
        --app-text-muted: #94a3b8;
    }

    /* 1. Base Canvas & Ambient Depth in Dark Mode */
    html.dark,
    html.dark body,
    html.dark .fi-layout,
    html.dark .fi-main {
        background-color: var(--app-canvas) !important;
        background-image:
            radial-gradient(circle at 14% 0%, rgba(99, 102, 241, 0.08) 0%, transparent 42%),
            radial-gradient(circle at 86% 8%, rgba(14, 165, 233, 0.05) 0%, transparent 38%) !important;
        background-attachment: fixed !important;
        color: var(--app-text-body);
    }

    /* 2. Topbar Header (Frosted Glassmorphism) */
    html.dark .fi-topbar {
        background-color: rgba(14, 20, 36, 0.82) !important;
        border-bottom: 1px solid var(--app-border) !important;
        backdrop-filter: blur(16px) saturate(180%);
        -webkit-backdrop-filter: blur(16px) saturate(180%);
    }

    /* 3. Sidebar Navigation (Executive SaaS Polish) */
    html.dark .fi-sidebar {
        background-color: rgba(14, 20, 36, 0.96) !important;
        border-right: 1px solid var(--app-border) !important;
        backdrop-filter: blur(12px);
    }

    html.dark .fi-sidebar-header {
        border-bottom: 1px solid var(--app-border) !important;
    }

    html.dark .fi-sidebar-group-label {
        color: var(--app-text-muted) !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-size: 0.7rem;
    }

    html.dark .fi-sidebar-item-button {
        color: var(--app-text-body) !important;
        border-radius: 0.65rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    html.dark .fi-sidebar-item-icon {
        color: #818cf8 !important;
        transition: transform 0.2s ease;
    }

    html.dark .fi-sidebar-item-button:hover {
        background-color: var(--app-hover) !important;
        color: #ffffff !important;
        transform: translateX(2px);
    }

    html.dark .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
        transform: scale(1.08);
    }

    html.dark .fi-sidebar-item-active .fi-sidebar-item-button {
        background: linear-gradient(90deg, rgba(99, 102, 241, 0.16) 0%, rgba(99, 102, 241, 0.06) 100%) !important;
        color: #a5b4fc !important;
        font-weight: 700 !important;
        border: 1px solid rgba(99, 102, 241, 0.25) !important;
        border-left: 3px solid var(--app-indigo-primary) !important;
        box-shadow: 0 2px 10px rgba(99, 102, 241, 0.12);
    }

    /* Sidebar Badge Counters */
    html.dark .fi-sidebar-item-badge {
        background-color: rgba(99, 102, 241, 0.15) !important;
        color: #a5b4fc !important;
        border: 1px solid rgba(99, 102, 241, 0.28) !important;
        font-weight: 700;
        border-radius: 9999px;
        padding: 0.15rem 0.55rem;
    }

    /* 4. Headings & Typography */
    html.dark h1,
    html.dark h2,
    html.dark h3,
    html.dark h4,
    html.dark .fi-header-heading {
        color: #ffffff !important;
        font-weight: 800 !important;
        letter-spacing: -0.025em;
    }

    html.dark .fi-breadcrumbs-item-label {
        color: var(--app-text-muted) !important;
    }

    /* 5. Cards, Tables, Modals & Dropdown Panels (Laser-Cut Inner Bevel) */
    html.dark .fi-section,
    html.dark .fi-ta-ctn,
    html.dark .fi-modal-window,
    html.dark .fi-dropdown-panel,
    html.dark .fi-widget > div,
    html.dark .fi-wi-stats-overview-stat {
        background-color: var(--app-card) !important;
        border: 1px solid var(--app-border) !important;
        box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.07), 0 8px 24px -4px rgba(0, 0, 0, 0.45) !important;
        border-radius: 1rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    html.dark .fi-wi-stats-overview-stat:hover {
        border-color: var(--app-border-hover) !important;
        box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.1), 0 12px 28px -4px rgba(0, 0, 0, 0.6), 0 0 16px -2px rgba(99, 102, 241, 0.15) !important;
        transform: translateY(-2px);
    }

    html.dark .fi-wi-stats-overview-stat-value {
        color: #ffffff !important;
        font-weight: 800 !important;
        letter-spacing: -0.03em;
    }

    html.dark .fi-wi-stats-overview-stat-label {
        color: var(--app-text-muted) !important;
        font-weight: 700 !important;
        font-size: 0.72rem !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
    }

    html.dark .fi-wi-stats-overview-stat-description {
        font-weight: 600 !important;
        font-size: 0.75rem !important;
    }

    /* 6. Table Components */
    html.dark .fi-ta-header-cell {
        background-color: var(--app-surface) !important;
        border-bottom: 1px solid var(--app-border) !important;
        color: var(--app-text-muted) !important;
        font-weight: 700 !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
    }

    html.dark .fi-ta-row {
        border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
        transition: background-color 0.15s ease;
    }

    html.dark .fi-ta-row:hover {
        background-color: rgba(26, 39, 68, 0.5) !important;
    }

    html.dark .fi-ta-cell,
    html.dark .fi-ta-text {
        color: var(--app-text-body) !important;
    }

    /* 7. Action Buttons (Micro-Interactions) */
    .fi-btn {
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .fi-btn:active {
        transform: scale(0.97) !important;
    }

    html.dark .fi-btn-color-primary {
        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border-radius: 0.65rem;
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        border: none !important;
    }

    html.dark .fi-btn-color-primary:hover {
        background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
        box-shadow: 0 6px 20px rgba(79, 70, 229, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
        transform: translateY(-1px);
    }

    html.dark .fi-btn-color-gray {
        background-color: var(--app-card-elevated) !important;
        border: 1px solid var(--app-border) !important;
        color: var(--app-text-body) !important;
        border-radius: 0.65rem;
    }

    html.dark .fi-btn-color-gray:hover {
        background-color: var(--app-hover) !important;
        border-color: var(--app-border-hover) !important;
        color: #ffffff !important;
    }

    /* 8. Segmented Tabs Navigation */
    html.dark .fi-tabs {
        background-color: var(--app-surface) !important;
        border: 1px solid var(--app-border) !important;
        border-radius: 9999px !important;
        padding: 4px !important;
        display: inline-flex !important;
        width: auto !important;
    }

    html.dark .fi-tabs-item {
        color: var(--app-text-muted) !important;
        border-radius: 9999px !important;
        padding: 0.35rem 0.95rem !important;
        font-size: 0.8125rem !important;
        font-weight: 600 !important;
        border: none !important;
        transition: all 0.2s ease !important;
    }

    html.dark .fi-tabs-item:hover {
        color: #ffffff !important;
    }

    html.dark .fi-tabs-item-active {
        background-color: #4f46e5 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 8px rgba(79, 70, 229, 0.4) !important;
    }

    /* 9. Form Inputs, Select & Search Bars */
    html.dark .fi-input-wrp {
        background-color: var(--app-surface) !important;
        border: 1px solid var(--app-border) !important;
        color: #ffffff !important;
        border-radius: 0.65rem !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    html.dark input,
    html.dark select,
    html.dark textarea {
        color: #ffffff !important;
    }

    html.dark input::placeholder,
    html.dark textarea::placeholder {
        color: var(--app-text-muted) !important;
    }

    html.dark .fi-input-wrp:focus-within {
        border-color: var(--app-indigo-primary) !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25), inset 0 1px 0 0 rgba(255, 255, 255, 0.05) !important;
    }

    /* 10. Badges & Pills Universal Styling */
    .fi-badge {
        border-radius: 9999px !important;
        font-weight: 700 !important;
        letter-spacing: 0.03em !important;
    }

    /* 11. Modal Dialogs */
    html.dark .fi-modal-window {
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.75), 0 0 24px -4px rgba(99, 102, 241, 0.15) !important;
    }

    /* 12. Custom Luxury Scrollbar */
    html.dark ::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    html.dark ::-webkit-scrollbar-track {
        background: var(--app-canvas);
    }

    html.dark ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.14);
        border-radius: 9999px;
    }

    html.dark ::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.55);
    }

    /* 13. Authentication & Simple Pages (Luxury Auth Suite) */
    html.dark .fi-simple-layout {
        position: relative;
        min-height: 100vh;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        background-color: #080c14 !important;
        background-image: 
            radial-gradient(circle at 50% 25%, rgba(99, 102, 241, 0.2) 0%, rgba(14, 165, 233, 0.08) 35%, transparent 70%),
            linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px) !important;
        background-size: 100% 100%, 32px 32px, 32px 32px !important;
        padding: 2.5rem 1rem !important;
    }

    html.dark .fi-simple-main {
        position: relative;
        z-index: 10;
        width: 100% !important;
        max-width: 450px !important;
        background: rgba(18, 26, 45, 0.88) !important;
        backdrop-filter: blur(24px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 1.5rem !important;
        padding: 2.5rem 2.25rem !important;
        box-shadow: 
            inset 0 1px 0 0 rgba(255, 255, 255, 0.14),
            0 25px 50px -12px rgba(0, 0, 0, 0.75),
            0 0 40px -5px rgba(99, 102, 241, 0.2) !important;
        margin: auto !important;
    }

    html.dark .fi-simple-header-heading {
        color: #ffffff !important;
    }

    html.dark .fi-simple-header-subheading {
        color: #94a3b8 !important;
    }
</style>
