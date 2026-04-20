@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('clients.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div class="flex-grow">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Edit Client</h1>
                <p class="text-sm text-gray-500 font-medium">Update register details for this customer</p>
            </div>
            <button type="button" id="unlock-btn" onclick="unlockForm()"
                class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 text-amber-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-amber-100 transition border border-amber-100 shadow-sm shadow-amber-500/10">
                <i class="fas fa-unlock"></i>
                <span>Edit Details</span>
            </button>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form id="edit-client-form" action="{{ route('clients.update', $client->id) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client
                            Name</label>
                        <input type="text" name="name" value="{{ old('name', $client->name) }}" required disabled
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('name') border-red-500 @enderror disabled:opacity-60 disabled:cursor-not-allowed"
                            placeholder="Enter client or company name">
                        @error('name')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Office
                            Address</label>
                        <textarea name="address" rows="3" disabled
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('address') border-red-500 @enderror disabled:opacity-60 disabled:cursor-not-allowed"
                            placeholder="Enter physical or billing address">{{ old('address', $client->address) }}</textarea>
                        @error('address')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Email
                                Address</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}" disabled
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('email') border-red-500 @enderror disabled:opacity-60 disabled:cursor-not-allowed"
                                placeholder="client@email.com">
                            @error('email')
                                <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}" disabled
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('phone') border-red-500 @enderror disabled:opacity-60 disabled:cursor-not-allowed"
                                placeholder="+1 (555) 000-0000">
                            @error('phone')
                                <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client
                            Logo (Optional)</label>

                        @if($client->logo)
                            <div id="current-logo-display" class="mb-4 flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($client->logo) }}"
                                    class="h-16 w-16 object-contain rounded-lg border bg-white p-1" alt="Current Logo">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Current Branding
                                    </p>
                                    <p class="text-[10px] text-gray-500 font-medium">Will be replaced if a new one is uploaded
                                    </p>
                                </div>
                            </div>
                        @endif

                        <div id="new-logo-preview-container" class="hidden mb-4 flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                            <img id="new-logo-preview" src="#" class="h-16 w-16 object-contain rounded-lg border bg-white p-1">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest text-primary">New Logo Preview</p>
                                <p class="text-[10px] text-gray-500 font-medium">This will replace your current logo</p>
                            </div>
                        </div>

                        <div id="logo-upload-zone"
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-100 border-dashed rounded-2xl bg-gray-50/50 opacity-40 grayscale pointer-events-none transition-all">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl mb-4"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo-upload"
                                        class="relative cursor-pointer bg-white rounded-md font-bold text-primary hover:text-primary-dark focus-within:outline-none px-2">
                                        <span>Upload new logo</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewLogo(this)" disabled>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">PNG, JPG up to 2MB
                                </p>
                            </div>
                        </div>
                        @error('logo')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <script>
                    function previewLogo(input) {
                        const container = document.getElementById('new-logo-preview-container');
                        const preview = document.getElementById('new-logo-preview');
                        const currentLogo = document.getElementById('current-logo-display');
                        
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                container.classList.remove('hidden');
                                if (currentLogo) currentLogo.style.opacity = '0.5';
                            }
                            
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                <div id="footer-actions" class="hidden flex justify-center sm:justify-end gap-4 pt-8 border-t border-gray-100 animate-slide-in">
                    <button type="button" onclick="window.location.reload()"
                        class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-sm">Cancel</button>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function unlockForm() {
            const form = document.getElementById('edit-client-form');
            const inputs = form.querySelectorAll('input, textarea');
            const uploadZone = document.getElementById('logo-upload-zone');
            const unlockBtn = document.getElementById('unlock-btn');
            const footerActions = document.getElementById('footer-actions');

            inputs.forEach(field => {
                field.disabled = false;
                field.removeAttribute('disabled');
            });

            if (uploadZone) uploadZone.classList.remove('opacity-40', 'grayscale', 'pointer-events-none');
            if (unlockBtn) unlockBtn.classList.add('hidden');
            if (footerActions) footerActions.classList.remove('hidden');
        }
    </script>
@endsection