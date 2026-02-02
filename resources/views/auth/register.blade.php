<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Registration - Property Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    
    <style>
        .auth-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 2px solid #4b5563;
        }
        
        .auth-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .auth-input {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 12px 16px;
            transition: all 0.2s ease;
        }
        
        .auth-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }
        
        .auth-button {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid #4b5563;
            transition: all 0.2s ease;
        }
        
        .auth-button:hover {
            background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="auth-header shadow-lg">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <div class="flex justify-between items-center py-6">
                    <div class="flex items-center">
                        <i class="fas fa-building text-3xl text-blue-400 mr-4"></i>
                        <h1 class="text-3xl font-bold text-white">Property Scraper</h1>
                    </div>
                    <div class="text-sm text-gray-300">
                        <span>Agent Portal</span>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-md mx-auto px-6 py-12">
            <div class="auth-card p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-user-plus text-4xl text-blue-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900">Agent Registration</h2>
                    <p class="text-gray-600 mt-2">Create your agent account</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Registration failed:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-blue-600"></i>Full Name
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                               class="auth-input w-full" required autofocus>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="auth-input w-full" required>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Password
                        </label>
                        <input type="password" id="password" name="password" 
                               class="auth-input w-full" required>
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Confirm Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="auth-input w-full" required>
                    </div>
                    
                    <div>
                        <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user-tag mr-2 text-blue-600"></i>Role
                        </label>
                        <select id="role" name="role" class="auth-input w-full" required>
                            <option value="">Select your role</option>
                            <option value="agent" {{ old('role') == 'agent' ? 'selected' : '' }}>Agent</option>
                            <option value="marketing_agent" {{ old('role') == 'marketing_agent' ? 'selected' : '' }}>Marketing Agent</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="auth-button w-full text-white py-3 px-4 rounded-lg font-medium">
                        <i class="fas fa-user-plus mr-2"></i>Create Account
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
