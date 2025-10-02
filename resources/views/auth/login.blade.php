<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Login - TRUEHOLD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
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
                        <h1 class="text-3xl font-bold text-white">TRUEHOLD</h1>
                    </div>
                    <div class="text-sm text-gray-300">
                        <a href="{{ route('properties.index') }}" class="hover:text-white transition-colors">
                            <i class="fas fa-home mr-1"></i>View Site
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-md mx-auto px-6 py-12">
            <div class="auth-card p-8">
                <div class="text-center mb-8">
                    <i class="fas fa-user-shield text-4xl text-blue-600 mb-4"></i>
                    <h2 class="text-2xl font-bold text-gray-900">Agent Sign In</h2>
                    <p class="text-gray-600 mt-2">Access your property management dashboard</p>
                </div>

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Login failed:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
                    @csrf
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="auth-input w-full" required autofocus>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Password
                        </label>
                        <input type="password" id="password" name="password" 
                               class="auth-input w-full" required>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    
                    <button type="submit" class="auth-button w-full text-white py-3 px-4 rounded-lg font-medium">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        <a href="{{ route('properties.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Properties
                        </a>
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                        Contact your administrator to create an agent account
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
