@extends('resident.layout')

@section('title', 'My Requests')

@section('content')
<div class="max-w-7xl mx-auto bg-white rounded shadow p-4 sm:p-6 lg:p-8 overflow-x-auto">
    <h1 class="text-2xl font-bold mb-6">My Requests</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-xl font-semibold mb-4">My Blotter Reports</h2>
    @if($blotterRequests->isEmpty())
        <p class="text-gray-600 mb-6">You have not submitted any blotter reports yet.</p>
    @else
        <table class="min-w-full border border-gray-300 table-auto mb-8">
            <thead>
                <tr class="bg-red-600 text-white">
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Recipient</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Type</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Description</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Status</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Submitted On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blotterRequests as $request)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $request->recipient_name }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $request->type }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3">{{ $request->description }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ ucfirst($request->status) }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2 class="text-xl font-semibold mb-4">My Document Requests</h2>
    @if($documentRequests->isEmpty())
        <p class="text-gray-600">You have not submitted any document requests yet.</p>
    @else
        <table class="min-w-full border border-gray-300 table-auto">
            <thead>
                <tr class="bg-blue-600 text-white">
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Document Type</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Description</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Status</th>
                    <th class="border border-gray-300 p-2 sm:p-3 text-center">Submitted On</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documentRequests as $request)
                <tr class="border-t border-gray-300 hover:bg-gray-100">
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $request->document_type }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3">{{ $request->description }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ ucfirst($request->status) }}</td>
                    <td class="border border-gray-300 p-2 sm:p-3 text-center">{{ $request->created_at->format('Y-m-d H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection