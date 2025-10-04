<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Map - TRUEHOLD</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    
    <style>
        /* Admin Dashboard Dark Mode Styling */
        body {
            background-color: #111827 !important;
            color: #f9fafb !important;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        html {
            background-color: #111827 !important;
        }
        
        .main-content {
            background-color: #111827 !important;
        }
        
        /* Dark mode overrides */
        .bg-white {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        
        .text-gray-900 {
            color: #d1d5db !important;
        }
        
        .text-gray-700 {
            color: #9ca3af !important;
        }
        
        .text-gray-500 {
            color: #6b7280 !important;
        }
        
        /* Lighter placeholders */
        input::placeholder, textarea::placeholder, select::placeholder {
            color: #9ca3af !important;
        }
        
        /* Form inputs dark mode */
        input, textarea, select {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        input:focus, textarea:focus, select:focus {
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 1px #fbbf24 !important;
        }
        
        /* All labels lighter - comprehensive coverage */
        label, .form-label, .label, .field-label, .control-label, .input-label,
        .col-form-label, .custom-control-label, .form-check-label, .radio-label,
        .checkbox-label, .switch-label, .toggle-label, .btn-label,
        .nav-label, .menu-label, .sidebar-label, .card-label,
        .table-label, .list-label, .item-label, .section-label {
            color: #d1d5db !important;
        }
        
        /* Field labels and descriptions */
        .field-label, .field-description, .help-text {
            color: #d1d5db !important;
        }
        
        /* Required field indicators */
        .required, .asterisk {
            color: #fbbf24 !important;
        }
        
        /* Form groups and containers */
        .form-group, .form-control-group {
            margin-bottom: 1rem;
        }
        
        /* Input groups */
        .input-group-text {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        /* Form validation */
        .is-invalid {
            border-color: #ef4444 !important;
        }
        
        .invalid-feedback {
            color: #ef4444 !important;
        }
        
        .is-valid {
            border-color: #10b981 !important;
        }
        
        /* Form sections and headers */
        .form-section, .card-header h3, .card-header h4, .card-header h5, .card-header h6 {
            color: #d1d5db !important;
        }
        
        /* Help text and descriptions */
        .form-text, .help-block, .field-help {
            color: #9ca3af !important;
        }
        
        /* Specific form elements */
        .form-control, .form-select, .form-check-input {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #374151 !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 0.2rem rgba(251, 191, 36, 0.25) !important;
            color: #d1d5db !important;
        }
        
        /* Checkboxes and radio buttons */
        .form-check-label {
            color: #d1d5db !important;
        }
        
        .form-check-input:checked {
            background-color: #fbbf24 !important;
            border-color: #fbbf24 !important;
        }
        
        /* Select dropdowns */
        select option {
            background-color: #374151 !important;
            color: #d1d5db !important;
        }
        
        /* Text areas */
        textarea.form-control {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        /* Admin panel specific labels */
        .admin-label, .panel-label, .dashboard-label, .content-label,
        .widget-label, .component-label, .module-label, .feature-label,
        .setting-label, .option-label, .config-label, .preference-label {
            color: #d1d5db !important;
        }
        
        /* Table and list labels */
        th, .table-header, .list-header, .group-header {
            color: #d1d5db !important;
        }
        
        /* Navigation labels */
        .nav-link, .menu-item, .sidebar-link {
            color: #d1d5db !important;
        }
        
        /* Card and widget labels */
        .card-title, .widget-title, .panel-title, .section-title {
            color: #d1d5db !important;
        }
        
        /* Button labels */
        .btn-text, .button-text, .link-text {
            color: #d1d5db !important;
        }
        
        /* Catch-all for any remaining labels */
        [class*="label"], [class*="title"], [class*="header"] {
            color: #d1d5db !important;
        }
        
        /* Override any white text that should be lighter */
        .text-white {
            color: #d1d5db !important;
        }
        
        /* Ensure all form-related text is lighter */
        .form-text, .form-description, .field-description {
            color: #9ca3af !important;
        }
        
        .border-gray-200 {
            border-color: #374151 !important;
        }
        
        .bg-gray-50 {
            background-color: #374151 !important;
        }
        
        .bg-gray-100 {
            background-color: #4b5563 !important;
        }
        
        /* Gold accent colors */
        .text-blue-600, .text-green-600, .text-purple-600, .text-orange-600, .text-red-600, .text-indigo-600, .text-teal-600, .text-yellow-600 {
            color: #fbbf24 !important;
        }
        
        .border-blue-200, .border-green-200, .border-purple-200, .border-orange-200, .border-red-200, .border-indigo-200, .border-teal-200, .border-yellow-200 {
            border-color: #fbbf24 !important;
        }
        
        /* Tables dark mode */
        table {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
        }
        
        th {
            background-color: #374151 !important;
            color: #d1d5db !important;
            border-color: #4b5563 !important;
        }
        
        td {
            background-color: #1f2937 !important;
            color: #d1d5db !important;
            border-color: #4b5563 !important;
        }
        
        tr:hover td {
            background-color: #374151 !important;
        }
        
        /* Cards dark mode */
        .card, .bg-white {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        
        /* Standardized button styles */
        .btn, button, input[type="submit"], input[type="button"], input[type="reset"] {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border: 1px solid #6b7280 !important;
            color: #d1d5db !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
        }
        
        .btn:hover, button:hover, input[type="submit"]:hover, input[type="button"]:hover, input[type="reset"]:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #fbbf24 !important;
            color: #f9fafb !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }
        
        .btn:active, button:active, input[type="submit"]:active, input[type="button"]:active, input[type="reset"]:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2) !important;
        }
        
        /* Primary buttons (gold accent) */
        .btn-primary, .btn-success, .btn-warning, .btn-info {
            background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
            border-color: #f59e0b !important;
            color: #111827 !important;
        }
        
        .btn-primary:hover, .btn-success:hover, .btn-warning:hover, .btn-info:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            border-color: #d97706 !important;
            color: #111827 !important;
        }
        
        /* Danger buttons (red) */
        .btn-danger, .btn-delete, .btn-remove {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            border-color: #dc2626 !important;
            color: #ffffff !important;
        }
        
        .btn-danger:hover, .btn-delete:hover, .btn-remove:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            border-color: #b91c1c !important;
            color: #ffffff !important;
        }
        
        /* Secondary buttons (dark gray) */
        .btn-secondary, .btn-outline, .btn-cancel {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border-color: #6b7280 !important;
            color: #d1d5db !important;
        }
        
        .btn-secondary:hover, .btn-outline:hover, .btn-cancel:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #9ca3af !important;
            color: #f9fafb !important;
        }
        
        /* Button sizes */
        .btn-sm {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.875rem !important;
        }
        
        .btn-lg {
            padding: 0.75rem 1.5rem !important;
            font-size: 1.125rem !important;
        }
        
        /* Button states */
        .btn:disabled, button:disabled, input[type="submit"]:disabled, input[type="button"]:disabled, input[type="reset"]:disabled {
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            transform: none !important;
        }
        
        /* Remove all white backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #1f2937 !important;
        }
        
        /* Make all text light */
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400, .text-gray-300, .text-gray-200, .text-gray-100, .text-gray-50 {
            color: #d1d5db !important;
        }
        
        /* Map-specific styling */
        .gradient-header {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid #374151;
        }
        
        .gradient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.9) 0%, rgba(55, 65, 81, 0.9) 100%);
            z-index: 1;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .glass-card {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(75, 85, 99, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #d1d5db;
        }
        
        .glass-card:hover {
            background: rgba(55, 65, 81, 0.98);
            border-color: rgba(251, 191, 36, 0.4);
            color: #f9fafb;
            transform: scale(1.05);
        }
        
        .filters-section {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-bottom: 1px solid rgba(75, 85, 99, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .action-button {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border: 1px solid #6b7280 !important;
            color: #d1d5db !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
        }
        
        .action-button:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #fbbf24 !important;
            color: #f9fafb !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }
        
        .secondary-button {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border: 1px solid #6b7280 !important;
            color: #d1d5db !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
        }
        
        .secondary-button:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #9ca3af !important;
            color: #f9fafb !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }
        
        .search-button {
            background: linear-gradient(135deg, #fbbf24, #f59e0b) !important;
            border-color: #f59e0b !important;
            color: #111827 !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.375rem !important;
            font-weight: 500 !important;
            transition: all 0.3s ease !important;
            text-decoration: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
        }
        
        .search-button:hover {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
            border-color: #d97706 !important;
            color: #111827 !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3) !important;
        }
        
        .filter-input {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 1rem !important;
            transition: all 0.3s ease !important;
        }
        
        .filter-input:focus {
            outline: none !important;
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 1px #fbbf24 !important;
        }
        
        .filter-label {
            font-weight: 600;
            color: #d1d5db !important;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .filter-badge {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border: 1px solid #3b82f6;
            color: #1e40af;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.25rem;
        }
        
        .results-summary {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #0ea5e9;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.1);
        }
        
        .success-message {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border: 1px solid #fbbf24;
            color: #d1d5db;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 2rem;
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Filter toggle button */
        .filter-toggle {
            display: block;
            width: 100%;
            padding: 16px 20px;
            background: linear-gradient(135deg, #374151, #4b5563);
            border: 2px solid #6b7280;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            color: #d1d5db;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .filter-toggle:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280);
            border-color: #fbbf24;
            color: #f9fafb;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .filter-toggle:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .filters-content {
            display: none;
            opacity: 0;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .filters-content.show {
            display: block;
            opacity: 1;
            max-height: 2000px;
        }
        
        /* Remove bottom margin when collapsed to prevent white space */
        .filters-collapsed .filters-section {
            margin-bottom: 0;
            padding-bottom: 1rem;
        }
        
        .filters-collapsed .filters-content {
            margin-bottom: 0;
        }
        
        /* Mobile-first responsive design */
        @media (max-width: 767px) {
            .action-button, .secondary-button, .search-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                min-height: 44px;
            }
            
            .filter-input {
                padding: 0.75rem;
                font-size: 1rem;
                min-height: 44px;
            }
            
            .filters-section {
                padding: 1.5rem 1rem;
            }
            
            .filters-section .max-w-7xl {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (max-width: 767px) {
            button, select, input, a {
                min-height: 44px;
                min-width: 44px;
            }
        }
        
        #map {
            height: calc(100vh - 200px);
            width: 100%;
            min-height: 400px;
            transition: height 0.3s ease;
            position: relative;
        }
        
        /* Responsive map height */
        @media (max-width: 768px) {
            #map {
                height: calc(100vh - 150px);
                min-height: 300px;
            }
        }
        
        /* When filters are collapsed, expand map */
        .filters-collapsed #map {
            height: calc(100vh - 120px);
        }
        
        @media (max-width: 768px) {
            .filters-collapsed #map {
                height: calc(100vh - 100px);
            }
        }
        
        
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1f2937;
            color: #d1d5db;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #374151;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
        }
        
        .error-message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fee2e2;
            color: #dc2626;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            z-index: 1000;
            max-width: 400px;
        }
        
        .info-window {
            max-width: 280px;
            padding: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
            border: 1px solid rgba(229, 231, 235, 0.5);
        }
        
        .property-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 7px 28px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(229, 231, 235, 0.5);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.15);
            border-color: #3b82f6;
        }
        
        .property-image {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .property-card:hover .property-image {
            transform: scale(1.08);
        }
        
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            color: #374151;
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-button:hover {
            background: #f9fafb;
            transform: translateY(-2px);
        }
        
        
        .filters-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            min-width: 250px;
            max-height: 80vh;
                overflow-y: auto;
            }
            
        .filters-panel h3 {
            margin: 0 0 15px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .filter-group {
            margin-bottom: 15px;
        }
        
        .filter-label {
            display: block;
                font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        
        .filter-input {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
                font-size: 14px;
            transition: border-color 0.2s;
        }
        
        .filter-input:focus {
            outline: none;
            border-color: #3b82f6;
        }
        
        .filter-button {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            margin: 5px 0;
            transition: background-color 0.2s;
        }
        
        .filter-button:hover {
            background: #2563eb;
        }
        
        .filter-button.secondary {
            background: #6b7280;
        }
        
        .filter-button.secondary:hover {
            background: #4b5563;
        }
        
        .toggle-panel {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 16px;
            border-radius: 8px;
            cursor: pointer;
                font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .toggle-panel:hover {
            background: #2563eb;
        }
        
        .panel-hidden {
            display: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">

        <!-- Property Map Header -->
        <div class="bg-white border-b border-gray-200 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Property Map</h1>
                        <div class="hidden sm:block h-6 w-px bg-gray-300"></div>
                        <div class="text-sm sm:text-base text-gray-600">
                            <i class="fas fa-map-marked-alt mr-2"></i><span id="propertyCount">Loading properties...</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                        @auth
                        @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200 text-sm sm:text-base">
                            <i class="fas fa-sign-in-alt mr-2"></i>Login
                        </a>
                        @endauth
                        <a href="{{ route('properties.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg font-medium transition-colors text-sm sm:text-base">
                            <i class="fas fa-list mr-2"></i>List View
                        </a>
                </div>
            </div>
            </div>
        </div>

        <!-- Success Messages -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4">
                <div class="success-message animate-fade-in">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <span class="text-lg font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Enhanced Filters -->
        <div class="filters-section">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <!-- Filter toggle button -->
                <button type="button" class="filter-toggle w-full mb-4" onclick="toggleFilters()">
                    <i class="fas fa-filter mr-2"></i>
                    <span id="filterToggleText">Show Filters</span>
                    <i class="fas fa-chevron-down ml-2" id="filterToggleIcon"></i>
                </button>
                
                <div class="filters-content" id="filtersContent">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 space-y-4 sm:space-y-0">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 flex items-center space-x-2 sm:space-x-3">
                            <div class="bg-blue-100 p-2 sm:p-3 rounded-full">
                                <i class="fas fa-filter text-blue-600 text-lg sm:text-xl"></i>
                            </div>
                            <span>Search Filters</span>
                        </h2>
                        <div class="flex flex-col sm:flex-row items-start sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                            <a href="{{ route('properties.map') }}" class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                            <i class="fas fa-times mr-2"></i>Clear Filters
                        </a>
                            <a href="{{ route('properties.index') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-300 text-sm sm:text-base">
                                <i class="fas fa-list mr-2"></i>Switch to List View
                        </a>
                    </div>
                </div>
                
                
                    <!-- Combined filters form -->
                    <form id="filterForm" class="space-y-4 sm:space-y-6">
                        <!-- First row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                                <label class="filter-label text-sm sm:text-base">Location</label>
                                <select id="locationFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Locations</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>
                                        {{ $location }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                                <label class="filter-label text-sm sm:text-base">Property Type</label>
                                <select id="propertyTypeFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Types</option>
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type }}" {{ request('property_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        @auth
                        <div>
                                <label class="filter-label text-sm sm:text-base">Agent Name</label>
                                <select id="agentFilter" class="filter-input w-full text-sm sm:text-base">
                                <option value="">All Agents</option>
                                @foreach($agentNames as $agent)
                                    <option value="{{ $agent }}" {{ request('agent_name') == $agent ? 'selected' : '' }}>
                                        {{ $agent }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endauth
                        
                        </div>
                        
                        <!-- Second row of filters -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                        <div>
                                <label class="filter-label text-sm sm:text-base">Min Price</label>
                                <input type="number" id="minPriceFilter" value="{{ request('min_price') }}" 
                                       placeholder="£0" class="filter-input w-full text-sm sm:text-base">
                        </div>
                    
                        <div>
                                <label class="filter-label text-sm sm:text-base">Max Price</label>
                                <input type="number" id="maxPriceFilter" value="{{ request('max_price') }}" 
                                       placeholder="£5000" class="filter-input w-full text-sm sm:text-base">
                        </div>
                        
                        <div>
                                <label class="filter-label text-sm sm:text-base">Couples Allowed</label>
                                <select id="couplesAllowedFilter" class="filter-input w-full text-sm sm:text-base">
                                    <option value="">All Properties</option>
                                    <option value="yes" {{ request('couples_allowed') == 'yes' ? 'selected' : '' }}>Couples Welcome</option>
                                    <option value="no" {{ request('couples_allowed') == 'no' ? 'selected' : '' }}>Singles Only</option>
                                </select>
                        </div>
                        
                            <div class="flex flex-col sm:flex-row items-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="button" onclick="applyFilters()" class="search-button w-full text-sm sm:text-base">
                                    <i class="fas fa-search mr-2"></i>Search Properties
                            </button>
                                <button type="button" onclick="clearFilters()" class="secondary-button text-sm sm:text-base">
                                <i class="fas fa-times mr-2"></i>Clear
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>



    <!-- Loading Screen -->
    <div class="loading" id="loadingScreen">
        <i class="fas fa-spinner fa-spin text-3xl text-blue-600 mb-3"></i>
        <div class="text-lg font-semibold text-gray-800">Loading Map...</div>
        <div class="text-sm text-gray-600">Please wait while we load the properties</div>
                </div>
                
    <!-- Error Screen -->
    <div class="error-message" id="errorScreen" style="display: none;">
        <i class="fas fa-exclamation-triangle text-3xl text-red-600 mb-3"></i>
        <div class="text-lg font-semibold text-gray-800">Map Error</div>
        <div class="text-sm text-gray-600" id="errorMessage">Something went wrong loading the map</div>
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors mt-3" onclick="location.reload()">
            <i class="fas fa-refresh mr-2"></i>Retry
        </button>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Properties Data -->
    <div id="properties-data" style="display: none;">{!! json_encode($properties) !!}</div>

    <!-- Google Maps API -->
    @if(config('services.google.maps_api_key') && config('services.google.maps_api_key') !== 'YOUR_GOOGLE_MAPS_API_KEY')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap&v=weekly&loading=async"></script>
    @else
        <script>
            // Show API key error immediately
            document.addEventListener('DOMContentLoaded', function() {
                showError('Google Maps API key not configured. Please add GOOGLE_MAPS_API_KEY to your .env file. See GOOGLE_MAPS_SETUP.md for instructions.');
            });
        </script>
    @endif
    

    <script>
        // Global variables
        let map;
        let markers = [];
        let infoWindow;
        let properties = [];
        let filteredProperties = [];
        let agentStats = {};
        let agentColors = {};
        let showOthersOnly = false;

        // Initialize map when Google Maps API loads
        function initMap() {
            try {
                console.log('🗺️ Initializing map...');
                
                // Hide loading screen
                document.getElementById('loadingScreen').style.display = 'none';
                
                // Create map with dark theme
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 51.5074, lng: -0.1278 }, // London center
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                        { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                        { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                        {
                            featureType: "administrative.locality",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#d59563" }]
                        },
                        {
                            featureType: "poi",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#d59563" }]
                        },
                        {
                            featureType: "poi.park",
                            elementType: "geometry",
                            stylers: [{ color: "#263c3f" }]
                        },
                        {
                            featureType: "poi.park",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#6b9a76" }]
                        },
                        {
                            featureType: "road",
                            elementType: "geometry",
                            stylers: [{ color: "#38414e" }]
                        },
                        {
                            featureType: "road",
                            elementType: "geometry.stroke",
                            stylers: [{ color: "#212a37" }]
                        },
                        {
                            featureType: "road",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#9ca5b3" }]
                        },
                        {
                            featureType: "road.highway",
                            elementType: "geometry",
                            stylers: [{ color: "#746855" }]
                        },
                        {
                            featureType: "road.highway",
                            elementType: "geometry.stroke",
                            stylers: [{ color: "#1f2835" }]
                        },
                        {
                            featureType: "road.highway",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#f3d19c" }]
                        },
                        {
                            featureType: "transit",
                            elementType: "geometry",
                            stylers: [{ color: "#2f3948" }]
                        },
                        {
                            featureType: "transit.station",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#d59563" }]
                        },
                        {
                            featureType: "water",
                            elementType: "geometry",
                            stylers: [{ color: "#17263c" }]
                        },
                        {
                            featureType: "water",
                            elementType: "labels.text.fill",
                            stylers: [{ color: "#515c6d" }]
                        },
                        {
                            featureType: "water",
                            elementType: "labels.text.stroke",
                            stylers: [{ color: "#17263c" }]
                        }
                    ]
                });
                
                // Create info window
                infoWindow = new google.maps.InfoWindow();


                // Load properties
                loadProperties();
                
                console.log('✅ Map initialized successfully');
                
                } catch (error) {
                console.error('❌ Map initialization failed:', error);
                showError('Failed to initialize map: ' + error.message);
            }
        }

        // Load properties from data attribute
        function loadProperties() {
            try {
                const propertiesData = document.getElementById('properties-data');
                if (!propertiesData) {
                    throw new Error('Properties data not found');
                }

                const propertiesJson = propertiesData.textContent;
                if (!propertiesJson || propertiesJson.trim() === '') {
                    throw new Error('No properties data available');
                }

                properties = JSON.parse(propertiesJson);
                console.log(`📊 Loaded ${properties.length} properties`);

                // Filter properties with valid coordinates
                const validProperties = properties.filter(property => {
                    const lat = parseFloat(property.latitude);
                    const lng = parseFloat(property.longitude);
                    return !isNaN(lat) && !isNaN(lng) && 
                           lat >= -90 && lat <= 90 && 
                           lng >= -180 && lng <= 180;
                });

                console.log(`📍 Found ${validProperties.length} properties with valid coordinates`);

                if (validProperties.length === 0) {
                    showError('No properties with valid coordinates found');
                    return;
                }
                
                // Update property count
                document.getElementById('propertyCount').textContent = 
                    `${validProperties.length} properties loaded`;

                // Initialize agent colors
                initializeAgentColors(validProperties);

                // Initialize filters from URL
                initializeFiltersFromURL();

                // Add event listeners for real-time filtering
                addFilterEventListeners();

                // Create markers
                createMarkers(validProperties);


                // Fit map to bounds
                fitMapToProperties(validProperties);

                    } catch (error) {
                console.error('❌ Error loading properties:', error);
                showError('Failed to load properties: ' + error.message);
            }
        }

        // Create markers for properties
        function createMarkers(properties) {
            try {
                // Clear existing markers
                clearMarkers();

                    const bounds = new google.maps.LatLngBounds();

                properties.forEach((property, index) => {
                    const lat = parseFloat(property.latitude);
                    const lng = parseFloat(property.longitude);
                    const agentColor = getAgentColor(property.agent_name);
                    
                    console.log(`🎨 Property "${property.title}" (Agent: "${property.agent_name}") gets color:`, agentColor);

                    // Create marker
                        const marker = new google.maps.Marker({
                        position: { lat, lng },
                        map: map,
                            title: property.title || 'Property',
                            icon: {
                                path: google.maps.SymbolPath.CIRCLE,
                                scale: 8,
                            fillColor: agentColor.fill,
                                fillOpacity: 1,
                            strokeColor: agentColor.stroke,
                                strokeWeight: 2
                            }
                        });
                        
                    // Add click listener
                    marker.addListener('click', () => {
                        showPropertyInfo(property, marker);
                    });

                    // Add to markers array
                                markers.push(marker);
                    bounds.extend({ lat, lng });
                });


                console.log(`✅ Created ${markers.length} markers`);

                            } catch (error) {
                console.error('❌ Error creating markers:', error);
                showError('Failed to create markers: ' + error.message);
            }
        }

        // Show property info in info window
        function showPropertyInfo(property, marker) {
            try {
                const content = createInfoWindowContent(property);
                infoWindow.setContent(content);
                                    infoWindow.open(map, marker);
                            } catch (error) {
                console.error('❌ Error showing property info:', error);
            }
        }

        // Create info window content with card style matching listing view
        function createInfoWindowContent(property) {
            const title = property.title || 'Untitled Property';
            const location = property.location || 'Location not specified';
            const price = property.formatted_price || property.price || 'Price on request';
            const propertyType = property.property_type || 'Unknown type';
            const description = property.description || 'No description available';
            const bedrooms = property.bedrooms || 'N/A';
            const bathrooms = property.bathrooms || 'N/A';
            const availableDate = property.available_date || 'N/A';
            const propertyId = property.id || '';
            const photoCount = property.photo_count || 0;
            const allPhotos = property.all_photos_array || [];

            // Get first image with fallback
            let imageHtml = '';
            if (property.high_quality_photos_array && property.high_quality_photos_array.length > 0) {
                imageHtml = `<img src="${property.high_quality_photos_array[0]}" alt="${title}" class="property-image w-full h-24 object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            } else if (property.first_photo_url && property.first_photo_url !== 'N/A') {
                imageHtml = `<img src="${property.first_photo_url}" alt="${title}" class="property-image w-full h-24 object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            } else if (property.photos && property.photos.length > 0) {
                imageHtml = `<img src="${property.photos[0]}" alt="${title}" class="property-image w-full h-24 object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />`;
            }

            // Add placeholder if no image
            if (!imageHtml) {
                imageHtml = `<div class="flex items-center justify-center h-24 bg-gradient-to-br from-gray-100 to-gray-200"><i class="fas fa-home text-3xl text-gray-400"></i></div>`;
                    } else {
                imageHtml += `<div class="flex items-center justify-center h-24 bg-gradient-to-br from-gray-100 to-gray-200" style="display: none;"><i class="fas fa-home text-3xl text-gray-400"></i></div>`;
            }

            // Photo count badge
            let photoBadgeHtml = '';
            if ((allPhotos && allPhotos.length > 0) || photoCount > 0) {
                const count = allPhotos.length > 0 ? allPhotos.length : photoCount;
                photoBadgeHtml = `
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur-sm border border-white/20 text-gray-800 px-2 py-1 rounded-full font-semibold text-xs shadow-md">
                        <i class="fas fa-camera mr-1"></i>
                        ${count}
                    </div>
                `;
            }

            return `
                <div class="property-card w-30 max-w-30 bg-white rounded-xl shadow-lg border-0 overflow-hidden cursor-pointer group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 transform" onclick="window.open('/properties/${propertyId}', '_blank')">
                    <!-- Property Image with Overlay -->
                    <div class="relative h-24 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                        ${imageHtml}
                        ${photoBadgeHtml}
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Price Badge -->
                        <div class="absolute bottom-1 left-1 right-1">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-md px-3 py-2 shadow-lg">
                                <div class="text-sm font-bold text-center">${price}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Property Details -->
                    <div class="p-3">
                        <!-- Title and Location -->
                        <div class="mb-2">
                            <h3 class="font-bold text-xs text-gray-900 line-clamp-2 leading-tight group-hover:text-blue-600 transition-colors duration-300 mb-1">
                                ${title.length > 25 ? title.substring(0, 25) + '...' : title}
                            </h3>
                            <div class="flex items-center text-gray-500 text-xs">
                                <i class="fas fa-map-marker-alt text-red-500 mr-1"></i>
                                <span class="truncate">${location.length > 20 ? location.substring(0, 20) + '...' : location}</span>
                            </div>
                        </div>
                        
                        <!-- Price and Property Type -->
                        <div class="mb-3 flex items-center justify-between">
                            <div class="text-lg font-bold text-green-600">
                                ${price}
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-home mr-1"></i>
                                ${propertyType}
                            </span>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="flex flex-col space-y-1">
                            <button class="w-full bg-gradient-to-r from-blue-600 via-blue-700 to-blue-800 hover:from-blue-700 hover:via-blue-800 hover:to-blue-900 text-white px-3 py-2 rounded-lg font-semibold text-xs transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg flex items-center justify-center">
                                <i class="fas fa-eye mr-1"></i>
                                View Details
                            </button>
                            
                            ${availableDate && availableDate !== 'N/A' ? `
                                <div class="flex items-center justify-center text-xs text-green-600 bg-green-50 px-2 py-1 rounded-md font-medium border border-green-200">
                                    <i class="fas fa-calendar mr-1"></i>
                                    ${availableDate}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Generate colors for property managers
        let companyColors = {};
        let colorPalette = [
            '#ef4444', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
            '#ec4899', '#14b8a6', '#f43f5e', '#8b5a2b', '#7c3aed', '#059669', '#dc2626', '#0891b2',
            '#be185d', '#7c2d12', '#1e40af', '#e11d48', '#0d9488', '#ca8a04', '#9333ea', '#0891b2',
            '#dc2626', '#059669', '#7c3aed', '#8b5a2b', '#f43f5e', '#14b8a6', '#ec4899', '#6366f1',
            '#f97316', '#84cc16', '#06b6d4', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444', '#e11d48',
            '#0d9488', '#ca8a04', '#9333ea', '#0891b2', '#dc2626', '#059669', '#7c3aed', '#8b5a2b'
        ];

        // Initialize agent colors based on unique agent names
        function initializeAgentColors(properties) {
            const uniqueAgents = [...new Set(properties.map(p => p.agent_name).filter(a => a && a !== 'N/A' && a !== '' && a !== null))];
            
            console.log('🎨 Found unique agents:', uniqueAgents);
            
            uniqueAgents.forEach((agent, index) => {
                const colorIndex = index % colorPalette.length;
                agentColors[agent] = {
                    fill: colorPalette[colorIndex],
                    stroke: '#ffffff'
                };
                console.log(`🎨 Agent "${agent}" gets color: ${colorPalette[colorIndex]}`);
            });

            // Add Others category for null/empty values
            agentColors['Others'] = { fill: '#6b7280', stroke: '#ffffff' };
            agentColors['default'] = { fill: '#3b82f6', stroke: '#ffffff' };

            console.log('🎨 Initialized colors for agents:', Object.keys(agentColors));
        }

        // Get agent color for markers
        function getAgentColor(agent) {
            if (!agent || agent === 'N/A' || agent === '' || agent === null) {
                return agentColors['Others'] || agentColors['default'];
            }

            // Check for exact match first
            if (agentColors[agent]) {
                return agentColors[agent];
            }

            // Check for partial matches
            for (const [key, color] of Object.entries(agentColors)) {
                if (key !== 'default' && key !== 'Others' && key !== 'N/A' && key !== '') {
                    if (agent.toLowerCase().includes(key.toLowerCase()) || 
                        key.toLowerCase().includes(agent.toLowerCase())) {
                        return color;
                    }
                }
            }

            // Generate a new color for unknown agents
            const colorIndex = Object.keys(agentColors).length % colorPalette.length;
            const newColor = {
                fill: colorPalette[colorIndex],
                stroke: '#ffffff'
            };
            agentColors[agent] = newColor;
            return newColor;
        }

        // Clear all markers
        function clearMarkers() {
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            
        }

        // Fit map to all properties
        function fitMapToProperties(properties) {
            if (properties.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            properties.forEach(property => {
                bounds.extend({
                    lat: parseFloat(property.latitude),
                    lng: parseFloat(property.longitude)
                });
            });

            if (properties.length === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(15);
                        } else {
                map.fitBounds(bounds);
            }
        }


        // Fit map to bounds
        function fitToBounds() {
            if (properties.length === 0) return;
            
            const validProperties = properties.filter(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                return !isNaN(lat) && !isNaN(lng);
            });
            
            fitMapToProperties(validProperties);
        }

        // Reset map view
        function resetMap() {
            map.setCenter({ lat: 51.5074, lng: -0.1278 });
            map.setZoom(12);
        }

        // Show error message
        function showError(message) {
            document.getElementById('loadingScreen').style.display = 'none';
            document.getElementById('errorScreen').style.display = 'block';
            document.getElementById('errorMessage').textContent = message;
        }

        // Handle Google Maps API errors
        window.gm_authFailure = function() {
            showError('Google Maps API authentication failed. Please check your API key in the .env file.');
        };

        // Handle window errors
        window.addEventListener('error', function(event) {
            console.error('❌ JavaScript error:', event.error);
            if (event.error && event.error.message.includes('Google Maps')) {
                showError('Google Maps API error: ' + event.error.message);
            }
        });

        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', function(event) {
            console.error('❌ Unhandled promise rejection:', event.reason);
            if (event.reason && event.reason.message && event.reason.message.includes('Google Maps')) {
                showError('Google Maps API error: ' + event.reason.message);
            }
        });

        // Filter properties based on current filter values
        function filterProperties() {
            const location = document.getElementById('locationFilter').value;
            const propertyType = document.getElementById('propertyTypeFilter').value;
            const agentElement = document.getElementById('agentFilter');
            const agent = agentElement ? agentElement.value : '';
            const minPrice = parseFloat(document.getElementById('minPriceFilter').value) || 0;
            const maxPrice = parseFloat(document.getElementById('maxPriceFilter').value) || Infinity;
            const couplesAllowed = document.getElementById('couplesAllowedFilter').value;

            filteredProperties = properties.filter(property => {
                // Location filter
                if (location && property.location !== location) {
                    return false;
                }

                // Property type filter
                if (propertyType && property.property_type !== propertyType) {
                    return false;
                }

                // Agent filter
                if (agent && property.agent_name !== agent) {
                    return false;
                }

                // Handle "Others" category filter
                if (showOthersOnly) {
                    const hasAgent = property.agent_name && 
                                   property.agent_name !== 'N/A' && 
                                   property.agent_name !== '' && 
                                   property.agent_name !== null;
                    if (hasAgent) {
                        return false;
                    }
                }

                // Price filters
                const price = parseFloat(property.price) || 0;
                if (price < minPrice || price > maxPrice) {
                    return false;
                }

                // Couples allowed filter
                if (couplesAllowed) {
                    const propertyCouplesOk = property.couples_ok;
                    const propertyCouplesAllowed = property.couples_allowed;
                    
                    if (couplesAllowed === 'yes') {
                        // Check if couples are welcome
                        const isCouplesWelcome = (propertyCouplesOk && propertyCouplesOk.toString().toLowerCase().includes('yes')) ||
                                              (propertyCouplesAllowed && propertyCouplesAllowed.toString().toLowerCase().includes('yes')) ||
                                              (propertyCouplesAllowed && propertyCouplesAllowed.toString().toLowerCase().includes('welcome'));
                        if (!isCouplesWelcome) {
                            return false;
                        }
                    }
                    if (couplesAllowed === 'no') {
                        // Check if singles only
                        const isSinglesOnly = (propertyCouplesOk && propertyCouplesOk.toString().toLowerCase().includes('no')) ||
                                            (propertyCouplesAllowed && propertyCouplesAllowed.toString().toLowerCase().includes('no')) ||
                                            (propertyCouplesAllowed && propertyCouplesAllowed.toString().toLowerCase().includes('singles'));
                        if (!isSinglesOnly) {
                            return false;
                        }
                    }
                }

                return true;
            });

            console.log(`🔍 Filtered to ${filteredProperties.length} properties`);
            return filteredProperties;
        }

        // Apply filters and update map
        function applyFilters() {
            console.log('🔍 Applying filters...');
            
            const validProperties = filterProperties().filter(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                return !isNaN(lat) && !isNaN(lng) && 
                       lat >= -90 && lat <= 90 && 
                       lng >= -180 && lng <= 180;
            });

            console.log(`📍 Filtered to ${validProperties.length} valid properties`);

            // Update property count
            document.getElementById('propertyCount').textContent = 
                `${validProperties.length} properties (${properties.length} total)`;

            // Recreate markers with filtered data
            createMarkers(validProperties);
            

            // Fit map to filtered properties
            if (validProperties.length > 0) {
                fitMapToProperties(validProperties);
            }
        }

        // Clear all filters
        function clearFilters() {
            document.getElementById('locationFilter').value = '';
            document.getElementById('propertyTypeFilter').value = '';
            const agentFilter = document.getElementById('agentFilter');
            if (agentFilter) {
                agentFilter.value = '';
            }
            document.getElementById('minPriceFilter').value = '';
            document.getElementById('maxPriceFilter').value = '';
            document.getElementById('couplesAllowedFilter').value = '';
            
            // Reset flags
            showOthersOnly = false;
            
            // Reset to show all properties
            filteredProperties = properties;
            applyFilters();
        }

        // Toggle filters panel
        function toggleFiltersPanel() {
            const panel = document.getElementById('filtersPanel');
            const button = document.getElementById('togglePanelBtn');
            
            if (panel.classList.contains('panel-hidden')) {
                panel.classList.remove('panel-hidden');
                button.innerHTML = '<i class="fas fa-times mr-2"></i>Close';
            } else {
                panel.classList.add('panel-hidden');
                button.innerHTML = '<i class="fas fa-filter mr-2"></i>Filters';
            }
        }


        // Initialize filters from URL parameters
        function initializeFiltersFromURL() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('location')) {
                document.getElementById('locationFilter').value = urlParams.get('location');
            }
            if (urlParams.get('property_type')) {
                document.getElementById('propertyTypeFilter').value = urlParams.get('property_type');
            }
            if (urlParams.get('agent_name')) {
                const agentFilter = document.getElementById('agentFilter');
                if (agentFilter) {
                    agentFilter.value = urlParams.get('agent_name');
                }
            }
            if (urlParams.get('min_price')) {
                document.getElementById('minPriceFilter').value = urlParams.get('min_price');
            }
            if (urlParams.get('max_price')) {
                document.getElementById('maxPriceFilter').value = urlParams.get('max_price');
            }
            if (urlParams.get('couples_allowed')) {
                document.getElementById('couplesAllowedFilter').value = urlParams.get('couples_allowed');
            }
        }

        // Add event listeners for real-time filtering
        function addFilterEventListeners() {
            // Select filters with immediate effect
            const selectFilters = [
                'locationFilter',
                'propertyTypeFilter', 
                'couplesAllowedFilter'
            ];

            // Add agent filter only if it exists (when user is authenticated)
            const agentFilter = document.getElementById('agentFilter');
            if (agentFilter) {
                selectFilters.push('agentFilter');
            }

            selectFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', applyFilters);
                }
            });

            // Price filters with debounce
            let searchTimeout;
            const priceFilters = ['minPriceFilter', 'maxPriceFilter'];
            priceFilters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('input', function() {
                        clearTimeout(searchTimeout);
                        searchTimeout = setTimeout(() => {
                            applyFilters();
                        }, 500);
                    });
                }
            });
        }

        // Toggle mobile filters
        function toggleFilters() {
            const filtersContent = document.getElementById('filtersContent');
            const filterToggleText = document.getElementById('filterToggleText');
            const filterToggleIcon = document.getElementById('filterToggleIcon');
            
            if (filtersContent.classList.contains('show')) {
                filtersContent.classList.remove('show');
                filterToggleText.textContent = 'Show Filters';
                filterToggleIcon.classList.remove('fa-chevron-up');
                filterToggleIcon.classList.add('fa-chevron-down');
                // Add collapsed class to body for responsive map
                document.body.classList.add('filters-collapsed');
            } else {
                filtersContent.classList.add('show');
                filterToggleText.textContent = 'Hide Filters';
                filterToggleIcon.classList.remove('fa-chevron-down');
                filterToggleIcon.classList.add('fa-chevron-up');
                // Remove collapsed class from body
                document.body.classList.remove('filters-collapsed');
            }
            
            // Trigger map resize after transition
            setTimeout(() => {
                if (typeof google !== 'undefined' && google.maps) {
                    google.maps.event.trigger(map, 'resize');
                }
            }, 350);
        }
        

    </script>
</body>
</html>
