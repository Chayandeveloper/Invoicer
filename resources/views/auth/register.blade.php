<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Invoicer</title>
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
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.1); opacity: 1; }
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #clerk-signup-root {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        /* Customize Clerk Internal styles via CSS variables if possible, 
           otherwise Clerk's appearance prop handles it better. */
    </style>
    <!-- Clerk JS SDK -->
    <script async crossorigin="anonymous" data-clerk-publishable-key="{{ env('CLERK_PUBLISHABLE_KEY') }}" src="{{ env('CLERK_FRONTEND_API') }}/npm/@clerk/clerk-js@latest/dist/clerk.browser.js" type="text/javascript"></script>
</head>

<body class="flex items-center justify-center min-h-screen p-4">
    <div class="mesh-gradient"></div>

    <div class="w-full max-w-lg glass rounded-[2.5rem] overflow-hidden flex flex-col fade-in">
        <!-- Header -->
        <div class="p-8 pb-4 text-center">
            <div class="bg-primary/20 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 relative">
                <div class="absolute inset-0 bg-primary blur-2xl opacity-20"></div>
                <i class="fas fa-user-plus text-primary text-2xl relative z-10"></i>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight mb-2">Create Account</h1>
            <p class="text-white/40 text-xs font-medium">Join Invoicer to elevate your financial management</p>
        </div>

        <!-- Clerk Signup Component -->
        <div class="px-8 pb-8">
            <div id="clerk-signup-root">
                <!-- Clerk will mount here -->
                <div class="flex items-center justify-center py-12">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-white/30 text-[11px] font-black uppercase tracking-widest">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-primary hover:text-primary/80 transition-colors ml-1">Log In</a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-6 bg-white/5 border-t border-white/5 text-center">
            <p class="text-[8px] text-white/20 font-black uppercase tracking-[0.4em]">Design Excellence &middot; Fillosoft</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', async () => {
            try {
                // Wait for Clerk to be ready
                while (!window.Clerk) {
                  await new Promise(resolve => setTimeout(resolve, 100));
                }
                
                await Clerk.load();
                
                Clerk.mountSignUp(document.getElementById('clerk-signup-root'), {
                    appearance: {
                        baseTheme: 'dark',
                        variables: {
                            colorPrimary: '#6932BB',
                            colorBackground: 'transparent',
                            colorText: 'white',
                            colorInputBackground: 'rgba(255, 255, 255, 0.05)',
                            colorInputText: 'white',
                        },
                        elements: {
                            card: {
                                background: 'transparent',
                                border: 'none',
                                boxShadow: 'none',
                            },
                            headerTitle: { display: 'none' },
                            headerSubtitle: { display: 'none' },
                            socialButtonsBlockButton: {
                                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                                border: '1px solid rgba(255, 255, 255, 0.1)',
                                color: 'white',
                            },
                            footer: { display: 'none' }
                        }
                    },
                    afterSignUpUrl: '{{ route('auth.clerk.sync') }}',
                    afterSignInUrl: '{{ route('auth.clerk.sync') }}',
                    signInUrl: '{{ route('login') }}'
                });
            } catch (error) {
                console.error('Clerk signup failed to load:', error);
                document.getElementById('clerk-signup-root').innerHTML = 
                    '<p class="text-red-400 text-center py-8">Failed to load registration form. Please refresh.</p>';
            }
        });
    </script>
</body>

</html>
