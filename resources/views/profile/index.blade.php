@extends('layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="h-32 bg-gradient-to-r from-primary/80 to-primary"></div>
        <div class="px-8 pb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-end gap-6 -mt-12">
                <div class="w-32 h-32 rounded-3xl bg-white p-2 shadow-xl">
                    <div class="w-full h-full rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-5xl font-black">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                </div>
                <div class="flex-grow text-center sm:text-left mb-2">
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-gray-500 font-medium flex items-center justify-center sm:justify-start gap-2">
                        <i class="fas fa-envelope text-xs text-gray-300"></i>
                        {{ $user->email }}
                    </p>
                </div>
                <div class="mb-2">
                    <span class="px-4 py-2 bg-green-50 text-primary rounded-xl text-xs font-black uppercase tracking-widest border border-primary/10">
                        Pro Account
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Business Stat -->
        <a href="{{ route('businesses.index') }}" class="group bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-primary/10 text-primary rounded-2xl flex items-center justify-center text-2xl transition-transform group-hover:scale-110">
                    <i class="fas fa-building"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-200 group-hover:text-primary transition-colors"></i>
            </div>
            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">My Businesses</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-gray-900 tracking-tighter">{{ $businessCount }}</span>
                <span class="text-sm font-bold text-gray-400">Connected Profiles</span>
            </div>
            <p class="mt-4 text-xs text-gray-400 font-medium leading-relaxed">Click to manage your business identities, addresses, and branding assets.</p>
        </a>

        <!-- Client Stat -->
        <a href="{{ route('clients.index') }}" class="group bg-white p-8 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-6">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl transition-transform group-hover:scale-110">
                    <i class="fas fa-users"></i>
                </div>
                <i class="fas fa-arrow-right text-gray-200 group-hover:text-amber-600 transition-colors"></i>
            </div>
            <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">My Clients</h3>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black text-gray-900 tracking-tighter">{{ $clientCount }}</span>
                <span class="text-sm font-bold text-gray-400">Total Contacts</span>
            </div>
            <p class="mt-4 text-xs text-gray-400 font-medium leading-relaxed">Access your client database to manage billing information and history.</p>
        </a>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-green-50 border border-green-100 rounded-2xl flex items-center gap-3 text-green-700 animate-slide-in">
        <i class="fas fa-check-circle"></i>
        <p class="text-xs font-bold uppercase tracking-widest">{{ session('success') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Personal Information -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-edit text-sm"></i>
                    </div>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Personal Information</h2>
                </div>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Display Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                                class="w-full bg-gray-50 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold focus:ring-primary focus:border-primary transition-all"
                                placeholder="Your full name">
                            @error('name')
                                <p class="mt-2 text-[10px] font-bold text-red-500 uppercase px-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 px-1">Email Address</label>
                            <input type="email" value="{{ $user->email }}" disabled
                                class="w-full bg-gray-50 border-gray-100 rounded-2xl px-5 py-4 text-sm font-bold text-gray-400 cursor-not-allowed">
                            <p class="mt-2 text-[9px] text-gray-400 font-medium px-1">Email is managed via your connected Google/Clerk account.</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-gray-900 text-white px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg shadow-gray-900/10">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Account Details Section -->
        <div class="space-y-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-8 h-8 bg-gray-50 text-gray-400 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <h2 class="text-sm font-black text-gray-900 uppercase tracking-widest">Security</h2>
                </div>

                <div class="space-y-6">
                    <div class="p-6 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Identity Provider</p>
                        <div class="flex items-center gap-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_Reference_Logo.svg" class="w-4 h-4">
                            <p class="text-xs font-bold text-gray-700">Google Workspace</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest px-1">Password Management</p>
                        <button type="button" 
                            onclick="if(window.Clerk) window.Clerk.openUserProfile({ appearance: { elements: { userProfileSecurityPage: 'Security' } } }); else window.location.href='https://accounts.google.com/';"
                            class="w-full flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 hover:border-primary/20 transition-all group">
                            <span class="text-xs font-bold text-gray-700">Change Password</span>
                            <i class="fas fa-external-link-alt text-[10px] text-gray-300 group-hover:text-primary transition-colors"></i>
                        </button>
                    </div>

                    <div class="pt-6">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-50 text-red-500 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
                                Sign Out Safely
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes slide-in {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slide-in 0.3s ease-out forwards;
    }
</style>
@endsection
