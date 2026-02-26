<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Teacher Dashboard - Manage Library
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('status'))
                <div class="mb-6 bg-green-100 border-l-4 border-green-500 p-4 text-green-700 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Books List -->
                <div class="md:col-span-2 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h3 class="text-xl font-bold text-gray-800">Available Books</h3>
                        
                        <!-- Search Form -->
                        <form action="{{ route('teacher.dashboard') }}" method="GET" class="w-full sm:w-64">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" 
                                    placeholder="Search books..." 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all"
                                >
                            </div>
                        </form>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($books as $book)
                            <div
                                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition">
                                <div class="p-6">
                                    <h4 class="font-bold text-lg mb-1">{{ $book->title }}</h4>
                                    <p class="text-sm text-gray-500 mb-4">{{ $book->author }} • {{ $book->total_pages }}
                                        pages</p>

                                    <div class="space-y-2">
                                        <button
                                            onclick="openRecommendModal({{ $book->id }}, '{{ addslashes($book->title) }}')"
                                            class="w-full bg-blue-600 text-white py-2 rounded-xl font-bold hover:bg-blue-700 transition flex items-center justify-center shadow-sm">
                                            Recommend
                                        </button>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('reader', $book->id) }}" target="_blank"
                                                class="flex-1 bg-green-50 text-green-700 py-2 rounded-xl font-bold hover:bg-green-100 transition text-center text-sm">
                                                Read
                                            </a>
                                            <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this book? This will also remove the file from Google Drive.');"
                                                class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-full bg-red-50 text-red-700 py-2 rounded-xl font-bold hover:bg-red-100 transition text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Quick Upload placeholder -->
                <div class="space-y-8">
                    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm h-fit">
                        <h3 class="text-lg font-bold mb-4 text-gray-800">Student Progress</h3>
                        <div class="space-y-2">
                            @foreach($students as $student)
                                <a href="{{ route('teacher.students.progress', $student->id) }}"
                                    class="flex items-center justify-between p-3 rounded-2xl hover:bg-blue-50 transition group">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-xs uppercase">
                                            {{ substr($student->name, 0, 2) }}
                                        </div>
                                        <span
                                            class="text-sm font-bold text-gray-700 group-hover:text-blue-600 truncate max-w-[120px]">{{ $student->name }}</span>
                                    </div>
                                    <svg class="w-4 h-4 text-gray-300 group-hover:text-blue-400 transition" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-gray-50 p-6 rounded-3xl border-2 border-dashed border-gray-200 h-fit">
                        <h3 class="text-lg font-bold mb-4">Add More Books</h3>
                        <p class="text-gray-500 text-sm mb-6">Want to expand the library? You can upload new PDFs here.
                        </p>
                        @include('teacher.upload')
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Recommendation Modal -->
    <div id="recommend-modal"
        class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl transform transition-all">
            <h3 class="text-2xl font-bold mb-2">Recommend Book</h3>
            <p id="modal-book-title" class="text-blue-600 font-semibold mb-6"></p>

            <form action="{{ route('recommendations.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="book_id" id="modal-book-id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                    <select name="student_id"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500" required>
                        <option value="">-- Choose a student --</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Personal Note (Optional)</label>
                    <textarea name="note" rows="3"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="e.g. Read this before Friday!"></textarea>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="button" onclick="closeRecommendModal()"
                        class="flex-1 bg-gray-100 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition">Send
                        Recommendation</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRecommendModal(id, title) {
            document.getElementById('modal-book-id').value = id;
            document.getElementById('modal-book-title').textContent = title;
            document.getElementById('recommend-modal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRecommendModal() {
            document.getElementById('recommend-modal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
</x-app-layout>