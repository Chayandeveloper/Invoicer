@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto py-12">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">Create New Client</h1>
            <p class="text-sm text-gray-500 font-medium mt-2">Add a new business partner or lead to your system</p>
        </div>

        @if ($errors->any())
            <div class="mb-8 bg-rose-50 border border-rose-100 p-6 rounded-[2rem] animate-slide-in">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-rose-500 text-white p-1.5 rounded-lg text-xs"><i class="fas fa-exclamation-triangle"></i></div>
                    <p class="text-rose-800 font-black uppercase tracking-widest text-[10px]">Please correct the following errors:</p>
                </div>
                <ul class="list-disc list-inside text-rose-600 text-xs font-bold space-y-1 ml-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-2xl shadow-gray-200/50 rounded-[2.5rem] border border-gray-100 overflow-hidden">
            <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Identity Section -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Company Identity</label>
                            <input type="text" name="name" value="{{ old('name') }}" required 
                                   placeholder="Full Company Name"
                                   class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Industry</label>
                                <input type="text" name="industry" value="{{ old('industry') }}" placeholder="e.g. Tech"
                                       class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Logo Design</label>
                            <div class="flex items-center gap-6">
                                <div id="logo-preview-container" class="h-20 w-20 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200 flex items-center justify-center overflow-hidden shrink-0">
                                    <img id="logo-preview" src="#" alt="Preview" class="hidden h-full w-full object-contain">
                                    <i id="logo-placeholder" class="fas fa-image text-gray-200 text-2xl"></i>
                                </div>
                                <div class="flex-grow">
                                    <input type="file" name="logo" id="logo-input" onchange="previewLogo(this)" class="hidden">
                                    <label for="logo-input" class="inline-block bg-white border-2 border-gray-100 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-500 hover:border-primary hover:text-primary transition-all cursor-pointer">
                                        Choose File
                                    </label>
                                    <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-wider">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Financial Section -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Primary Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" placeholder="billing@client.com"
                                       class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Phone Number</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 00000 00000"
                                       class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">GST/Tax Number</label>
                            <input type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="Optional"
                                   class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div class="mt-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Business Address</label>
                        <textarea name="address" rows="3" placeholder="Full registered address..."
                                  class="w-full bg-gray-50 border-gray-100 border-2 rounded-2xl px-5 py-4 font-bold text-gray-900 focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all outline-none">{{ old('address') }}</textarea>
                    </div>

                    <div class="pt-6 flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="flex-grow bg-primary text-white py-5 rounded-2xl font-black uppercase tracking-[0.2em] text-xs hover:bg-primary-dark transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-3">
                            <i class="fas fa-check-circle text-lg"></i>
                            Save Client Profile
                        </button>
                        <a href="{{ route('clients.index') }}" class="sm:w-48 bg-gray-50 text-gray-400 py-5 rounded-2xl font-black uppercase tracking-[0.2em] text-xs hover:bg-gray-100 hover:text-gray-600 transition-all flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').src = e.target.result;
                    document.getElementById('logo-preview').classList.remove('hidden');
                    document.getElementById('logo-placeholder').classList.add('hidden');
                    document.getElementById('logo-preview-container').classList.remove('border-dashed');
                    document.getElementById('logo-preview-container').classList.add('border-primary/20');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection