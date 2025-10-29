<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Application - TRUEHOLD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.properties-navigation')

        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-6 text-center">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Client Application</h1>
                <p class="text-gray-600 dark:text-gray-300">Fill in your details below. No login required.</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-700 dark:text-green-300">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-700 dark:text-red-300">
                    <p class="font-semibold mb-2">Please fix the following:</p>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('public.client.store') }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name *</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth *</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number *</label>
                    <input type="tel" name="phone_number" value="{{ old('phone_number') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nationality *</label>
                    <input type="text" name="nationality" value="{{ old('nationality') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Position/Role *</label>
                    <input type="text" name="position_role" value="{{ old('position_role') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Address *</label>
                    <textarea name="current_address" rows="2" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>{{ old('current_address') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company/University Name *</label>
                    <input type="text" name="company_university_name" value="{{ old('company_university_name') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company/University Address *</label>
                    <input type="text" name="company_university_address" value="{{ old('company_university_address') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Landlord/Agency Name *</label>
                    <input type="text" name="current_landlord_name" value="{{ old('current_landlord_name') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Landlord/Agency Contact *</label>
                    <input type="text" name="current_landlord_contact_info" value="{{ old('current_landlord_contact_info') }}" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg" required>
                </div>
            </div>

            <div class="flex items-start space-x-2">
                <input id="privacy_consent" name="privacy_consent" type="checkbox" class="mt-1" required {{ old('privacy_consent') ? 'checked' : '' }}>
                <label for="privacy_consent" class="text-sm text-gray-700 dark:text-gray-300">I consent to TRUEHOLD storing and processing my data for the purpose of this application.</label>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full md:w-auto text-white px-6 py-2 rounded-lg font-medium"
                        style="background: linear-gradient(135deg, #1f2937, #374151); border: 1px solid #fbbf24;"
                        onmouseover="this.style.background='linear-gradient(135deg, #fbbf24, #f59e0b)'; this.style.color='#1f2937';"
                        onmouseout="this.style.background='linear-gradient(135deg, #1f2937, #374151)'; this.style.color='#ffffff';">
                    Submit Application
                </button>
            </div>
        </form>
        </div>
    </div>
</body>
</html>


