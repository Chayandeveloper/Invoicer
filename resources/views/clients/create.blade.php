@extends('layout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('clients.index') }}"
                class="p-2 bg-white rounded-xl border border-gray-100 shadow-sm text-gray-400 hover:text-primary transition">
                <i class="fas fa-chevron-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Add Client</h1>
                <p class="text-sm text-gray-500 font-medium">Register a new customer or partner</p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-10 rounded-2xl border border-gray-100 shadow-sm">
            <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client
                            Name</label>
                        <input type="text" name="name" required
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                            placeholder="Enter full name or company">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Office
                            Address</label>
                        <textarea name="address" rows="3"
                            class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                            placeholder="Enter physical or billing address"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Email
                                Address</label>
                            <input type="email" name="email"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                                placeholder="client@example.com">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Phone
                                Number</label>
                            <input type="text" name="phone"
                                class="w-full border-gray-100 bg-gray-50 rounded-xl text-xs font-bold focus:ring-primary focus:border-primary p-4"
                                placeholder="+1 (000) 000-0000">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase mb-2 ml-1 tracking-widest">Client
                            Logo (Optional)</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-100 border-dashed rounded-2xl bg-gray-50/50">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl mb-4"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="logo-upload"
                                        class="relative cursor-pointer bg-white rounded-md font-bold text-primary hover:text-primary-dark focus-within:outline-none px-2">
                                        <span>Upload a logo</span>
                                        <input id="logo-upload" name="logo" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">PNG, JPG up to 2MB
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center sm:justify-end gap-4 pt-8 border-t border-gray-100">
                    <a href="{{ route('clients.index') }}"
                        class="px-8 py-4 text-gray-400 font-bold hover:text-gray-600 transition text-sm">Cancel</a>
                    <button type="submit"
                        class="bg-primary text-white px-12 py-4 rounded-xl font-black hover:bg-primary-dark transition shadow-xl shadow-primary/20 uppercase tracking-widest text-xs">
                        Save Client
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection