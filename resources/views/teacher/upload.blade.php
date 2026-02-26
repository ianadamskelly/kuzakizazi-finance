<div class="space-y-4">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Upload New Book</h2>

    <!-- PDF Compression Tip -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" width="20" height="20" style="width: 20px; height: 20px;"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <span class="font-bold">Tip:</span> Use compressed PDFs to help students on mobile networks save
                    data.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4"
        id="upload-form">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Book Title</label>
            <input type="text" name="title" value="{{ old('title') }}"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror"
                placeholder="Enter book title">
            @error('title')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Author Name</label>
            <input type="text" name="author" value="{{ old('author') }}"
                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('author') border-red-500 @enderror"
                placeholder="Enter author name">
            @error('author')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Upload PDF</label>
            <input type="file" name="pdf_file" accept="application/pdf"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('pdf_file') border-red-500 @enderror">
            @error('pdf_file')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Cover Image (Optional)</label>
            <input type="file" name="cover_image" accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
        </div>

        <button type="submit" id="submit-btn"
            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition flex justify-center items-center">
            <span id="btn-text">Add to Library</span>
            <span id="btn-spinner" class="hidden">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </span>
        </button>
    </form>

    <script>
        document.getElementById('upload-form').addEventListener('submit', function () {
            const btn = document.getElementById('submit-btn');
            const text = document.getElementById('btn-text');
            const spinner = document.getElementById('btn-spinner');

            btn.disabled = true;
            text.classList.add('hidden');
            spinner.classList.remove('hidden');
        });
    </script>