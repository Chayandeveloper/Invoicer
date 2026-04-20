<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syncing Session... | Invoicer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; background: #0f172a; display: flex; align-items: center; justify-content: center; min-height: screen; overflow: hidden; }
        .mesh-gradient { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; background-color: #0f172a; background-image: radial-gradient(at 0% 0%, hsla(158, 86%, 30%, 0.15) 0px, transparent 50%), radial-gradient(at 100% 100%, hsla(158, 86%, 30%, 0.15) 0px, transparent 50%); animation: pulse 10s ease-in-out infinite alternate; }
        @keyframes pulse { 0% { opacity: 0.8; } 100% { opacity: 1; } }
        .spinner { width: 50px; height: 50px; border: 4px solid rgba(12, 141, 93, 0.1); border-left-color: #0C8D5D; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
    <!-- Clerk JS SDK -->
    <script async crossorigin="anonymous" data-clerk-publishable-key="{{ env('CLERK_PUBLISHABLE_KEY') }}" src="{{ env('CLERK_FRONTEND_API') }}/npm/@clerk/clerk-js@latest/dist/clerk.browser.js" type="text/javascript"></script>
</head>

<body>
    <div class="mesh-gradient"></div>
    
    <div class="text-center space-y-6">
        <div class="spinner mx-auto"></div>
        <div class="space-y-2">
            <h1 class="text-white font-black text-xl tracking-tight">Syncing your session</h1>
            <p class="text-white/40 text-xs font-semibold uppercase tracking-[0.3em] animate-pulse">Please wait a moment...</p>
        </div>
    </div>

    <script>
        window.addEventListener('load', async () => {
            try {
                // Wait for Clerk
                while (!window.Clerk) {
                    await new Promise(resolve => setTimeout(resolve, 100));
                }
                
                await Clerk.load();
                
                // Wait for session to be fully ready
                if (!Clerk.user) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }

                // Get the user data and handshake with Laravel
                const userData = {
                    id: Clerk.user.id,
                    email: Clerk.user.primaryEmailAddress.emailAddress,
                    name: Clerk.user.fullName,
                    _token: "{{ csrf_token() }}"
                };

                const response = await fetch("{{ route('auth.clerk.handshake') }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(userData)
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    console.error('Handshake failed:', result.message);
                    window.location.href = "{{ route('login') }}?error=sync_failed";
                }
            } catch (error) {
                console.error('Critical sync error:', error);
                window.location.href = "{{ route('login') }}?error=critical_fail";
            }
        });
    </script>
</body>

</html>
