<x-filament-panels::page.simple>
    {{ $this->content }}

    <div style="margin-top: 1.5rem;">
        {{-- Divider --}}
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
            <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
            <span style="font-size: 0.75rem; font-weight: 500; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.05em;">atau</span>
            <div style="flex: 1; height: 1px; background: #e2e8f0;"></div>
        </div>

        {{-- Google SSO Button --}}
        <a href="{{ route('auth.google') }}"
           style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.7rem 1.25rem; border-radius: 0.625rem; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; font-size: 0.875rem; font-weight: 600; text-decoration: none; transition: all 0.2s ease; cursor: pointer; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05);"
           onmouseover="this.style.background='#f8fafc'; this.style.borderColor='#cbd5e1'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.1)';"
           onmouseout="this.style.background='#ffffff'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 1px 2px 0 rgba(0,0,0,0.05)';">
            {{-- Google Icon SVG --}}
            <svg width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Masuk dengan Google
        </a>
    </div>
</x-filament-panels::page.simple>
