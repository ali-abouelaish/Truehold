<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>{{ $property->title ?: 'Property Details' }} - TRUEHOLD</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Clean Dark Mode - Simple and Effective */
        * {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        
        html, body {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
            min-height: 100vh;
        }
        
        .property-card, .image-gallery, .card {
            background-color: #2d2d2d !important;
            border: 1px solid #444444 !important;
            color: #ffffff !important;
        }
        
        .action-button, .success-button, .secondary-button {
            background-color: #3d3d3d !important;
            border: 1px solid #555555 !important;
            color: #ffffff !important;
        }
        
        .action-button:hover, .success-button:hover, .secondary-button:hover {
            background-color: #4d4d4d !important;
            border-color: #666666 !important;
        }
        
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #1a1a1a !important;
        }
        
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400, .text-gray-300, .text-gray-200, .text-gray-100, .text-gray-50 {
            color: #ffffff !important;
        }
        
        /* Ensure main container and all sections are dark */
        main, .min-h-screen, .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #1a1a1a !important;
        }
        
        /* Ensure all divs and sections are dark */
        div, section, article, aside, header, footer, nav {
            background-color: #1a1a1a !important;
        }
        
        /* Remove any remaining white backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #1a1a1a !important;
        }
        
        .action-button, .success-button, .secondary-button {
            background-color: #3d3d3d !important;
            border: 1px solid #555555 !important;
            color: #ffffff !important;
        }
        
        .action-button:hover, .success-button:hover, .secondary-button:hover {
            background-color: #4d4d4d !important;
            border-color: #666666 !important;
        }
        
        /* Remove all white backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #1a1a1a !important;
        }
        
        /* Override any remaining white elements */
        .bg-white, [class*="bg-white"], [class*="bg-gray-50"], [class*="bg-gray-100"], [class*="bg-gray-200"] {
            background-color: #343E4E !important;
        }
        
        /* Target specific elements that might be white */
        .property-header, .property-overview, .image-gallery, .property-description, .property-details, .contact-info, .location-info, .quick-actions {
            background-color: #343E4E !important;
        }
        
        /* Override any card backgrounds */
        .card, .glass-card, .property-card, .info-card, .detail-card, .feature-card, .location-card {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
        }
        
        /* Override header section specifically */
        .bg-white.border-b.border-gray-200.shadow-sm {
            background-color: #343E4E !important;
            border-color: #4b5563 !important;
        }
        
        /* Override any remaining white elements with high specificity */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Override flex containers */
        .flex, .flex-col, .sm\\:flex-row, .items-start, .sm\\:items-center, .justify-between, .space-y-3, .sm\\:space-y-0 {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white elements */
        .bg-white, [class*="bg-white"], [class*="bg-gray-50"], [class*="bg-gray-100"], [class*="bg-gray-200"] {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white elements */
        .bg-white, [class*="bg-white"], [class*="bg-gray-50"], [class*="bg-gray-100"], [class*="bg-gray-200"] {
            background-color: #343E4E !important;
        }
        
        html {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for any remaining white elements */
        div, section, article, aside, header, main, footer, nav, ul, ol, li, p, span, a, button, input, textarea, select, form, fieldset, legend, label, table, thead, tbody, tr, td, th, h1, h2, h3, h4, h5, h6 {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white elements with maximum specificity */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white elements */
        .bg-white, [class*="bg-white"], [class*="bg-gray-50"], [class*="bg-gray-100"], [class*="bg-gray-200"] {
            background-color: #343E4E !important;
        }
        
        /* Override any remaining white elements */
        .bg-white, [class*="bg-white"], [class*="bg-gray-50"], [class*="bg-gray-100"], [class*="bg-gray-200"] {
            background-color: #343E4E !important;
        }
        
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on min-h-screen with maximum specificity */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on min-h-screen with maximum specificity */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on min-h-screen with maximum specificity */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on min-h-screen with maximum specificity */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on min-h-screen with maximum specificity */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
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
        
        /* Dark mode overrides for all elements */
        .bg-white { background-color: #1f2937 !important; }
        .bg-gray-50 { background-color: #343E4E !important; }
        .bg-gray-100 { background-color: #1f2937 !important; }
        .bg-gray-200 { background-color: #374151 !important; }
        .bg-gray-300 { background-color: #4b5563 !important; }
        .bg-gray-400 { background-color: #6b7280 !important; }
        .bg-gray-500 { background-color: #9ca3af !important; }
        .bg-gray-600 { background-color: #d1d5db !important; }
        .bg-gray-700 { background-color: #f3f4f6 !important; }
        .bg-gray-800 { background-color: #f9fafb !important; }
        .bg-gray-900 { background-color: #ffffff !important; }
        
        .text-gray-900 { color: #f9fafb !important; }
        .text-gray-800 { color: #f3f4f6 !important; }
        .text-gray-700 { color: #d1d5db !important; }
        .text-gray-600 { color: #9ca3af !important; }
        .text-gray-500 { color: #6b7280 !important; }
        .text-gray-400 { color: #4b5563 !important; }
        .text-gray-300 { color: #374151 !important; }
        .text-gray-200 { color: #1f2937 !important; }
        .text-gray-100 { color: #111827 !important; }
        
        .border-gray-200 { border-color: #374151 !important; }
        .border-gray-300 { border-color: #4b5563 !important; }
        .border-gray-400 { border-color: #6b7280 !important; }
        .border-gray-500 { border-color: #9ca3af !important; }
        
        /* Form elements */
        input, select, textarea {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        
        input::placeholder, textarea::placeholder, select::placeholder {
            color: #9ca3af !important;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #fbbf24 !important;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1) !important;
        }
        
        /* Cards and containers */
        .card, .glass-card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #d1d5db !important;
        }
        
        /* Buttons */
        .btn, .button {
            background: linear-gradient(135deg, #374151, #4b5563) !important;
            border: 1px solid #6b7280 !important;
            color: #d1d5db !important;
        }
        
        .btn:hover, .button:hover {
            background: linear-gradient(135deg, #4b5563, #6b7280) !important;
            border-color: #fbbf24 !important;
            color: #f9fafb !important;
        }
        
        /* Links */
        a {
            color: #d1d5db !important;
        }
        
        a:hover {
            color: #fbbf24 !important;
        }
        
        /* Headers */
        h1, h2, h3, h4, h5, h6 {
            color: #f9fafb !important;
        }
        
        /* Text elements */
        p, div, span {
            color: #d1d5db !important;
        }
        
        small {
            color: #9ca3af !important;
        }
        
        strong {
            color: #ffffff !important;
        }
        
        /* Navigation */
        nav {
            background-color: #1f2937 !important;
            border-bottom-color: #374151 !important;
        }
        
        /* Footer */
        footer {
            background-color: #343E4E !important;
            border-top-color: #374151 !important;
        }
        
        /* Main content areas */
        .container, .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl {
            background-color: #343E4E !important;
        }
        
        /* Sections and divs */
        section, div, main, article, aside {
            background-color: transparent !important;
        }
        
        /* Override any remaining light backgrounds */
        [class*="bg-"]:not([class*="bg-gray-9"]):not([class*="bg-gray-8"]):not([class*="bg-gray-7"]) {
            background-color: #1f2937 !important;
        }
        
        /* Force dark background everywhere */
        * {
            background-color: inherit !important;
        }
        
        body, html, .min-h-screen, .bg-white, .bg-gray-50, .bg-gray-100 {
            background-color: #343E4E !important;
        }
        
        /* Override any white or light backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200 {
            background-color: #1f2937 !important;
        }
        
        /* Ensure all main elements have dark background */
        main, section, article, aside, div:not([class*="bg-"]) {
            background-color: transparent !important;
        }
        
        /* Override any remaining white backgrounds */
        [style*="background-color: white"], [style*="background-color: #fff"], [style*="background-color: #ffffff"] {
            background-color: #343E4E !important;
        }
        
        .property-card {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(251, 191, 36, 0.12), 0 4px 16px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(251, 191, 36, 0.4);
            color: #d1d5db;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        
        .property-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 48px rgba(251, 191, 36, 0.25), 0 8px 24px rgba(0, 0, 0, 0.4);
            border-color: #fbbf24;
        }
        
        /* All cards and containers dark mode with gold accents */
        .card, .glass-card, .property-card, .info-card, .detail-card, .feature-card {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
            border: 2px solid rgba(251, 191, 36, 0.4) !important;
            color: #d1d5db !important;
            box-shadow: 0 8px 32px rgba(251, 191, 36, 0.12), 0 4px 16px rgba(0, 0, 0, 0.3) !important;
            border-radius: 12px !important;
        }
        
        .card:hover, .glass-card:hover, .property-card:hover, .info-card:hover, .detail-card:hover, .feature-card:hover {
            border-color: #fbbf24 !important;
            box-shadow: 0 12px 48px rgba(251, 191, 36, 0.25), 0 8px 24px rgba(0, 0, 0, 0.4) !important;
            transform: translateY(-2px) !important;
        }
        
        /* Container backgrounds with gold accents */
        .container, .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl, .max-w-3xl, .max-w-2xl {
            background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%) !important;
            border: 2px solid rgba(251, 191, 36, 0.5) !important;
            box-shadow: 0 8px 32px rgba(251, 191, 36, 0.15), 0 4px 16px rgba(0, 0, 0, 0.3) !important;
            border-radius: 12px !important;
        }
        
        .container:hover, .max-w-7xl:hover, .max-w-6xl:hover, .max-w-5xl:hover, .max-w-4xl:hover, .max-w-3xl:hover, .max-w-2xl:hover {
            border-color: #fbbf24 !important;
            box-shadow: 0 12px 48px rgba(251, 191, 36, 0.25), 0 8px 24px rgba(0, 0, 0, 0.4) !important;
        }
        
        /* Section backgrounds */
        section, .section, .content-section, .main-section {
            background-color: transparent !important;
        }
        
        /* Div backgrounds */
        div:not([class*="bg-"]) {
            background-color: transparent !important;
        }
        
        .feature-badge {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .feature-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        
        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .status-available {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        
        .status-rented {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }
        
        .status-unavailable {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .status-on_hold {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .price-display {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .price-display::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .action-button {
            background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
            color: #d1d5db;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.1), 0 2px 8px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(251, 191, 36, 0.3);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .action-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(251, 191, 36, 0.2), 0 4px 12px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-color: #fbbf24;
            color: #1f2937;
        }
        
        .secondary-button {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            color: #9ca3af;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .secondary-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-color: #d1d5db;
            color: #1f2937;
        }
        
        .success-button {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2), 0 2px 8px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(16, 185, 129, 0.4);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .success-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3), 0 4px 12px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-color: #10b981;
            color: #ffffff;
        }
        
        .image-gallery {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.3);
            border: 2px solid rgba(251, 191, 36, 0.4);
        }
        
        /* Gallery header dark mode */
        .image-gallery h2 {
            color: #d1d5db !important;
        }
        
        .image-gallery .bg-blue-100 {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        
        .image-gallery .text-blue-600 {
            color: #fbbf24 !important;
        }
        
        /* Gallery counter dark mode */
        .image-gallery .bg-white {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
        }
        
        .image-gallery .text-gray-700 {
            color: #d1d5db !important;
        }
        
        /* Thumbnail grid dark mode */
        .thumbnail-grid {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
            border-radius: 12px;
            padding: 1rem;
        }
        
        .thumbnail {
            background-color: #374151 !important;
            border: 1px solid #4b5563 !important;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .thumbnail:hover {
            border-color: #fbbf24 !important;
            transform: translateY(-2px);
        }
        
        .thumbnail.active {
            border-color: #fbbf24 !important;
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
        }
        
        /* Gallery navigation buttons dark mode */
        .gallery-nav-button {
            background-color: #1f2937 !important;
            border: 1px solid #374151 !important;
            color: #d1d5db !important;
        }
        
        .gallery-nav-button:hover {
            background-color: #374151 !important;
            border-color: #fbbf24 !important;
            color: #fbbf24 !important;
        }
        
        /* Remove all icon backgrounds and text highlights */
        .bg-blue-100, .bg-green-100, .bg-orange-100, .bg-indigo-100, .bg-purple-100, .bg-pink-100, .bg-red-100, .bg-yellow-100 {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        
        /* Remove text highlights */
        .text-blue-600, .text-green-600, .text-orange-600, .text-indigo-600, .text-purple-600, .text-pink-600, .text-red-600, .text-yellow-600 {
            color: #d1d5db !important;
        }
        
        /* Remove all background highlights */
        .bg-blue-50, .bg-green-50, .bg-orange-50, .bg-indigo-50, .bg-purple-50, .bg-pink-50, .bg-red-50, .bg-yellow-50 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds */
        .bg-blue-200, .bg-green-200, .bg-orange-200, .bg-indigo-200, .bg-purple-200, .bg-pink-200, .bg-red-200, .bg-yellow-200 {
            background-color: transparent !important;
        }
        
        /* Remove all colored borders */
        .border-blue-200, .border-green-200, .border-orange-200, .border-indigo-200, .border-purple-200, .border-pink-200, .border-red-200, .border-yellow-200 {
            border-color: transparent !important;
        }
        
        /* Remove all colored text */
        .text-blue-500, .text-green-500, .text-orange-500, .text-indigo-500, .text-purple-500, .text-pink-500, .text-red-500, .text-yellow-500 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored backgrounds with higher specificity */
        .bg-blue-300, .bg-green-300, .bg-orange-300, .bg-indigo-300, .bg-purple-300, .bg-pink-300, .bg-red-300, .bg-yellow-300 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with even higher specificity */
        .bg-blue-400, .bg-green-400, .bg-orange-400, .bg-indigo-400, .bg-purple-400, .bg-pink-400, .bg-red-400, .bg-yellow-400 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with maximum specificity */
        .bg-blue-500, .bg-green-500, .bg-orange-500, .bg-indigo-500, .bg-purple-500, .bg-pink-500, .bg-red-500, .bg-yellow-500 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with maximum specificity */
        .bg-blue-600, .bg-green-600, .bg-orange-600, .bg-indigo-600, .bg-purple-600, .bg-pink-600, .bg-red-600, .bg-yellow-600 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with maximum specificity */
        .bg-blue-700, .bg-green-700, .bg-orange-700, .bg-indigo-700, .bg-purple-700, .bg-pink-700, .bg-red-700, .bg-yellow-700 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with maximum specificity */
        .bg-blue-800, .bg-green-800, .bg-orange-800, .bg-indigo-800, .bg-purple-800, .bg-pink-800, .bg-red-800, .bg-yellow-800 {
            background-color: transparent !important;
        }
        
        /* Remove all colored backgrounds with maximum specificity */
        .bg-blue-900, .bg-green-900, .bg-orange-900, .bg-indigo-900, .bg-purple-900, .bg-pink-900, .bg-red-900, .bg-yellow-900 {
            background-color: transparent !important;
        }
        
        /* Remove faint grey highlights from specific sections */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from cards and containers */
        .property-card, .info-card, .detail-card, .feature-card, .location-card {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%) !important;
        }
        
        /* Remove grey highlights from form elements */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from specific elements */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove all faint grey highlights from all sections */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Remove grey highlights from all possible grey backgrounds */
        .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500 {
            background-color: transparent !important;
        }
        
        /* Force dark background on main container and all its children */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on all container elements */
        .container, .max-w-7xl, .max-w-6xl, .max-w-5xl, .max-w-4xl, .max-w-3xl, .max-w-2xl {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on all main content areas */
        main, section, article, aside, header, footer, nav, div {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on all possible white elements */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on all possible white elements */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on all possible white elements */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        .main-image-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }
        
        /* Ultra-aggressive overrides for main container background */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for main container background */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for main container background */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for main container background */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for main container background */
        .max-w-7xl, .mx-auto, .px-4, .sm\\:px-6, .lg\\:px-8, .py-4, .sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Remove all text backgrounds */
        span, p, h1, h2, h3, h4, h5, h6, div, a, button, label, strong, em, small, b, i, u, mark, code, pre, blockquote, cite, abbr, acronym, address, del, ins, s, strike, sub, sup, tt, var, kbd, samp, dfn, q, s, u, mark, code, pre, blockquote, cite, abbr, acronym, address, del, ins, s, strike, sub, sup, tt, var, kbd, samp, dfn, q {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with higher specificity */
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400, .text-gray-300, .text-gray-200, .text-gray-100, .text-gray-50 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-600, .text-green-600, .text-orange-600, .text-indigo-600, .text-purple-600, .text-pink-600, .text-red-600, .text-yellow-600 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-500, .text-green-500, .text-orange-500, .text-indigo-500, .text-purple-500, .text-pink-500, .text-red-500, .text-yellow-500 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-400, .text-green-400, .text-orange-400, .text-indigo-400, .text-purple-400, .text-pink-400, .text-red-400, .text-yellow-400 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-300, .text-green-300, .text-orange-300, .text-indigo-300, .text-purple-300, .text-pink-300, .text-red-300, .text-yellow-300 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-200, .text-green-200, .text-orange-200, .text-indigo-200, .text-purple-200, .text-pink-200, .text-red-200, .text-yellow-200 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove all text backgrounds with maximum specificity */
        .text-blue-100, .text-green-100, .text-orange-100, .text-indigo-100, .text-purple-100, .text-pink-100, .text-red-100, .text-yellow-100 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .gallery-nav-button {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Ultra-aggressive overrides for min-h-screen */
        .min-h-screen {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on ALL elements with maximum specificity */
        *, *::before, *::after {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on ALL elements with maximum specificity */
        *, *::before, *::after {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on ALL elements with maximum specificity */
        *, *::before, *::after {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on ALL elements with maximum specificity */
        *, *::before, *::after {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on ALL elements with maximum specificity */
        *, *::before, *::after {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on body and html specifically */
        html, body {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on body and html specifically */
        html, body {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on body and html specifically */
        html, body {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on body and html specifically */
        html, body {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on body and html specifically */
        html, body {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on main container specifically */
        main.max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-4.sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on main container specifically */
        main.max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-4.sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on main container specifically */
        main.max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-4.sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on main container specifically */
        main.max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-4.sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        /* Force dark background on main container specifically */
        main.max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-4.sm\\:py-8 {
            background-color: #343E4E !important;
        }
        
        .gallery-nav-button:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.1);
        }
        
        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        
        .thumbnail {
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        
        .thumbnail:hover {
            transform: scale(1.05);
            border-color: #3b82f6;
        }
        
        .thumbnail.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
        
        .info-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .info-value {
            color: #6b7280;
            line-height: 1.6;
        }
        
        .section-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
            margin: 2rem 0;
        }
        
        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 1000;
        }
        
        .modal-overlay {
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .description-text {
            white-space: pre-wrap;
            word-wrap: break-word;
            line-height: 1.8;
            color: #374151;
        }
        
        .success-message {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 1px solid #10b981;
            color: #065f46;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
            margin-bottom: 2rem;
        }
        
        .location-card {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 1px solid #f59e0b;
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.2);
        }
        
        .maps-button {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1e3a8a;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);
            text-decoration: none;
            display: block;
            text-align: center;
            border: 1px solid #bfdbfe;
        }
        
        .maps-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-color: #93c5fd;
            color: #1e40af;
        }
        
        .responsive-grid {
            display: grid;
            gap: 2rem;
        }
        
        @media (min-width: 1024px) {
            .responsive-grid {
                grid-template-columns: 2fr 1fr;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-up {
            animation: slideUp 0.8s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Mobile-first responsive design */
        @media (max-width: 767px) {
            .property-card {
                border-radius: 16px;
                margin-bottom: 1rem;
            }
            
            .property-card .p-8 {
                padding: 1.5rem;
            }
            
            .price-display {
                padding: 1.5rem;
                border-radius: 16px;
            }
            
            .price-display .text-6xl {
                font-size: 2.5rem;
            }
            
            .feature-badge, .status-badge {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }
            
            .action-button, .secondary-button, .success-button {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                min-height: 44px;
                width: 100%;
                justify-content: center;
            }
            
            .grid.grid-cols-1.lg\\:grid-cols-3 {
                gap: 1.5rem;
            }
            
            .space-y-6 > * + * {
                margin-top: 1.5rem;
            }
        }
        
        /* Small screens */
        @media (max-width: 640px) {
            .property-card .p-8 {
                padding: 1rem;
            }
            
            .price-display {
                padding: 1rem;
            }
            
            .price-display .text-6xl {
                font-size: 2rem;
            }
            
            .feature-badge, .status-badge {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
            }
            
            .text-2xl {
                font-size: 1.25rem;
            }
            
            .text-3xl {
                font-size: 1.5rem;
            }
        }
        
        /* Very small screens */
        @media (max-width: 375px) {
            .property-card .p-8 {
                padding: 0.75rem;
            }
            
            .price-display {
                padding: 0.75rem;
            }
            
            .price-display .text-6xl {
                font-size: 1.75rem;
            }
        }
        
        /* Touch-friendly improvements */
        @media (max-width: 767px) {
            button, select, input, a {
                min-height: 44px;
                min-width: 44px;
            }
            
            .property-card {
                -webkit-tap-highlight-color: transparent;
            }
            
            .property-card:active {
                transform: scale(0.98);
            }
            
            .grid.gap-8 {
                gap: 1.5rem;
            }
        }
        
        /* High DPI displays */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .property-image {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }
        
        /* Ultra-aggressive overrides for any remaining white backgrounds */
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-gray-200, .bg-gray-300, .bg-gray-400, .bg-gray-500, .bg-gray-600, .bg-gray-700, .bg-gray-800, .bg-gray-900 {
            background-color: #343E4E !important;
        }
        
        /* Override any inline styles that might be white */
        [style*="background-color: white"], [style*="background-color: #fff"], [style*="background-color: #ffffff"], [style*="background-color: #f9fafb"], [style*="background-color: #f8fafc"] {
            background-color: #343E4E !important;
        }
        
        /* Force dark on all possible elements */
        html, body, div, section, main, article, aside, header, footer, nav, ul, ol, li, p, span, a, button, input, textarea, select, form, fieldset, legend, label, table, thead, tbody, tr, td, th, h1, h2, h3, h4, h5, h6 {
            background-color: #343E4E !important;
        }
        
        /* Remove ALL text backgrounds including icons and numbers */
        span, p, h1, h2, h3, h4, h5, h6, div, a, button, label, strong, em, small, b, i, u, mark, code, pre, blockquote, cite, abbr, acronym, address, del, ins, s, strike, sub, sup, tt, var, kbd, samp, dfn, q, s, u, mark, code, pre, blockquote, cite, abbr, acronym, address, del, ins, s, strike, sub, sup, tt, var, kbd, samp, dfn, q {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove text backgrounds from all Tailwind text classes */
        .text-gray-900, .text-gray-800, .text-gray-700, .text-gray-600, .text-gray-500, .text-gray-400, .text-gray-300, .text-gray-200, .text-gray-100, .text-gray-50 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove text backgrounds from colored text classes */
        .text-blue-600, .text-green-600, .text-orange-600, .text-indigo-600, .text-purple-600, .text-pink-600, .text-red-600, .text-yellow-600 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .text-blue-500, .text-green-500, .text-orange-500, .text-indigo-500, .text-purple-500, .text-pink-500, .text-red-500, .text-yellow-500 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .text-blue-400, .text-green-400, .text-orange-400, .text-indigo-400, .text-purple-400, .text-pink-400, .text-red-400, .text-yellow-400 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .text-blue-300, .text-green-300, .text-orange-300, .text-indigo-300, .text-purple-300, .text-pink-300, .text-red-300, .text-yellow-300 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .text-blue-200, .text-green-200, .text-orange-200, .text-indigo-200, .text-purple-200, .text-pink-200, .text-red-200, .text-yellow-200 {
            background-color: transparent !important;
            background: none !important;
        }
        
        .text-blue-100, .text-green-100, .text-orange-100, .text-indigo-100, .text-purple-100, .text-pink-100, .text-red-100, .text-yellow-100 {
            background-color: transparent !important;
            background: none !important;
        }
        
        /* Remove backgrounds from all icon containers */
        .bg-blue-100, .bg-green-100, .bg-orange-100, .bg-indigo-100, .bg-purple-100, .bg-pink-100, .bg-red-100, .bg-yellow-100 {
            background-color: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        
        /* Remove backgrounds from all colored backgrounds */
        .bg-blue-50, .bg-green-50, .bg-orange-50, .bg-indigo-50, .bg-purple-50, .bg-pink-50, .bg-red-50, .bg-yellow-50 {
            background-color: transparent !important;
        }
        
        .bg-blue-200, .bg-green-200, .bg-orange-200, .bg-indigo-200, .bg-purple-200, .bg-pink-200, .bg-red-200, .bg-yellow-200 {
            background-color: transparent !important;
        }
        
        .bg-blue-300, .bg-green-300, .bg-orange-300, .bg-indigo-300, .bg-purple-300, .bg-pink-300, .bg-red-300, .bg-yellow-300 {
            background-color: transparent !important;
        }
        
        .bg-blue-400, .bg-green-400, .bg-orange-400, .bg-indigo-400, .bg-purple-400, .bg-pink-400, .bg-red-400, .bg-yellow-400 {
            background-color: transparent !important;
        }
        
        .bg-blue-500, .bg-green-500, .bg-orange-500, .bg-indigo-500, .bg-purple-500, .bg-pink-500, .bg-red-500, .bg-yellow-500 {
            background-color: transparent !important;
        }
        
        .bg-blue-600, .bg-green-600, .bg-orange-600, .bg-indigo-600, .bg-purple-600, .bg-pink-600, .bg-red-600, .bg-yellow-600 {
            background-color: transparent !important;
        }
        
        .bg-blue-700, .bg-green-700, .bg-orange-700, .bg-indigo-700, .bg-purple-700, .bg-pink-700, .bg-red-700, .bg-yellow-700 {
            background-color: transparent !important;
        }
        
        .bg-blue-800, .bg-green-800, .bg-orange-800, .bg-indigo-800, .bg-purple-800, .bg-pink-800, .bg-red-800, .bg-yellow-800 {
            background-color: transparent !important;
        }
        
        .bg-blue-900, .bg-green-900, .bg-orange-900, .bg-indigo-900, .bg-purple-900, .bg-pink-900, .bg-red-900, .bg-yellow-900 {
            background-color: transparent !important;
        }
        
        /* Remove all borders from colored elements */
        .border-blue-200, .border-green-200, .border-orange-200, .border-indigo-200, .border-purple-200, .border-pink-200, .border-red-200, .border-yellow-200 {
            border-color: transparent !important;
        }
        
        /* Remove all colored text */
        .text-blue-500, .text-green-500, .text-orange-500, .text-indigo-500, .text-purple-500, .text-pink-500, .text-red-500, .text-yellow-500 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with higher specificity */
        .text-blue-600, .text-green-600, .text-orange-600, .text-indigo-600, .text-purple-600, .text-pink-600, .text-red-600, .text-yellow-600 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-700, .text-green-700, .text-orange-700, .text-indigo-700, .text-purple-700, .text-pink-700, .text-red-700, .text-yellow-700 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-800, .text-green-800, .text-orange-800, .text-indigo-800, .text-purple-800, .text-pink-800, .text-red-800, .text-yellow-800 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-900, .text-green-900, .text-orange-900, .text-indigo-900, .text-purple-900, .text-pink-900, .text-red-900, .text-yellow-900 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-100, .text-green-100, .text-orange-100, .text-indigo-100, .text-purple-100, .text-pink-100, .text-red-100, .text-yellow-100 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-200, .text-green-200, .text-orange-200, .text-indigo-200, .text-purple-200, .text-pink-200, .text-red-200, .text-yellow-200 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-300, .text-green-300, .text-orange-300, .text-indigo-300, .text-purple-300, .text-pink-300, .text-red-300, .text-yellow-300 {
            color: #d1d5db !important;
        }
        
        /* Remove all colored text with maximum specificity */
        .text-blue-400, .text-green-400, .text-orange-400, .text-indigo-400, .text-purple-400, .text-pink-400, .text-red-400, .text-yellow-400 {
            color: #d1d5db !important;
        }
    </style>
</head>
<body class="min-h-screen" style="background-color: #343E4E !important;">
    @include('layouts.properties-navigation')
    <div class="min-h-screen">


        <!-- Property Header Section -->
        <div class="border-b border-gray-200 shadow-sm" style="background-color: #1f2937 !important; border-color: #374151 !important;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-3 sm:space-y-0">
                    <div class="flex items-center space-x-3 sm:space-x-4">
                        <a href="{{ route('properties.index') }}" 
                           class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                            <i class="fas fa-arrow-left text-lg sm:text-xl"></i>
                        </a>
                        <div class="hidden sm:block h-8 w-px bg-gray-300"></div>
                        <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900">
                            {{ Str::limit($property->title ?: 'Property Details', 50) }}
                        </h1>
                    </div>
                    @if($property->status)
                        <div class="status-badge status-{{ $property->status === 'available' ? 'available' : 'rented' }} text-sm sm:text-base">
                            <i class="fas fa-{{ $property->status === 'available' ? 'check-circle' : 'clock' }} mr-2"></i>
                            {{ ucfirst($property->status) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
            @if(session('success'))
                <div class="success-message animate-fade-in">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-check-circle text-xl sm:text-2xl"></i>
                        <span class="text-base sm:text-lg font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Property Overview Card -->
            <div class="property-card p-4 sm:p-6 lg:p-8 mb-6 sm:mb-8 animate-slide-up">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                    <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <i class="fas fa-map-marker-alt text-red-500 text-xl sm:text-2xl"></i>
                            @if($property->latitude && $property->longitude && $property->latitude !== 'N/A' && $property->longitude !== 'N/A')
                                <a href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}" 
                                   target="_blank" 
                                   class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-700 hover:text-blue-600 transition-colors duration-300 hover:underline">
                                    {{ $property->location ?: 'Location not specified' }}
                                    <i class="fas fa-external-link-alt ml-2 sm:ml-3 text-base sm:text-lg opacity-75"></i>
                                </a>
                            @else
                                <span class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-700">{{ $property->location ?: 'Location not specified' }}</span>
                            @endif
                        </div>
                        
                        @if($property->property_type)
                            <div class="feature-badge inline-block text-sm sm:text-base">
                                <i class="fas fa-home mr-2"></i>
                                {{ $property->property_type }}
                            </div>
                        @endif
                        
                        @auth
                        <div class="pt-4">
                            @if($property instanceof \App\Models\PropertyFromSheet)
                                <span class="text-sm text-gray-500">Properties from Google Sheets cannot be edited through the admin panel.</span>
                            @else
                                <a href="{{ route('admin.properties.edit', $property->id) }}" class="action-button w-full sm:w-auto">
                                    <i class="fas fa-edit"></i>
                                    Edit Property
                                </a>
                            @endif
                        </div>
                        @endauth
                    </div>
                    
                    <div class="space-y-4 sm:space-y-6">
                        <div class="price-display">
                            <div class="text-4xl sm:text-5xl lg:text-6xl font-bold mb-2 leading-none">
                                {{ $property->formatted_price }}
                            </div>
                        </div>
                        
                        @if($property->available_date && $property->available_date !== 'N/A')
                            <div class="bg-green-100 text-green-800 px-4 sm:px-6 py-3 sm:py-4 rounded-12 sm:rounded-16 font-semibold text-center shadow-lg text-sm sm:text-base">
                                <i class="fas fa-calendar mr-2"></i>
                                Available: {{ $property->available_date }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            <div class="responsive-grid">
                <!-- Main Content -->
                <div class="space-y-8">
                    <!-- Enhanced Image Gallery Carousel -->
                    @if($property->high_quality_photos_array && count($property->high_quality_photos_array) > 0)
                        <div class="image-gallery" data-photos="{{ json_encode($property->high_quality_photos_array ?? []) }}" data-original-photos="{{ json_encode($property->all_photos_array ?? []) }}">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-3xl font-bold text-gray-900 flex items-center space-x-3">
                                    <div class="bg-blue-100 p-3 rounded-full">
                                        <i class="fas fa-images text-blue-600 text-2xl"></i>
                                    </div>
                                    <span>Property Gallery</span>
                                </h2>
                                <div class="bg-white px-4 py-2 rounded-full shadow-lg">
                                    <span class="text-gray-700 font-semibold">
                                        <span id="currentImage">1</span> of {{ count($property->high_quality_photos_array) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Enhanced Main Image Display -->
                            <div class="main-image-container mb-6">
                                <div class="aspect-w-16 aspect-h-9">
                                    <img id="mainImage" src="{{ $property->high_quality_photos_array[0] }}" 
                                         alt="Property photo" 
                                         class="w-full h-[500px] object-cover"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgdmlld0JveD0iMCAwIDQwMCA0MDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0MDAiIGhlaWdodD0iNDAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0yMDAgMTUwQzE3NS4xNDcgMTUwIDE1NSAxNzAuMTQ3IDE1NSAxOTVDMTU1IDIxOS44NTMgMTc1LjE0NyAyNDAgMjAwIDI0MEMyMjQuODUzIDI0MCAyNDUgMjE5Ljg1MyAyNDUgMTk1QzI0NSAxNzAuMTQ3IDIyNC44NTMgMTUwIDIwMCAxNTBaIiBmaWxsPSIjOUI5QkEwIi8+CjxwYXRoIGQ9Ik0yMDAgMjgwQzE2NS40OSAyODAgMTM1IDI5MC40OSAxMzUgMzA1VjM1MEgyNjVWMzA1QzI2NSAyOTAuNDkgMjM0LjUxIDI4MCAyMDAgMjgwWiIgZmlsbD0iIzlCOUJBMCIvPgo8L3N2Zz4K'">
                                </div>
                                
                                <!-- Enhanced Navigation Arrows -->
                                <button onclick="previousImage()" class="gallery-nav-button absolute left-6 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-chevron-left text-xl"></i>
                                </button>
                                <button onclick="nextImage()" class="gallery-nav-button absolute right-6 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-chevron-right text-xl"></i>
                                </button>
                                
                                <!-- Enhanced Fullscreen Button -->
                                <button onclick="openFullscreen()" class="gallery-nav-button absolute top-6 right-6">
                                    <i class="fas fa-expand text-xl"></i>
                                </button>
                            </div>
                            
                            <!-- Enhanced Thumbnail Navigation -->
                            <div class="thumbnail-grid">
                                @foreach($property->high_quality_photos_array as $index => $photoUrl)
                                    <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="showImage({{ $index }})">
                                        <img src="{{ $photoUrl }}" 
                                             alt="Thumbnail {{ $index + 1 }}" 
                                             class="w-full h-20 object-cover"
                                             id="thumb{{ $index }}"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik00MCAzMEMzNS4wMjkgMzAgMzEgMzQuMDI5IDMxIDM5QzMxIDQzLjk3MSAzNS4wMjkgNDggNDAgNDhDNDQuOTcxIDQ4IDQ5IDQzLjk3MSA0OSAzOUM0OSAzNC4wMjkgNDQuOTcxIDMwIDQwIDMwWiIgZmlsbD0iIzlCOUJBMCIvPgo8cGF0aCBkPSJNNDAgNTZDMzIuOTggNTYgMjcgNjAuOTggMjcgNjZWNzBINjNWNjZDNjMgNjAuOTggNTcuMDIgNTYgNDAgNTZaIiBmaWxsPSIjOUI5QkEwIi8+Cjwvc3ZnPgo='">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Property Description -->
                    @if($property->description && $property->description !== 'N/A')
                        <div class="property-card p-10">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                                </div>
                                <span>Description</span>
                            </h2>
                            <div class="prose max-w-none">
                                <div class="description-text text-lg">
                                    {!! nl2br(e($property->description)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Detailed Property Information -->
                    <div class="property-card p-10">
                        <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center space-x-3">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-list-ul text-blue-600 text-2xl"></i>
                            </div>
                            <span>Property Details</span>
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Basic Details -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-900 border-b-2 border-blue-200 pb-3">Basic Information</h3>
                                
                                @if($property->amenities && $property->amenities !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Amenities</span>
                                        <p class="info-value">{{ $property->amenities }}</p>
                                    </div>
                                @endif
                                
                                @if($property->bills_included && $property->bills_included !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Bills Included</span>
                                        <p class="info-value">{{ $property->bills_included }}</p>
                                    </div>
                                @endif
                                
                                @if($property->deposit && $property->deposit !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Deposit</span>
                                        <p class="info-value">{{ $property->deposit }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Additional Details -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-bold text-gray-900 border-b-2 border-blue-200 pb-3">Additional Features</h3>
                                
                                @if($property->minimum_term && $property->minimum_term !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Minimum Term</span>
                                        <p class="info-value">{{ $property->minimum_term }}</p>
                                    </div>
                                @endif
                                
                                @if($property->furnishings && $property->furnishings !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Furnishings</span>
                                        <p class="info-value">{{ $property->furnishings }}</p>
                                    </div>
                                @endif
                                
                                @if($property->garden_patio && $property->garden_patio !== 'N/A')
                                    <div class="info-section">
                                        <span class="info-label">Garden/Patio</span>
                                        <p class="info-value">{{ $property->garden_patio }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Sidebar -->
                <div class="space-y-8">
                    <!-- Contact Information -->
                    @if($property->contact_info && $property->contact_info !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-phone text-green-600 text-xl"></i>
                                </div>
                                <span>Contact Information</span>
                            </h2>
                            <div class="bg-green-50 p-4 rounded-12 border border-green-200">
                                <p class="text-gray-700 font-medium">{{ $property->contact_info }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Management Company -->
                    @if($property->management_company && $property->management_company !== 'N/A' && auth()->check())
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-building text-purple-600 text-xl"></i>
                                </div>
                                <span>Management Company</span>
                            </h2>
                            <div class="bg-purple-50 p-4 rounded-12 border border-purple-200">
                                <p class="text-gray-700 font-medium">{{ $property->management_company }}</p>
                            </div>
                        </div>
                    @elseif($property->management_company && $property->management_company !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-building text-purple-600 text-xl"></i>
                                </div>
                                <span>Management Company</span>
                            </h2>
                            <div class="bg-gray-100 border border-gray-300 text-gray-600 px-6 py-4 rounded-lg text-center">
                                <i class="fas fa-lock text-2xl mb-3"></i>
                                <p class="mb-2">Management company information is available to registered users.</p>
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login to view company details</a>
                            </div>
                        </div>
                    @endif

                    <!-- Property Manager -->
                    @if($property->agent_name && $property->agent_name !== 'N/A' && auth()->check())
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                                </div>
                                <span>Property Manager</span>
                            </h2>
                            <div class="bg-blue-50 p-4 rounded-12 border border-blue-200">
                                <p class="text-gray-700 font-medium">
                                    {{ $property->agent_name }}
                                    @if($property->paying && in_array(strtolower(trim($property->paying)), ['yes', 'y', '1', 'true']))
                                        <span class="ml-2">⚡</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    @elseif($property->agent_name && $property->agent_name !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                                </div>
                                <span>Property Manager</span>
                            </h2>
                            <div class="bg-gray-100 border border-gray-300 text-gray-600 px-6 py-4 rounded-lg text-center">
                                <i class="fas fa-lock text-2xl mb-3"></i>
                                <p class="mb-2">Property manager information is available to registered users.</p>
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login to view manager details</a>
                            </div>
                        </div>
                    @endif

                    <!-- Enhanced Location Information -->
                    @if($property->latitude && $property->longitude && $property->latitude !== 'N/A' && $property->longitude !== 'N/A')
                        <div class="property-card p-8">
                            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                                </div>
                                <span>Location</span>
                            </h2>
                            <div class="location-card mb-6">
                                <i class="fas fa-map-marker-alt text-5xl text-orange-500 mb-4"></i>
                                <p class="text-gray-700 font-semibold mb-2">Coordinates:</p>
                                <p class="text-lg text-gray-600 font-mono">{{ $property->latitude }}, {{ $property->longitude }}</p>
                            </div>
                            <a href="https://www.google.com/maps?q={{ $property->latitude }},{{ $property->longitude }}" 
                               target="_blank" 
                               class="maps-button">
                                <i class="fas fa-map-marked-alt mr-2"></i>Open in Google Maps
                            </a>
                        </div>
                    @endif

                    <!-- Mark Client as Interested -->
                    @auth
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <span>Mark Client as Interested</span>
                        </h2>
                        <form method="POST" action="{{ route('admin.properties.interests.add', $property) }}" class="grid grid-cols-1 gap-4">
                            @csrf
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-2">Select Client (A→Z)</label>
                                <select id="client_id" name="client_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choose a client</option>
                                    @foreach($clients ?? [] as $client)
                                        <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>
                                <textarea id="notes" name="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Availability, preferences, etc."></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                    <i class="fas fa-user-plus mr-2"></i>Add Interested Client
                                </button>
                            </div>
                        </form>
                    </div>
                    @endauth

                    <!-- Interested Clients -->
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <i class="fas fa-user-friends text-indigo-600 text-xl"></i>
                            </div>
                            <span>Interested Clients</span>
                        </h2>
                        
                        @auth
                        <!-- Full client details for authenticated users -->
                        <ul class="divide-y divide-gray-200">
                            @forelse($property->interestedClients ?? [] as $client)
                                <li class="py-3 flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $client->full_name }}</p>
                                        <p class="text-sm text-gray-600">{{ $client->email }} @if($client->phone_number) • {{ $client->phone_number }} @endif</p>
                                        @if($client->pivot && $client->pivot->created_at)
                                            <p class="text-xs text-gray-400 mt-1">Added {{ $client->pivot->created_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                    <form method="POST" action="{{ route('admin.properties.interests.remove', [$property, $client]) }}" onsubmit="return confirm('Remove this client from interested list?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:text-red-700">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </li>
                            @empty
                                <li class="py-4 text-center text-gray-500">
                                    <i class="fas fa-users text-2xl mb-2"></i>
                                    <p>No interested clients yet</p>
                                </li>
                            @endforelse
                        </ul>
                        @else
                        <!-- Privacy-protected view for non-authenticated users -->
                        <div class="text-center py-8">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                <i class="fas fa-lock text-blue-600 text-3xl mb-4"></i>
                                <h3 class="text-lg font-semibold text-blue-800 mb-2">Client Privacy Protected</h3>
                                <p class="text-blue-600 mb-4">
                                    {{ $property->interestedClients->count() }} client{{ $property->interestedClients->count() !== 1 ? 's' : '' }} 
                                    {{ $property->interestedClients->count() === 1 ? 'is' : 'are' }} interested in this property
                                </p>
                                <p class="text-sm text-blue-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Full client details are only visible to authenticated users
                                </p>
                            </div>
                        </div>
                        @endauth
                    </div>

                    <!-- Enhanced Quick Actions -->
                    <div class="property-card p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center space-x-3">
                            <div class="bg-indigo-100 p-3 rounded-full">
                                <i class="fas fa-bolt text-indigo-600 text-xl"></i>
                            </div>
                            <span>Quick Actions</span>
                        </h2>
                        <div class="space-y-4">
                            @auth
                            @if($property instanceof \App\Models\PropertyFromSheet)
                                <div class="text-sm text-gray-500 text-center p-4 bg-gray-50 rounded-lg">
                                    Properties from Google Sheets cannot be edited through the admin panel.
                                </div>
                            @else
                                <a href="{{ route('admin.properties.edit', $property->id) }}" 
                                   class="action-button w-full justify-center">
                                    <i class="fas fa-edit"></i>
                                    Edit Property
                                </a>
                            @endif
                            @endauth
                            
                            <button onclick="shareProperty()" 
                                    class="success-button w-full justify-center">
                                <i class="fas fa-share"></i>
                                Share Property
                            </button>
                            
                            @if($property->url && auth()->check())
                            <a href="{{ $property->url }}" target="_blank" 
                               class="secondary-button w-full justify-center">
                                <i class="fas fa-external-link-alt"></i>
                                View Original Listing
                            </a>
                            @elseif($property->url)
                            <div class="bg-gray-100 border border-gray-300 text-gray-600 px-4 py-3 rounded-lg text-center">
                                <i class="fas fa-lock mr-2"></i>
                                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-800">Login</a> to view original listing
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Enhanced Fullscreen Image Modal -->
    <div id="fullscreenModal" class="modal-overlay fixed inset-0 hidden z-50 flex items-center justify-center p-4">
        <div class="modal-content relative max-w-7xl max-h-full">
            <img id="fullscreenImage" src="" alt="Property photo" class="max-w-full max-h-full object-contain rounded-20">
            <button onclick="closeFullscreen()" class="absolute top-6 right-6 text-white text-4xl hover:text-gray-300 transition-colors duration-300 bg-black bg-opacity-50 p-3 rounded-full z-10">
                <i class="fas fa-times"></i>
            </button>
            <button onclick="previousFullscreenImage()" class="absolute left-6 top-1/2 transform -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition-colors duration-300 bg-black bg-opacity-50 p-4 rounded-full z-10">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button onclick="nextFullscreenImage()" class="absolute right-6 top-1/2 transform -translate-y-1/2 text-white text-4xl hover:text-gray-300 transition-colors duration-300 bg-black bg-opacity-50 p-4 rounded-full z-10">
                <i class="fas fa-chevron-right"></i>
            </button>
            <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-white text-xl bg-black bg-opacity-50 px-6 py-3 rounded-full">
                <span id="fullscreenCounter">1</span> of {{ count($property->high_quality_photos_array ?? $property->all_photos_array ?? []) }}
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <button onclick="scrollToTop()" class="glass-card p-4 rounded-full hover:scale-110 transition-transform duration-300 shadow-2xl">
            <i class="fas fa-arrow-up text-white text-xl"></i>
        </button>
    </div>

    <script>
        // Global photos data from PHP
        let propertyPhotos = []; // High quality photos for main image
        let originalPhotos = []; // Original photos for thumbnails
        let currentImageIndex = 0;
        let totalImages = 0;
        
        // Initialize photos on page load
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const photosElement = document.querySelector('[data-photos]');
                if (photosElement && photosElement.dataset.photos) {
                    const photosData = photosElement.dataset.photos;
                    propertyPhotos = JSON.parse(photosData);
                    // Ensure it's an array and filter out any empty/null values
                    if (Array.isArray(propertyPhotos)) {
                        propertyPhotos = propertyPhotos.filter(photo => photo && photo.trim() !== '');
                    } else {
                        propertyPhotos = [];
                    }
                    totalImages = propertyPhotos.length;
                    console.log('Loaded photos:', totalImages, 'Photos array:', propertyPhotos);
                }
                if (photosElement && photosElement.dataset.originalPhotos) {
                    originalPhotos = JSON.parse(photosElement.dataset.originalPhotos);
                    if (Array.isArray(originalPhotos)) {
                        originalPhotos = originalPhotos.filter(photo => photo && photo.trim() !== '');
                    }
                }
                
                // Fallback: if no photos from data attribute, try to get from thumbnails
                if (totalImages === 0 || propertyPhotos.length === 0) {
                    const thumbnails = document.querySelectorAll('.thumbnail img');
                    propertyPhotos = Array.from(thumbnails)
                        .map(img => img.src)
                        .filter(src => src && !src.includes('data:image/svg') && src.trim() !== '');
                    totalImages = propertyPhotos.length;
                    console.log('Loaded photos from thumbnails:', totalImages, 'Photos array:', propertyPhotos);
                }
                
                // Final validation - ensure we have photos
                if (propertyPhotos.length === 0) {
                    console.warn('No photos found after initialization');
                } else {
                    console.log('Successfully initialized with', propertyPhotos.length, 'photos');
                }
            } catch (error) {
                console.error('Error parsing photos data:', error);
                propertyPhotos = [];
                originalPhotos = [];
                totalImages = 0;
            }
        });
        
        function showImage(index) {
            if (totalImages === 0) {
                console.warn('No photos available');
                return;
            }
            
            // Ensure index is within bounds and is a valid integer
            index = parseInt(index, 10);
            if (isNaN(index) || index < 0) index = totalImages - 1;
            if (index >= totalImages) index = 0;
            
            currentImageIndex = index;
            const mainImage = document.getElementById('mainImage');
            const currentImageSpan = document.getElementById('currentImage');
            
            if (mainImage && currentImageSpan) {
                // Ensure we have a valid array and the index exists
                if (propertyPhotos && Array.isArray(propertyPhotos) && propertyPhotos.length > 0) {
                    // Make sure index is valid
                    if (index >= 0 && index < propertyPhotos.length && propertyPhotos[index]) {
                        mainImage.src = propertyPhotos[index];
                        currentImageSpan.textContent = index + 1;
                    
                        // Update thumbnail selection
                        document.querySelectorAll('.thumbnail').forEach((thumb, i) => {
                            if (i === index) {
                                thumb.classList.add('active');
                            } else {
                                thumb.classList.remove('active');
                            }
                        });
                    } else {
                        console.warn('Photo not found at index:', index, 'Array length:', propertyPhotos.length, 'Available indices:', Array.from({length: propertyPhotos.length}, (_, i) => i));
                    }
                } else {
                    console.warn('Invalid photos array:', propertyPhotos);
                }
            } else {
                console.warn('Main image or counter element not found');
            }
        }

        function nextImage() {
            if (totalImages === 0 || !propertyPhotos || propertyPhotos.length === 0) {
                console.warn('No photos to navigate');
                return;
            }
            // Ensure we're using the actual array length, not totalImages
            const actualLength = propertyPhotos.length;
            let nextIndex = (currentImageIndex + 1) % actualLength;
            
            // Skip any undefined/null entries
            let attempts = 0;
            while (attempts < actualLength && (!propertyPhotos[nextIndex] || propertyPhotos[nextIndex].trim() === '')) {
                nextIndex = (nextIndex + 1) % actualLength;
                attempts++;
            }
            
            currentImageIndex = nextIndex;
            showImage(currentImageIndex);
        }

        function previousImage() {
            if (totalImages === 0 || !propertyPhotos || propertyPhotos.length === 0) {
                console.warn('No photos to navigate');
                return;
            }
            // Ensure we're using the actual array length, not totalImages
            const actualLength = propertyPhotos.length;
            let prevIndex = (currentImageIndex - 1 + actualLength) % actualLength;
            
            // Skip any undefined/null entries
            let attempts = 0;
            while (attempts < actualLength && (!propertyPhotos[prevIndex] || propertyPhotos[prevIndex].trim() === '')) {
                prevIndex = (prevIndex - 1 + actualLength) % actualLength;
                attempts++;
            }
            
            currentImageIndex = prevIndex;
            showImage(currentImageIndex);
        }

        function openFullscreen() {
            const fullscreenModal = document.getElementById('fullscreenModal');
            const fullscreenImage = document.getElementById('fullscreenImage');
            const fullscreenCounter = document.getElementById('fullscreenCounter');
            
            if (fullscreenModal && fullscreenImage && fullscreenCounter) {
                const mainImage = document.getElementById('mainImage');
                if (mainImage && propertyPhotos.length > 0) {
                    fullscreenImage.src = propertyPhotos[currentImageIndex] || mainImage.src;
                } else if (mainImage) {
                    fullscreenImage.src = mainImage.src;
                }
                fullscreenCounter.textContent = (currentImageIndex + 1) + ' of ' + totalImages;
                fullscreenModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }
        
        function nextFullscreenImage() {
            if (totalImages > 0) {
                nextImage();
                const fullscreenImage = document.getElementById('fullscreenImage');
                const fullscreenCounter = document.getElementById('fullscreenCounter');
                if (fullscreenImage && propertyPhotos.length > 0) {
                    fullscreenImage.src = propertyPhotos[currentImageIndex];
                }
                if (fullscreenCounter) {
                    fullscreenCounter.textContent = (currentImageIndex + 1) + ' of ' + totalImages;
                }
            }
        }
        
        function previousFullscreenImage() {
            if (totalImages > 0) {
                previousImage();
                const fullscreenImage = document.getElementById('fullscreenImage');
                const fullscreenCounter = document.getElementById('fullscreenCounter');
                if (fullscreenImage && propertyPhotos.length > 0) {
                    fullscreenImage.src = propertyPhotos[currentImageIndex];
                }
                if (fullscreenCounter) {
                    fullscreenCounter.textContent = (currentImageIndex + 1) + ' of ' + totalImages;
                }
            }
        }

        function closeFullscreen() {
            const fullscreenModal = document.getElementById('fullscreenModal');
            if (fullscreenModal) {
                fullscreenModal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            } else if (e.key === 'Escape') {
                closeFullscreen();
            }
        });

        function shareProperty() {
            if (navigator.share) {
                navigator.share({
                    title: '{{ $property->title }}',
                    text: 'Check out this property: {{ $property->title }}',
                    url: window.location.href
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    // Create a temporary success message
                    const successMsg = document.createElement('div');
                    successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
                    successMsg.innerHTML = '<i class="fas fa-check mr-2"></i>Property link copied to clipboard!';
                    document.body.appendChild(successMsg);
                    
                    setTimeout(() => {
                        successMsg.remove();
                    }, 3000);
                });
            }
        }

        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Keyboard navigation for fullscreen
        document.addEventListener('keydown', function(e) {
            const fullscreenModal = document.getElementById('fullscreenModal');
            if (!fullscreenModal || fullscreenModal.classList.contains('hidden')) {
                // If not in fullscreen, allow arrow keys to navigate main gallery
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    previousImage();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    nextImage();
                }
                return;
            }
            
            // In fullscreen mode
            if (e.key === 'Escape') {
                closeFullscreen();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                previousFullscreenImage();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                nextFullscreenImage();
            }
        });

        // Close fullscreen when clicking outside
        document.getElementById('fullscreenModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeFullscreen();
            }
        });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';

        // Add intersection observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        // Observe all property cards
        document.querySelectorAll('.property-card').forEach(card => {
            observer.observe(card);
        });
    </script>
</body>
</html>
