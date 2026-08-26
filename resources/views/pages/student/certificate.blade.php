<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificate - {{ $certificate->course->title }} - LMS Portal</title>
        @vite(['resources/css/app.css'])
        <style>
            @page { size: A4 landscape; margin: 0; }
            body { font-family: 'Instrument Sans', 'Segoe UI', system-ui, sans-serif; }
            .grad-text {
                background: linear-gradient(90deg, #ffc08a 0%, #ff9638 55%, #ff7a11 100%);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }
            .cert-wrap { display: flex; justify-content: center; overflow: hidden; }
            .cert-sheet { width: 1100px; height: 777px; flex-shrink: 0; transform-origin: top center; }
            @media print {
                .no-print { display: none !important; }
                body { background: #fff !important; padding: 0 !important; margin: 0 !important; }
                .cert-wrap { overflow: visible !important; height: auto !important; }
                .cert-sheet { transform: none !important; box-shadow: none !important; margin: 0 !important; width: 297mm !important; height: 210mm !important; border-radius: 0 !important; }
            }
        </style>
    </head>
    <body class="bg-gray-50 min-h-screen">

        {{-- Portal Header (matches verify page) --}}
        <header class="no-print bg-white border-b border-gray-200">
            <div class="max-w-[1140px] mx-auto flex items-center justify-between h-16 px-4 sm:px-6">
                <a href="{{ route('dashboard', ['tab' => 'certificates']) }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary-600 to-primary-800 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-lg font-bold text-primary-900">LMS<span class="text-accent-500">Portal</span></span>
                </a>
                <a href="{{ route('dashboard', ['tab' => 'certificates']) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm text-gray-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                    Back to Dashboard
                </a>
            </div>
        </header>

        {{-- Toolbar --}}
        <div class="no-print max-w-[1140px] mx-auto mt-6 px-4 sm:px-6">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="font-semibold text-gray-900">Course Completion Certificate</h1>
                    <p class="text-sm text-gray-500">{{ $certificate->course->title }} &middot; Issued {{ $certificate->issued_at->format('M d, Y') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="copyVerifyLink()" id="copyBtn" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/></svg>
                        <span id="copyLabel">Copy Verify Link</span>
                    </button>
                    <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z"/></svg>
                        Print
                    </button>
                    <a href="{{ route('certificates.download', $certificate) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg transition shadow-sm text-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        Download PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- Certificate Sheet : A4 Landscape 297mm x 210mm (scaled to fit viewport) --}}
        <main class="max-w-[1140px] mx-auto px-4 sm:px-6 py-6">
            <div class="cert-wrap">
                <div class="cert-sheet relative overflow-hidden rounded-xl shadow-2xl"
                    style="background: linear-gradient(135deg, #060d31 0%, #0d1a5c 45%, #1730b6 100%);">

                    {{-- Grid / circuit texture --}}
                    <div class="absolute inset-0 pointer-events-none" style="opacity: .05; background-image: linear-gradient(#ffc08a 1px, transparent 1px), linear-gradient(90deg, #ffc08a 1px, transparent 1px); background-size: 46px 46px;"></div>

                    {{-- Glow orbs --}}
                    <div class="absolute -top-36 -right-28 w-[460px] h-[460px] rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(255,122,17,.30) 0%, rgba(255,122,17,0) 68%);"></div>
                    <div class="absolute -bottom-44 -left-28 w-[520px] h-[520px] rounded-full pointer-events-none" style="background: radial-gradient(circle, rgba(77,107,255,.32) 0%, rgba(77,107,255,0) 68%);"></div>

                    {{-- Frame + tech corner brackets --}}
                    <div class="absolute inset-4 rounded-2xl border pointer-events-none" style="border-color: rgba(255,150,56,.35);"></div>
                    <div class="absolute top-4 left-4 w-14 h-14 rounded-tl-2xl border-t-2 border-l-2 border-[#ff7a11] pointer-events-none"></div>
                    <div class="absolute top-4 right-4 w-14 h-14 rounded-tr-2xl border-t-2 border-r-2 border-[#ff7a11] pointer-events-none"></div>
                    <div class="absolute bottom-4 left-4 w-14 h-14 rounded-bl-2xl border-b-2 border-l-2 border-[#4d6bff] pointer-events-none"></div>
                    <div class="absolute bottom-4 right-4 w-14 h-14 rounded-br-2xl border-b-2 border-r-2 border-[#4d6bff] pointer-events-none"></div>

                    {{-- Watermark cloud --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.05]">
                        <svg viewBox="0 0 24 24" class="w-[430px] h-[430px]" fill="#ffc08a"><path d="M6.5 20a4.5 4.5 0 01-.42-8.98 6.502 6.502 0 0112.71 1.87A4.002 4.002 0 0118 20H6.5z"/></svg>
                    </div>

                    {{-- Content --}}
                    <div class="relative h-full flex flex-col items-center justify-between text-center px-16 pt-8 pb-6">

                        {{-- Brand --}}
                        <div class="flex flex-col items-center">
                            <div class="flex items-center gap-2.5">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30" style="background: linear-gradient(135deg, #ff9638 0%, #ff7a11 55%, #f05e07 100%);">
                                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 004.5 4.5H18a3.75 3.75 0 001.332-7.257 3 3 0 00-3.758-3.848 5.25 5.25 0 00-10.233 2.33A4.502 4.502 0 002.25 15z"/></svg>
                                </div>
                                <span class="text-xl font-bold tracking-wide text-white">LMS<span class="grad-text">PORTAL</span></span>
                            </div>
                            <p class="mt-3 text-[12px] tracking-[0.5em] uppercase font-semibold grad-text">Certificate of Completion</p>
                        </div>

                        {{-- Recipient --}}
                        <div class="flex flex-col items-center max-w-[900px]">
                            <p class="text-sm italic" style="color: rgba(255,200,150,.65);">This certificate is proudly presented to</p>
                            <h1 class="text-[42px] leading-tight font-extrabold text-white mt-1.5 tracking-tight break-words">{{ $certificate->user->name }}</h1>
                            <div class="flex items-center gap-3 mt-1.5 w-[340px]">
                                <span class="h-px flex-1" style="background: linear-gradient(to right, transparent, #ff7a11);"></span>
                                <svg class="w-4 h-4 text-[#ff9638]" fill="currentColor" viewBox="0 0 20 20"><path d="M13.5 2c-3.038 0-5.5 2.462-5.5 5.5 0 .51.07 1.002.2 1.47L2 15v3h3l1-1v-2h2v-2h2l.53-.53A5.5 5.5 0 1013.5 2zm1.5 5a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                                <span class="h-px flex-1" style="background: linear-gradient(to left, transparent, #4d6bff);"></span>
                            </div>
                            <p class="text-sm mt-2.5" style="color: rgba(255,214,178,.70);">for successfully completing all requirements of the course</p>
                            <h2 class="text-[24px] font-bold text-white mt-1.5 leading-snug break-words">{{ $certificate->course->title }}</h2>
                            <div class="mt-2.5 inline-flex items-center gap-2 px-5 py-1.5 rounded-full border border-[#ff7a11]/40 bg-white/5">
                                <svg class="w-3.5 h-3.5 text-[#ffb37a]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-xs" style="color: rgba(255,224,196,.85);">{{ $certificate->course->duration_hours }} hours of instruction &middot; {{ $certificate->course->category ?? 'General' }} &middot; {{ $certificate->course->level ?? 'All Levels' }}</span>
                            </div>
                        </div>

                        {{-- Signatures row --}}
                        <div class="w-full grid grid-cols-3 items-end gap-8">
                            <div class="flex flex-col items-center">
                                <p class="italic text-lg text-white border-b border-[#ff7a11]/50 pb-1 px-6">{{ $certificate->course->instructor->name ?? 'LMS Portal' }}</p>
                                <p class="text-[10px] uppercase tracking-[0.25em] mt-2" style="color: rgba(255,200,150,.55);">Course Instructor</p>
                            </div>

                            {{-- Notary-style verification seal --}}
                            <div class="flex justify-center self-center" style="transform: rotate(-10deg);">
                                <svg width="142" height="142" viewBox="0 0 170 170">
                                    {{-- Serrated outer edge --}}
                                    <circle cx="85" cy="85" r="80" fill="none" stroke="#ff7a11" stroke-width="7" stroke-dasharray="3.5 4.2"/>
                                    {{-- Main ring --}}
                                    <circle cx="85" cy="85" r="71" fill="rgba(255,122,17,.07)" stroke="#ff7a11" stroke-width="2.5"/>
                                    {{-- Inner ring --}}
                                    <circle cx="85" cy="85" r="49" fill="none" stroke="#ff7a11" stroke-width="1.5"/>

                                    <defs>
                                        <path id="sealArcTop" d="M 85,85 m -60,0 a 60,60 0 1,1 120,0"/>
                                        <path id="sealArcBottom" d="M 85,85 m -60,0 a 60,60 0 1,0 120,0"/>
                                    </defs>
                                    <text fill="#ffb37a" font-size="12" font-weight="bold" letter-spacing="3" font-family="Arial, sans-serif">
                                        <textPath href="#sealArcTop" startOffset="50%" text-anchor="middle">OFFICIALLY VERIFIED</textPath>
                                    </text>
                                    <text fill="#ffb37a" font-size="10" font-weight="bold" letter-spacing="2.5" font-family="Arial, sans-serif">
                                        <textPath href="#sealArcBottom" startOffset="50%" text-anchor="middle">&#9733; LMS PORTAL &#9733;</textPath>
                                    </text>

                                    {{-- Side separators --}}
                                    <circle cx="25" cy="85" r="2.5" fill="#ff9638"/>
                                    <circle cx="145" cy="85" r="2.5" fill="#ff9638"/>

                                    {{-- Center certification --}}
                                    <circle cx="85" cy="73" r="15" fill="#f05e07"/>
                                    <path d="M78 73l4.5 4.5 9-9" stroke="#ffffff" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                    <line x1="66" y1="97" x2="104" y2="97" stroke="#ff7a11" stroke-width="1"/>
                                    <text x="85" y="110" text-anchor="middle" fill="#ffffff" font-size="11" font-weight="bold" letter-spacing="2.5" font-family="Arial, sans-serif">CERTIFIED</text>
                                    <text x="85" y="123" text-anchor="middle" fill="#ffb37a" font-size="8.5" letter-spacing="2" font-family="Arial, sans-serif">{{ $certificate->issued_at->format('Y') }}</text>
                                </svg>
                            </div>

                            <div class="flex flex-col items-center">
                                <p class="italic text-lg text-white border-b border-[#4d6bff]/50 pb-1 px-6">{{ $certificate->issued_at->format('F j, Y') }}</p>
                                <p class="text-[10px] uppercase tracking-[0.25em] mt-2" style="color: rgba(255,200,150,.55);">Date of Issue</p>
                            </div>
                        </div>

                        {{-- Footer verification strip --}}
                        <div class="w-full border-t border-white/10 pt-2.5 flex items-end justify-between gap-6">
                            <p class="text-[11px] text-left select-all flex-1" style="color: rgba(255,214,178,.55);">
                                Certificate ID: <span class="font-mono font-bold text-[#ffb37a] select-all">{{ $certificate->code }}</span>
                                <span class="mx-2 text-white/20">|</span>
                                Verify at: <span class="font-mono text-[#8ebbff] select-all">{{ url('/verify-certificate/' . $certificate->code) }}</span>
                            </p>
                            <div class="flex items-center gap-2.5 shrink-0">
                                <span class="text-[9px] uppercase tracking-[0.2em] text-right leading-tight" style="color: rgba(255,200,150,.55);">Scan to<br>verify</span>
                                <div class="bg-white rounded-md p-1 shadow-lg">
                                    {!! qr_svg($certificate->verificationUrl(), 64) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function fitSheet() {
                const wrap = document.querySelector('.cert-wrap');
                const sheet = document.querySelector('.cert-sheet');
                if (!wrap || !sheet) return;
                const scale = Math.min(1, wrap.clientWidth / 1100);
                sheet.style.transform = 'scale(' + scale + ')';
                wrap.style.height = (777 * scale) + 'px';
            }
            window.addEventListener('resize', fitSheet);
            window.addEventListener('load', fitSheet);
            fitSheet();

            function copyVerifyLink() {
                const url = '{{ url('/verify-certificate/' . $certificate->code) }}';
                navigator.clipboard.writeText(url).then(() => {
                    const label = document.getElementById('copyLabel');
                    label.textContent = 'Copied!';
                    setTimeout(() => label.textContent = 'Copy Verify Link', 2000);
                });
            }
        </script>
    </body>
</html>
