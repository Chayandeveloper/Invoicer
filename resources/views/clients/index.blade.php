@extends('layout')

@section('content')
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Clients</h1>
            <p class="text-sm text-gray-500 font-medium">Manage your database of customers and partners</p>
        </div>
        <a href="{{ route('clients.create') }}"
            class="w-full sm:w-auto bg-primary text-white px-6 py-3 rounded-xl font-bold hover:bg-primary-dark transition shadow-lg shadow-primary/20 flex items-center justify-center gap-2 text-sm">
            <i class="fas fa-plus"></i> Add Client
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto scrolling-touch">
            <table class="w-full text-left text-sm text-gray-600 min-w-[600px]">
                <thead
                    class="bg-gray-50 text-gray-900 font-black border-b border-gray-100 uppercase tracking-widest text-[10px]">
                    <tr>
                        <th class="px-6 py-5">Client Name</th>
                        <th class="px-6 py-5">Email Address</th>
                        <th class="px-6 py-5">Phone Number</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-black text-gray-900 tracking-tight italic">{{ $client->name }}</td>
                            <td class="px-6 py-4 font-medium text-gray-600">{{ $client->email }}</td>
                            <td class="px-6 py-4 font-bold text-gray-400 text-xs">{{ $client->phone }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="p-2.5 text-gray-400 hover:text-amber-600 transition bg-gray-50 rounded-xl"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                        <i class="fas fa-users text-3xl text-gray-200"></i>
                                    </div>
                                    <p class="font-black text-gray-500 uppercase tracking-widest text-xs">No clients found</p>
                                    <p class="text-[10px] mt-2 font-medium">Add a client to quickly invoice them.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection