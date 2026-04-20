<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Invoicer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            overflow: hidden;
            background: #0f172a;
        }

        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: #0f172a;
            background-image:
                radial-gradient(at 0% 0%, hsla(158, 86%, 30%, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(217, 32%, 17%, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(158, 86%, 30%, 0.15) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(217, 32%, 17%, 0.15) 0px, transparent 50%);
            animation: pulse 10s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.1);
                opacity: 1;
            }
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-glass:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #0C8D5D;
            box-shadow: 0 0 0 4px rgba(12, 141, 93, 0.2);
            outline: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0C8D5D 0%, #09704a 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(12, 141, 93, 0.4);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .btn-login-google {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-login-google:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .otp-char {
            width: 3.5rem;
            height: 4rem;
            font-size: 1.5rem;
            text-align: center;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.2s ease;
        }

        .otp-char:focus {
            border-color: #0C8D5D;
            background: rgba(255, 255, 255, 0.08);
            outline: none;
            box-shadow: 0 0 0 4px rgba(12, 141, 93, 0.2);
        }

        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(12, 141, 93, 0.1);
            border-left-color: #0C8D5D;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <!-- Clerk JS SDK -->
    <script async crossorigin="anonymous" data-clerk-publishable-key="{{ env('CLERK_PUBLISHABLE_KEY') }}" src="https://{{ env('CLERK_FRONTEND_API') }}/npm/@clerk/clerk-js@latest/dist/clerk.browser.js" type="text/javascript"></script>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="mesh-gradient"></div>

    <div id="loading-overlay">
        <div class="text-center">
            <div class="spinner mx-auto mb-4"></div>
            <p class="text-white font-bold tracking-widest text-xs uppercase animate-pulse">Processing...</p>
        </div>
    </div>

    <div class="w-full max-w-md glass rounded-[2.5rem] overflow-hidden flex flex-col fade-in">
        <!-- Header -->
        <div class="p-10 pb-6 text-center">
            <div class="bg-primary/20 w-20 h-20 rounded-3xl flex items-center justify-center mx-auto mb-8 relative">
                <div class="absolute inset-0 bg-primary blur-2xl opacity-20"></div>
                <i class="fas fa-file-invoice-dollar text-primary text-4xl relative z-10"></i>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight mb-3">Invoicer</h1>
            <p class="text-white/40 text-sm font-medium">Elevate your financial management</p>
        </div>

        <!-- content -->
        <div class="px-10 pb-10 space-y-8">
            @if(session('error'))
                <div
                    class="bg-red-500/10 border border-red-500/20 p-4 rounded-2xl text-[11px] text-red-400 font-bold mb-4 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Clerk Login -->
            <a href="{{ route('login.clerk') }}" onclick="showLoader()"
                class="btn-login-google flex items-center justify-center gap-3 w-full p-4 rounded-2xl font-black text-[13px] uppercase tracking-widest transition-all shadow-sm">
                <i class="fas fa-user-shield text-primary text-lg"></i>
                Sign in with Clerk
            </a>

            <div class="flex items-center gap-4 py-2">
                <div class="flex-grow h-px bg-white/5"></div>
                <span class="text-[10px] uppercase font-black tracking-[0.3em] text-white/20">or securely with
                    mobile</span>
                <div class="flex-grow h-px bg-white/5"></div>
            </div>

            <!-- Mobile Section (Step 1) -->
            <div id="otp-phone-section" class="space-y-6">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-white/30 uppercase tracking-[0.2em] ml-1">Mobile
                        Number</label>
                    <div class="relative group">
                        <span
                            class="absolute left-5 top-1/2 -translate-y-1/2 text-white/40 font-black text-sm">+91</span>
                        <input type="tel" id="mobile_number" maxlength="10" placeholder="000 000 0000"
                            class="input-glass w-full pl-16 p-4 rounded-2xl text-sm font-black tracking-widest placeholder:text-white/10">
                    </div>
                </div>
                <button id="send-otp-btn"
                    class="btn-primary w-full text-white p-5 rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-2xl">
                    Get Access Code
                </button>
            </div>

            <!-- OTP Section (Step 2) -->
            <div id="otp-verify-section" class="hidden space-y-8 fade-in">
                <div class="text-center space-y-2">
                    <p class="text-[10px] font-black uppercase text-primary tracking-widest" id="otp-message-text"></p>
                    <p class="text-white/40 text-[11px] font-medium">Enter the 6-digit code we sent</p>
                </div>

                <form action="{{ route('otp.verify') }}" method="POST" class="space-y-8" id="verify-form"
                    onsubmit="showLoader()">
                    @csrf
                    <input type="hidden" name="mobile_number" id="verify_mobile">

                    <div class="flex justify-between gap-2 max-w-[280px] mx-auto">
                        <input type="text" maxlength="6" name="otp" id="otp-input"
                            class="input-glass w-full p-5 rounded-2xl text-center text-3xl font-black tracking-[0.6em] focus:tracking-[0.6em]">
                    </div>

                    @error('otp')
                        <p
                            class="text-red-400 text-[10px] text-center font-bold tracking-wider animate-shake uppercase italic">
                            <i class="fas fa-times-circle mr-1"></i> {{ $message }}
                        </p>
                    @enderror

                    <button type="submit"
                        class="btn-primary w-full text-white p-5 rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-2xl">
                        Verify & Access
                    </button>

                    <button type="button" onclick="resetOtpView()"
                        class="w-full text-[10px] font-black uppercase text-white/20 hover:text-white/40 transition-all tracking-widest mt-2 flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-left"></i> Use different number
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-8 bg-white/5 border-t border-white/5 text-center">
            <p class="text-[9px] text-white/20 font-black uppercase tracking-[0.4em]">Design Excellence &middot;
                Fillosoft</p>
        </div>
    </div>

    <script>
        const sendOtpBtn = document.getElementById('send-otp-btn');
        const phoneSection = document.getElementById('otp-phone-section');
        const verifySection = document.getElementById('otp-verify-section');
        const mobileInput = document.getElementById('mobile_number');
        const verifyMobileInput = document.getElementById('verify_mobile');
        const otpMessageText = document.getElementById('otp-message-text');
        const loader = document.getElementById('loading-overlay');

        function showLoader() {
            loader.style.display = 'flex';
        }

        function hideLoader() {
            loader.style.display = 'none';
        }

        sendOtpBtn.addEventListener('click', async () => {
            const mobile = mobileInput.value;
            if (!mobile || mobile.length < 10) {
                alert('Please enter a valid 10-digit mobile number');
                return;
            }

            sendOtpBtn.disabled = true;
            sendOtpBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Initializing...';
            showLoader();

            try {
                const response = await fetch("{{ route('otp.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ mobile_number: mobile })
                });

                const data = await response.json();
                if (data.success) {
                    phoneSection.classList.add('hidden');
                    verifySection.classList.remove('hidden');
                    verifyMobileInput.value = mobile;
                    otpMessageText.innerText = data.message;

                    // Auto focus OTP input
                    setTimeout(() => document.getElementById('otp-input').focus(), 300);
                } else {
                    alert('Error sending OTP. Please try again.');
                }
            } catch (error) {
                console.error(error);
                alert('Security connection failed. Please check network.');
            } finally {
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerText = 'Get Access Code';
                hideLoader();
            }
        });

        function resetOtpView() {
            phoneSection.classList.remove('hidden');
            verifySection.classList.add('hidden');
        }

        // Handle numeric only input for OTP and Mobile
        [mobileInput, document.getElementById('otp-input')].forEach(el => {
            if (!el) return;
            el.addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>

</html>