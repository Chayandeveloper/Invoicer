@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('businesses.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Add Business</h1>
                <p class="text-sm text-gray-500 font-medium">Create a new business identity</p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('businesses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Business
                            Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('name') border-red-500 @enderror"
                            placeholder="Enter business name">
                        @error('name')
                            <p class="text-[10px] text-red-500 mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Email
                                Address</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('email') border-red-500 @enderror"
                                placeholder="contact@business.com">
                            @error('email')
                                <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone
                                Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('phone') border-red-500 @enderror"
                                placeholder="+1 (555) 000-0000">
                            @error('phone')
                                <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Website
                            URL</label>
                        <input type="text" name="website" value="{{ old('website') }}"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('website') border-red-500 @enderror"
                            placeholder="https://www.business.com">
                        @error('website')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Physical
                            Address</label>
                        <textarea name="address" rows="3"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('address') border-red-500 @enderror"
                            placeholder="Enter full business address">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Bank
                            Details</label>
                        <textarea name="bank_details" rows="3"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4 @error('bank_details') border-red-500 @enderror"
                            placeholder="Acc Name, Acc Number, IFSC/BIC, Branch...">{{ old('bank_details') }}</textarea>
                        @error('bank_details')
                            <p class="text-[10px] text-red-500 mt-1 font-bold italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Business
                            Logo (Optional)</label>

                        <div id="logo-preview-container" class="hidden mb-4 flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100 w-fit">
                            <img id="logo-preview" src="#" class="h-16 w-16 object-contain rounded-lg border bg-white p-1">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Logo Preview</p>
                                <p class="text-[10px] text-gray-500 font-medium">This will be used on your invoices</p>
                            </div>
                        </div>

                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-100 border-dashed rounded-2xl bg-gray-50/50 @error('logo') border-red-500 @enderror">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl mb-4"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo-upload"
                                        class="relative cursor-pointer bg-white rounded-md font-bold text-primary hover:text-primary-dark focus-within:outline-none px-2">
                                        <span>Upload a logo</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewLogo(this)">
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
                        const container = document.getElementById('logo-preview-container');
                        const preview = document.getElementById('logo-preview');
                        
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            
                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                container.classList.remove('hidden');
                            }
                            
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                <div class="flex justify-center sm:justify-end gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('businesses.index') }}"
                        class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-xs">Cancel</a>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Save Business
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection