<x-filament-panels::page.simple>
    <style>
        /* =======================================================================
           SILAP-CAT ULTRA MODERN LOGIN EXPERIENCE (EXECUTIVE AUTH SUITE)
           Engineered with high-end glassmorphism, ambient lighting, and sharp bevels
           ======================================================================= */

        /* 1. Full-Screen Page Wrapper with Ambient Lighting */
        html.dark .fi-simple-layout,
        .fi-simple-layout {
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

        html:not(.dark) .fi-simple-layout {
            background-color: #f8fafc !important;
            background-image: 
                radial-gradient(circle at 50% 25%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                linear-gradient(rgba(0, 0, 0, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 0, 0, 0.03) 1px, transparent 1px) !important;
        }

        /* 2. Center Container */
        .fi-simple-main-ctn {
            width: 100% !important;
            display: flex !important;
            justify-content: center !important;
        }

        /* 3. The Elevated Auth Card */
        html.dark .fi-simple-main,
        .fi-simple-main {
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

        html:not(.dark) .fi-simple-main {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 
                0 20px 40px -15px rgba(15, 23, 42, 0.1),
                0 0 25px rgba(79, 70, 229, 0.05) !important;
        }

        /* 4. Card Header & Typography */
        .fi-simple-header {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            margin-bottom: 1.5rem !important;
            gap: 0.5rem !important;
        }

        .fi-simple-header .fi-logo {
            margin-bottom: 0.25rem !important;
        }

        .fi-simple-header-heading {
            font-size: 1.5rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.03em !important;
            margin: 0 !important;
            line-height: 1.25 !important;
        }

        html.dark .fi-simple-header-heading {
            color: #ffffff !important;
        }

        html:not(.dark) .fi-simple-header-heading {
            color: #0f172a !important;
        }

        .fi-simple-header-subheading {
            font-size: 0.8125rem !important;
            margin: 0 !important;
            line-height: 1.5 !important;
            font-weight: 500 !important;
        }

        html.dark .fi-simple-header-subheading {
            color: #94a3b8 !important;
        }

        html:not(.dark) .fi-simple-header-subheading {
            color: #64748b !important;
        }

        /* 5. Form Field Spacing & Input Styling */
        .fi-simple-page form {
            display: flex !important;
            flex-direction: column !important;
            gap: 1.15rem !important;
        }

        html.dark .fi-simple-page .fi-input-wrp {
            background-color: rgba(14, 20, 36, 0.85) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 0.75rem !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.25) !important;
            transition: all 0.2s ease !important;
        }

        html.dark .fi-simple-page .fi-input-wrp:hover {
            border-color: rgba(255, 255, 255, 0.22) !important;
        }

        html.dark .fi-simple-page .fi-input-wrp:focus-within {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        }

        html.dark .fi-simple-page input {
            color: #ffffff !important;
            font-size: 0.925rem !important;
        }

        html.dark .fi-simple-page input::placeholder {
            color: #64748b !important;
        }

        html.dark .fi-simple-page .fi-fo-field-wrp-label span {
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            color: #e2e8f0 !important;
        }

        html.dark .fi-simple-page .fi-input-wrp button {
            color: #94a3b8 !important;
        }

        html.dark .fi-simple-page .fi-input-wrp button:hover {
            color: #ffffff !important;
        }

        /* 6. Remember Me Checkbox */
        html.dark .fi-simple-page .fi-checkbox-input {
            background-color: rgba(14, 20, 36, 0.8) !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
            border-radius: 0.375rem !important;
        }

        html.dark .fi-simple-page .fi-checkbox-input:checked {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
        }

        html.dark .fi-simple-page label:has(.fi-checkbox-input) span {
            color: #cbd5e1 !important;
            font-size: 0.8125rem !important;
            font-weight: 600 !important;
        }

        /* 7. Primary Action Button ("Masuk") */
        .fi-simple-page .fi-form-actions button,
        .fi-simple-page button[type="submit"] {
            width: 100% !important;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            color: #ffffff !important;
            font-weight: 800 !important;
            font-size: 0.95rem !important;
            border-radius: 0.75rem !important;
            padding: 0.8rem 1.5rem !important;
            box-shadow: 0 4px 16px rgba(79, 70, 229, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
            border: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer !important;
        }

        .fi-simple-page .fi-form-actions button:hover,
        .fi-simple-page button[type="submit"]:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%) !important;
            box-shadow: 0 6px 22px rgba(79, 70, 229, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
            transform: translateY(-1px);
        }

        .fi-simple-page .fi-form-actions button:active,
        .fi-simple-page button[type="submit"]:active {
            transform: scale(0.98);
        }

        /* 8. Modern Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 1.5rem 0 1.25rem 0;
        }

        .auth-divider-line {
            flex: 1;
            height: 1px;
        }

        html.dark .auth-divider-line {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15));
        }

        html.dark .auth-divider-line:last-child {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.15), transparent);
        }

        html:not(.dark) .auth-divider-line {
            background: linear-gradient(90deg, transparent, #cbd5e1);
        }

        html:not(.dark) .auth-divider-line:last-child {
            background: linear-gradient(90deg, #cbd5e1, transparent);
        }

        .auth-divider-text {
            font-size: 0.6875rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        html.dark .auth-divider-text { color: #94a3b8; }
        html:not(.dark) .auth-divider-text { color: #64748b; }

        /* 9. High-End Google SSO Button */
        .auth-google-btn {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.75rem !important;
            width: 100% !important;
            padding: 0.75rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            cursor: pointer !important;
        }

        html.dark .auth-google-btn {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2) !important;
        }

        html.dark .auth-google-btn:hover {
            background: rgba(255, 255, 255, 0.09) !important;
            border-color: rgba(99, 102, 241, 0.45) !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35) !important;
        }

        html:not(.dark) .auth-google-btn {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #1e293b !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06) !important;
        }

        html:not(.dark) .auth-google-btn:hover {
            background: #f8fafc !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        .auth-google-btn:active {
            transform: scale(0.98) !important;
        }

        /* 10. Security & Footer Note */
        .auth-footer {
            margin-top: 1.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.45rem;
            text-align: center;
        }

        .auth-security-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.6875rem;
            font-weight: 600;
            color: #34d399;
        }

        .auth-copyright {
            font-size: 0.6875rem;
        }

        html.dark .auth-copyright { color: #64748b; }
        html:not(.dark) .auth-copyright { color: #94a3b8; }
    </style>

    {{-- Official Filament Form Content --}}
    {{ $this->content }}

    {{-- Sleek Divider --}}
    <div class="auth-divider">
        <span class="auth-divider-line"></span>
        <span class="auth-divider-text">ATAU</span>
        <span class="auth-divider-line"></span>
    </div>

    {{-- Prominent Google SSO Button --}}
    <a href="{{ route('auth.google') }}" class="auth-google-btn">
        <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        <span>Masuk dengan Google</span>
    </a>

    {{-- Security & Footer Credits --}}
    <div class="auth-footer">
        <div class="auth-security-badge">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0110 0v4"></path>
            </svg>
            <span>Koneksi Terenkripsi 256-bit SSL</span>
        </div>
        <div class="auth-copyright">
            © 2026 BKN Kanreg XIV Manokwari • v2.0
        </div>
    </div>
</x-filament-panels::page.simple>
