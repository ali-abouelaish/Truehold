@extends('layouts.admin')

@section('page-title', 'Property Scraper')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white mb-2">Property Scraper</h1>
                <p class="text-gray-300">Manage profile URLs and run the property scraper</p>
            </div>
            <div class="flex space-x-3 flex-wrap">
                <button onclick="runPythonScraper()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-python mr-2"></i>Python Scraper
                </button>
                <button onclick="runPhpScraper()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fab fa-php mr-2"></i>PHP Scraper
                </button>
                @if($scrapeExists)
                <button onclick="importData()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-download mr-2"></i>Import Data
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile Management -->
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-users mr-2 text-blue-400"></i>Profile URLs Management
        </h2>
        
        <!-- Add Profile Form -->
        <form method="POST" action="{{ route('admin.scraper.add-profile') }}" class="mb-6">
            @csrf
            <div class="flex space-x-3">
                <div class="flex-1">
                    <input type="url" name="profile_url" placeholder="https://www.spareroom.co.uk/u123456" 
                           class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500" required>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Profile
                </button>
            </div>
        </form>

        <!-- Profiles List -->
        <div class="space-y-2">
            @if(count($profiles) > 0)
                @foreach($profiles as $index => $profile)
                <div class="flex items-center justify-between bg-gray-800 p-3 rounded-lg border border-gray-600">
                    <div class="flex items-center space-x-3">
                        <span class="text-gray-400 text-sm">#{{ $index + 1 }}</span>
                        <a href="{{ $profile }}" target="_blank" class="text-blue-400 hover:text-blue-300 font-mono text-sm">
                            {{ $profile }}
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admin.scraper.remove-profile') }}" class="inline">
                        @csrf
                        <input type="hidden" name="profile_url" value="{{ $profile }}">
                        <button type="submit" class="text-red-400 hover:text-red-300 p-1" onclick="return confirm('Remove this profile URL?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p>No profile URLs found. Add some to get started!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Scraper Results -->
    @if($scrapeExists)
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-database mr-2 text-green-400"></i>Scraped Data Preview
        </h2>
        
        @if(count($scrapeData) > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-600">
                        <th class="text-left py-2 text-gray-300">Title</th>
                        <th class="text-left py-2 text-gray-300">Agent</th>
                        <th class="text-left py-2 text-gray-300">Location</th>
                        <th class="text-left py-2 text-gray-300">Price</th>
                        <th class="text-left py-2 text-gray-300">Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($scrapeData as $row)
                    <tr class="border-b border-gray-700">
                        <td class="py-2 text-gray-300">{{ $row[1] ?? 'N/A' }}</td>
                        <td class="py-2 text-gray-300">{{ $row[2] ?? 'N/A' }}</td>
                        <td class="py-2 text-gray-300">{{ $row[3] ?? 'N/A' }}</td>
                        <td class="py-2 text-gray-300">{{ $row[7] ?? 'N/A' }}</td>
                        <td class="py-2 text-gray-300">{{ $row[10] ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 text-sm text-gray-400">
            <i class="fas fa-info-circle mr-2"></i>
            Showing first 5 rows. Full data will be imported when you click "Import Data".
        </div>
        @else
        <div class="text-center py-8 text-gray-400">
            <i class="fas fa-database text-4xl mb-3"></i>
            <p>No scraped data found. Run the scraper first!</p>
        </div>
        @endif
    </div>
    @endif

    <!-- Instructions -->
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-info-circle mr-2 text-yellow-400"></i>How to Use
        </h2>
        <div class="space-y-3 text-gray-300">
            <div class="flex items-start space-x-3">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">1</span>
                <p>Add profile URLs from SpareRoom to the list above</p>
            </div>
            <div class="flex items-start space-x-3">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">2</span>
                <p>Click "Python Scraper" (advanced) or "PHP Scraper" (no Python required) to scrape all listings from the profiles</p>
            </div>
            <div class="flex items-start space-x-3">
                <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold">3</span>
                <p>Click "Import Data" to import the scraped data into your database</p>
            </div>
        </div>
    </div>

    <!-- Python Setup Guide -->
    <div class="bg-gray-900 rounded-lg shadow-lg border border-gray-600 p-6">
        <h2 class="text-xl font-bold text-white mb-4">
            <i class="fas fa-cog mr-2 text-green-400"></i>Python Setup Required
        </h2>
        <div class="space-y-4 text-gray-300">
            <p class="text-yellow-400 font-medium">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                The scraper requires Python to be installed on your server.
            </p>
            
            <div class="bg-gray-800 p-4 rounded-lg">
                <h3 class="text-lg font-semibold text-white mb-3">Quick Setup:</h3>
                <ol class="space-y-2 text-sm">
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-400 font-bold">1.</span>
                        <span>Download Python from <a href="https://python.org/downloads" target="_blank" class="text-blue-400 hover:text-blue-300">python.org</a></span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-400 font-bold">2.</span>
                        <span>Install Python with "Add to PATH" option checked</span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-400 font-bold">3.</span>
                        <span>Install required packages: <code class="bg-gray-700 px-2 py-1 rounded text-green-400">pip install requests beautifulsoup4 pandas lxml</code></span>
                    </li>
                    <li class="flex items-start space-x-2">
                        <span class="text-blue-400 font-bold">4.</span>
                        <span>Restart your web server</span>
                    </li>
                </ol>
            </div>
            
            <div class="bg-blue-900 p-4 rounded-lg">
                <h4 class="text-white font-semibold mb-2">
                    <i class="fas fa-file-alt mr-2"></i>Detailed Guide
                </h4>
                <p class="text-sm text-blue-200">
                    For detailed installation instructions, see <code class="bg-blue-800 px-2 py-1 rounded">PYTHON_SETUP_GUIDE.md</code> in your project root.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function runPythonScraper() {
    if (confirm('This will run the Python scraper and may take several minutes. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.scraper.run") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function runPhpScraper() {
    if (confirm('This will run the PHP scraper and may take several minutes. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.scraper.run-php") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function importData() {
    if (confirm('This will import the scraped data into your database. Continue?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.scraper.import") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
