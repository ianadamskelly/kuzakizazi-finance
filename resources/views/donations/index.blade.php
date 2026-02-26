<x-app-layout>
    <div>
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Donations</h1>
            <a href="{{ route('donations.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                <svg class="w-4 h-4 fill-current -ml-1 mr-2" viewBox="0 0 16 16"><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path></svg>
                <span>Add Donation</span>
            </a>
        </div>
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert"><p>{{ session('success') }}</p></div>
        @endif
        <div class="bg-white shadow-lg rounded-2xl border border-slate-200">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50">
                        <tr>
                            <th class="p-4"><div class="font-semibold text-left">Date</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Donor</div></th>
                            <th class="p-4"><div class="font-semibold text-left">Campus</div></th>
                            <th class="p-4"><div class="font-semibold text-right">Amount</div></th>
                            <th class="p-4"><div class="font-semibold text-center">Actions</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse ($donations as $donation)
                            <tr>
                                <td class="p-4"><div>{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</div></td>
                                <td class="p-4"><div class="font-medium text-slate-800">{{ $donation->donor_name }}</div></td>
                                <td class="p-4"><div>{{ $donation->campus->name ?? 'All Campuses' }}</div></td>
                                <td class="p-4"><div class="text-right font-medium text-green-600">${{ number_format($donation->amount, 2) }}</div></td>
                                <td class="p-4">
                                    <div class="flex justify-center">
                                        <form action="{{ route('donations.destroy', $donation) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-500 hover:text-red-700">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-4 text-center text-slate-500">No donations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-8">{{ $donations->links() }}</div>
    </div>
</x-app-layout>