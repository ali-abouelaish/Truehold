<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Browse properties on an interactive map - TrueHold Premium Property Listings">
    <meta name="theme-color" content="#1e3a5f">
    <title>Map View - TrueHold</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/truehold-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/truehold-logo.jpg') }}">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
/* ==========================================
   TRUEHOLD - Map View
   Color Palette: White, Navy Blue, Gold
   ========================================== */

/* CSS Variables */
:root {
    --primary-navy: #1e3a5f;
    --navy-dark: #152a45;
    --navy-light: #2d5280;
    --gold: #d4af37;
    --gold-light: #e8c55c;
    --gold-dark: #b8941f;
    --white: #ffffff;
    --off-white: #f8f9fa;
    --light-gray: #e9ecef;
    --gray: #6c757d;
    --text-dark: #212529;
    --shadow-sm: 0 2px 4px rgba(30, 58, 95, 0.08);
    --shadow-md: 0 4px 12px rgba(30, 58, 95, 0.12);
    --shadow-lg: 0 8px 24px rgba(30, 58, 95, 0.15);
    --transition: all 0.3s ease;
}

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    color: var(--text-dark);
    background-color: var(--off-white);
    line-height: 1.6;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow: hidden;
}

.map-page {
    overflow: hidden;
}

/* ==========================================
   NAVIGATION
   ========================================== */

.navbar {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    box-shadow: 0 4px 30px rgba(30, 58, 95, 0.08);
    position: sticky;
    top: 0;
    z-index: 1001;
    border-bottom: 1px solid rgba(30, 58, 95, 0.06);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

.nav-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 0;
}

.logo {
    display: flex;
    align-items: center;
    gap: 14px;
    font-weight: 700;
    font-size: 22px;
    color: var(--primary-navy);
    cursor: pointer;
    transition: var(--transition);
}

.logo:hover {
    transform: translateX(2px);
}

.logo-icon {
    width: 46px;
    height: 46px;
    background: linear-gradient(135deg, var(--primary-navy), var(--navy-light));
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.2);
    transition: var(--transition);
}

.logo-icon svg {
    stroke: var(--gold);
    stroke-width: 2.5;
}

.logo:hover .logo-icon {
    transform: rotate(-5deg) scale(1.05);
    box-shadow: 0 6px 20px rgba(30, 58, 95, 0.3);
}

.logo-text {
    letter-spacing: 1px;
}

.nav-links {
    display: flex;
    list-style: none;
    gap: 8px;
    align-items: center;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--gray);
    font-weight: 500;
    font-size: 15px;
    padding: 12px 20px;
    border-radius: 10px;
    position: relative;
    transition: var(--transition);
}

.nav-link svg {
    stroke-width: 2;
    transition: var(--transition);
}

.nav-link:hover {
    color: var(--primary-navy);
    background-color: rgba(30, 58, 95, 0.05);
}

.nav-link.active {
    color: var(--primary-navy);
    background-color: rgba(30, 58, 95, 0.08);
    font-weight: 600;
}

.nav-link.active svg {
    stroke: var(--gold);
}

.btn-agent {
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.3);
    margin-left: 8px;
}

.btn-agent:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 24px rgba(212, 175, 55, 0.4);
}

/* ==========================================
   MAP HEADER
   ========================================== */

.map-header {
    background: linear-gradient(135deg, var(--primary-navy) 0%, var(--navy-light) 100%);
    color: var(--white);
    padding: 24px 0;
    box-shadow: 0 4px 16px rgba(30, 58, 95, 0.15);
}

.map-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.map-title-section {
    display: flex;
    align-items: center;
    gap: 24px;
}

.map-title {
    font-size: 32px;
    font-weight: 700;
    margin: 0;
}

.properties-loaded {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.properties-loaded svg {
    color: var(--gold);
}

.map-header-actions {
    display: flex;
    gap: 12px;
}

.btn-list-view {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: var(--transition);
}

.btn-list-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
}

/* ==========================================
   FILTERS BAR
   ========================================== */

.map-filters-bar {
    background-color: var(--white);
    padding: 16px 0;
    border-bottom: 1px solid var(--light-gray);
}

.map-filters-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    background-color: var(--white);
    border: 2px solid var(--light-gray);
    border-radius: 8px;
    color: var(--primary-navy);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
}

.map-filters-toggle:hover {
    border-color: var(--primary-navy);
    background-color: var(--off-white);
}

.map-filters-toggle svg:first-child {
    color: var(--gold);
}

.map-filters-toggle .chevron {
    margin-left: 8px;
    transition: transform 0.3s ease;
}

.map-filters-content {
    display: none;
    margin-top: 16px;
    padding: 32px;
    background: linear-gradient(135deg, #475569 0%, #3f4a5c 100%);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.map-filters-content.active {
    display: block;
}

.filters-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.filters-header svg {
    color: var(--gold);
    font-size: 24px;
}

.filters-header h3 {
    color: var(--white);
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    color: rgba(255, 255, 255, 0.9);
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.filter-input {
    padding: 14px 16px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.95);
    background-color: rgba(30, 41, 59, 0.5);
    transition: var(--transition);
}

.filter-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.filter-input:focus {
    outline: none;
    border-color: var(--gold);
    background-color: rgba(30, 41, 59, 0.7);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

select.filter-input {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='rgba(255,255,255,0.7)' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    padding-right: 36px;
}

select.filter-input option {
    background-color: #1e293b;
    color: var(--white);
}

.filter-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.filter-btn-apply {
    padding: 12px 32px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn-apply:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
}

.filter-btn-clear {
    padding: 12px 24px;
    background-color: transparent;
    color: rgba(255, 255, 255, 0.9);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-btn-clear:hover {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
}

/* ==========================================
   MAP CONTROLS
   ========================================== */

.map-controls {
    position: absolute;
    top: 160px;
    left: 24px;
    z-index: 1000;
    display: flex;
    gap: 8px;
    background: var(--white);
    padding: 6px;
    border-radius: 10px;
    box-shadow: var(--shadow-md);
}

.map-control-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background-color: var(--white);
    border: 2px solid transparent;
    border-radius: 8px;
    color: var(--text-dark);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: var(--transition);
}

.map-control-btn:hover {
    background-color: var(--off-white);
}

.map-control-btn.active {
    background-color: var(--primary-navy);
    color: var(--white);
    border-color: var(--primary-navy);
}

.map-control-btn.active svg {
    stroke: var(--gold);
}

/* Map Legend */
.map-legend {
    position: absolute;
    bottom: 30px;
    left: 24px;
    z-index: 1000;
    background: var(--white);
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    min-width: 200px;
}

.map-legend-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.map-legend-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.map-legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 12px;
    color: var(--text-dark);
}

.legend-marker {
    width: 14px;
    height: 14px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.legend-marker.premium {
    width: 18px;
    height: 18px;
    background: #FFD700;
    border: 3px solid #b8941f;
}

.legend-marker.house {
    background: #3b82f6;
    border: 2px solid #1e3a5f;
}

.legend-marker.flat {
    background: #10b981;
    border: 2px solid #1e3a5f;
}

.legend-marker.studio {
    background: #8b5cf6;
    border: 2px solid #1e3a5f;
}

.legend-marker.room {
    background: #f59e0b;
    border: 2px solid #1e3a5f;
}

.legend-marker.other {
    background: #6b7280;
    border: 2px solid #1e3a5f;
}

/* ==========================================
   MAP CONTAINER
   ========================================== */

.map-container {
    position: fixed;
    top: 200px;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--light-gray);
}

#map {
    width: 100%;
    height: 100%;
}

.map-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    background: linear-gradient(135deg, #e6f2ff 0%, #f0f7ff 100%);
    color: var(--primary-navy);
}

.map-placeholder svg {
    stroke: var(--primary-navy);
    opacity: 0.3;
}

.map-placeholder p {
    font-size: 24px;
    font-weight: 700;
    margin: 0;
}

.map-placeholder small {
    font-size: 14px;
    color: var(--gray);
    max-width: 300px;
    text-align: center;
}

/* Loading Screen */
.loading-screen {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 2000;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid var(--light-gray);
    border-top-color: var(--gold);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loading-text {
    margin-top: 20px;
    font-size: 18px;
    font-weight: 600;
    color: var(--primary-navy);
}

/* Info Window Styling */
.gm-style .gm-style-iw-c {
    border-radius: 12px;
    padding: 0;
    max-width: 304px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.gm-style .gm-style-iw-d {
    overflow: hidden !important;
    padding: 0;
}

.gm-style .gm-style-iw-t::after {
    background: linear-gradient(45deg, var(--white) 50%, transparent 51%, transparent);
    box-shadow: -2px 2px 2px 0 rgba(0, 0, 0, 0.1);
}

.info-window-card {
    background: var(--white);
    overflow: hidden;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.info-window-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    display: block;
}

.info-window-content {
    padding: 16px;
}

.info-window-header {
    margin-bottom: 12px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e7eb;
}

.info-window-title {
    font-size: 14px;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 6px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.info-window-price {
    font-size: 19px;
    font-weight: 700;
    color: var(--gold);
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-window-price svg {
    width: 16px;
    height: 16px;
}

.info-window-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 12px;
}

.info-window-detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11px;
    color: var(--gray);
}

.info-window-detail-item svg {
    width: 13px;
    height: 13px;
    color: var(--gold);
    flex-shrink: 0;
}

.info-window-detail-item strong {
    color: var(--text-dark);
    font-weight: 600;
}

.info-window-footer {
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.info-window-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    width: 100%;
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border-radius: 8px;
    font-weight: 600;
    font-size: 12px;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 3px 10px rgba(212, 175, 55, 0.3);
}

.info-window-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 16px rgba(212, 175, 55, 0.4);
}

.info-window-btn svg {
    width: 13px;
    height: 13px;
}

/* Close button styling */
.gm-style-iw button {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    background: var(--white) !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
    opacity: 1 !important;
    top: 8px !important;
    right: 8px !important;
}

.gm-style-iw button:hover {
    background: var(--off-white) !important;
}

.gm-style-iw button img {
    margin: 0 !important;
}

/* Adjust close button position */
.gm-style .gm-style-iw-tc {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
}

/* ==========================================
   RESPONSIVE DESIGN
   ========================================== */

@media (max-width: 768px) {
    .map-header {
        padding: 16px 0;
    }
    
    .map-header-content {
        flex-wrap: wrap;
    }
    
    .map-title {
        font-size: 24px;
    }
    
    .properties-loaded {
        font-size: 14px;
    }
    
    .map-controls {
        top: 150px;
        left: 16px;
    }
    
    .map-control-btn {
        padding: 9px 14px;
        font-size: 13px;
    }
    
    .map-legend {
        bottom: 20px;
        left: 16px;
        padding: 12px;
        min-width: 170px;
    }
    
    .map-legend-title {
        font-size: 12px;
        margin-bottom: 10px;
    }
    
    .map-legend-item {
        font-size: 11px;
        gap: 8px;
    }
    
    .legend-marker {
        width: 12px;
        height: 12px;
    }
    
    .legend-marker.premium {
        width: 15px;
        height: 15px;
        border-width: 2px;
    }
    
    .map-container {
        top: 190px;
    }
    
    .nav-links {
        gap: 4px;
    }
    
    .nav-link {
        padding: 10px 12px;
        font-size: 13px;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
    
    .map-filters-content {
        padding: 24px 20px;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .filter-btn-apply,
    .filter-btn-clear {
        width: 100%;
        justify-content: center;
    }
    
    /* Info window responsive adjustments */
    .gm-style .gm-style-iw-c {
        max-width: 256px !important;
    }
    
    .info-window-image {
        height: 128px;
    }
    
    .info-window-content {
        padding: 12px;
    }
    
    .info-window-title {
        font-size: 13px;
    }
    
    .info-window-price {
        font-size: 16px;
    }
    
    .info-window-detail-item {
        font-size: 10px;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .container {
        padding: 0 16px;
    }
    
    .map-title-section {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .logo-text {
        font-size: 16px;
    }
}
    </style>
</head>
<body class="map-page">
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-content">
                <a href="{{ route('properties.index') }}" class="logo">
                    <div class="logo-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2"/>
                            <path d="M9 22V12h6v10" stroke-width="2"/>
                        </svg>
                    </div>
                    <span class="logo-text">TRUEHOLD</span>
                </a>
                <ul class="nav-links">
                    <li><a href="{{ route('properties.index') }}" class="nav-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" stroke-width="2"/>
                        </svg>
                        Properties
                    </a></li>
                    <li><a href="{{ route('properties.map') }}" class="nav-link active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z" stroke-width="2"/>
                        </svg>
                        Map View
                    </a></li>
                    @auth
                    <li><a href="{{ route('admin.dashboard') }}" class="btn-agent">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Dashboard
                    </a></li>
                    @else
                    <li><a href="{{ route('login', ['redirect' => url()->current()]) }}" class="btn-agent">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        Agent Login
                    </a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Map Header -->
    <div class="map-header">
        <div class="container">
            <div class="map-header-content">
                <div class="map-title-section">
                    <h1 class="map-title">Property Map</h1>
                    <div class="properties-loaded">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                        <span id="propertyCount">{{ $properties->count() }} properties loaded</span>
                    </div>
                </div>
                <div class="map-header-actions">
                    <a href="{{ route('properties.index') }}" class="btn-list-view">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        List View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="map-filters-bar">
        <div class="container">
            <button class="map-filters-toggle" onclick="toggleMapFilters()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                </svg>
                <span id="filterToggleText">Show Filters</span>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="chevron" id="filterChevron">
                    <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <!-- Filters Content -->
            <div class="map-filters-content" id="mapFiltersContent">
                <div class="filters-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                    </svg>
                    <h3>Search Filters</h3>
                </div>
                
                <div class="filter-grid">
                    <div class="filter-group">
                        <label class="filter-label">Location</label>
                        <select id="filterLocation" class="filter-input">
                            <option value="">All Locations</option>
                            @foreach($locations ?? [] as $location)
                                @if($location && $location !== 'N/A')
                                    <option value="{{ $location }}">{{ $location }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Property Type</label>
                        <select id="filterPropertyType" class="filter-input">
                            <option value="">All Types</option>
                            @foreach($propertyTypes ?? [] as $type)
                                @if($type && $type !== 'N/A')
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    @auth
                    <div class="filter-group">
                        <label class="filter-label">Agent Name</label>
                        <select id="filterAgentName" class="filter-input">
                            <option value="">All Agents</option>
                            @foreach($agentNames ?? [] as $agentName)
                                @if($agentName && $agentName !== 'N/A')
                                    <option value="{{ $agentName }}">
                                        {{ $agentName }}
                                        @if(in_array($agentName, $agentsWithPaying ?? []))
                                            ⚡
                                        @endif
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="filter-group">
                        <label class="filter-label">Min Price</label>
                        <input type="number" id="filterMinPrice" class="filter-input" placeholder="£0" min="0">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Max Price</label>
                        <input type="number" id="filterMaxPrice" class="filter-input" placeholder="£5000" min="0">
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Couples Allowed</label>
                        <select id="filterCouplesAllowed" class="filter-input">
                            <option value="">All Properties</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>
                </div>
                
                <div class="filter-actions">
                    <button type="button" onclick="clearMapFilters()" class="filter-btn-clear">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Clear Filters
                    </button>
                    <button type="button" onclick="applyMapFilters()" class="filter-btn-apply">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="8" stroke-width="2"/>
                            <path d="M21 21l-4.35-4.35" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Apply Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Controls -->
    <div class="map-controls">
        <button class="map-control-btn active" id="mapViewBtn" onclick="toggleMapView('roadmap')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path d="M1 6v16l7-4 8 4 7-4V2l-7 4-8-4-7 4z" stroke-width="2"/>
            </svg>
            Map
        </button>
        <button class="map-control-btn" id="satelliteViewBtn" onclick="toggleMapView('hybrid')">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="10" stroke-width="2"/>
                <path d="M12 2v20M2 12h20" stroke-width="2"/>
            </svg>
            Satellite
        </button>
    </div>

    <!-- Map Legend -->
    <div class="map-legend">
        <div class="map-legend-title">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
            </svg>
            Map Legend
        </div>
        <div class="map-legend-items">
            <div class="map-legend-item">
                <div class="legend-marker premium"></div>
                <span><strong>Premium Properties</strong></span>
            </div>
            <div class="map-legend-item">
                <div class="legend-marker house"></div>
                <span>Houses</span>
            </div>
            <div class="map-legend-item">
                <div class="legend-marker flat"></div>
                <span>Flats / Apartments</span>
            </div>
            <div class="map-legend-item">
                <div class="legend-marker studio"></div>
                <span>Studios</span>
            </div>
            <div class="map-legend-item">
                <div class="legend-marker room"></div>
                <span>Rooms</span>
            </div>
            <div class="map-legend-item">
                <div class="legend-marker other"></div>
                <span>Other</span>
            </div>
        </div>
    </div>

    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-spinner"></div>
        <div class="loading-text">Loading properties...</div>
    </div>

    <!-- Map Container -->
    <div class="map-container">
        <div id="map"></div>
    </div>

    <!-- Properties Data (hidden) -->
    <div id="properties-data" style="display: none;">{!! json_encode($propertiesForJson ?? $properties->map(fn($p) => $p->toArray())) !!}</div>

    <!-- Google Maps API -->
    @if(config('services.google.maps_api_key') && config('services.google.maps_api_key') !== 'YOUR_GOOGLE_MAPS_API_KEY')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap&v=weekly&loading=async"></script>
    @else
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('loadingScreen').innerHTML = '<div style="text-align: center;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i><div class="loading-text" style="color: #ef4444;">Google Maps API key not configured</div><p style="color: #6b7280; margin-top: 8px;">Please add GOOGLE_MAPS_API_KEY to your .env file</p></div>';
            });
        </script>
    @endif

    <script>
        let map;
        let markers = [];
        let infoWindow;
        let properties = [];

        function initMap() {
            try {
                console.log('🗺️ Initializing map...');
                
                // Create map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 51.5074, lng: -0.1278 }, // London center
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false,
                    fullscreenControl: true,
                    streetViewControl: true,
                    zoomControl: true
                });
                
                // Create info window
                infoWindow = new google.maps.InfoWindow();
                
                // Load properties
                loadProperties();
                
                // Hide loading screen
                document.getElementById('loadingScreen').style.display = 'none';
                
                console.log('✅ Map initialized successfully');
                
            } catch (error) {
                console.error('❌ Map initialization failed:', error);
                document.getElementById('loadingScreen').innerHTML = '<div style="text-align: center;"><i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 16px;"></i><div class="loading-text" style="color: #ef4444;">Map initialization failed</div><p style="color: #6b7280; margin-top: 8px;">' + error.message + '</p></div>';
            }
        }

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
                    alert('No properties with valid coordinates found');
                    return;
                }
                
                // Store all properties globally for filtering
                window.allProperties = validProperties;
                
                // Update property count
                document.getElementById('propertyCount').textContent = 
                    `${validProperties.length} properties loaded`;

                // Create markers
                createMarkers(validProperties);

                // Fit map to bounds
                fitMapToProperties(validProperties);

            } catch (error) {
                console.error('❌ Error loading properties:', error);
                alert('Failed to load properties: ' + error.message);
            }
        }

        function createMarkers(properties) {
            // Clear existing markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            window.markers = markers;

            properties.forEach(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                
                // Check if property is premium
                const isPremium = property.flag && property.flag.toLowerCase() === 'premium';
                
                // Determine marker color based on property type and premium status
                let markerColor, hoverColor, markerShape, markerScale;
                
                if (isPremium) {
                    // Premium properties - Gold star
                    markerShape = google.maps.SymbolPath.CIRCLE;
                    markerScale = 14;
                    markerColor = '#FFD700'; // Bright gold
                    hoverColor = '#FFC107';
                } else {
                    // Different colors for different property types
                    const propertyType = (property.property_type || '').toLowerCase();
                    
                    if (propertyType.includes('house') || propertyType.includes('home')) {
                        markerColor = '#3b82f6'; // Blue for houses
                        hoverColor = '#60a5fa';
                    } else if (propertyType.includes('flat') || propertyType.includes('apartment')) {
                        markerColor = '#10b981'; // Green for flats
                        hoverColor = '#34d399';
                    } else if (propertyType.includes('studio')) {
                        markerColor = '#8b5cf6'; // Purple for studios
                        hoverColor = '#a78bfa';
                    } else if (propertyType.includes('room')) {
                        markerColor = '#f59e0b'; // Orange for rooms
                        hoverColor = '#fbbf24';
                    } else {
                        markerColor = '#6b7280'; // Gray for others
                        hoverColor = '#9ca3af';
                    }
                    
                    markerShape = google.maps.SymbolPath.CIRCLE;
                    markerScale = 10;
                }

                const marker = new google.maps.Marker({
                    position: { lat, lng },
                    map: map,
                    title: property.title,
                    animation: google.maps.Animation.DROP,
                    icon: {
                        path: markerShape,
                        scale: markerScale,
                        fillColor: markerColor,
                        fillOpacity: 1,
                        strokeColor: isPremium ? '#b8941f' : '#1e3a5f',
                        strokeWeight: isPremium ? 4 : 3
                    },
                    zIndex: isPremium ? 1000 : 100 // Premium markers on top
                });
                
                // Store original marker properties for hover
                marker.originalIcon = {
                    path: markerShape,
                    scale: markerScale,
                    fillColor: markerColor,
                    fillOpacity: 1,
                    strokeColor: isPremium ? '#b8941f' : '#1e3a5f',
                    strokeWeight: isPremium ? 4 : 3
                };
                
                marker.hoverIcon = {
                    path: markerShape,
                    scale: markerScale + 2,
                    fillColor: hoverColor,
                    fillOpacity: 1,
                    strokeColor: isPremium ? '#b8941f' : '#1e3a5f',
                    strokeWeight: isPremium ? 5 : 4
                };
                
                // Add hover effect
                marker.addListener('mouseover', function() {
                    this.setIcon(this.hoverIcon);
                });
                
                marker.addListener('mouseout', function() {
                    this.setIcon(this.originalIcon);
                });

                marker.addListener('click', () => {
                    // Get the first image or use a placeholder
                    const imageUrl = property.first_photo_url || 
                                   (property.high_quality_photos_array && property.high_quality_photos_array[0]) || 
                                   'https://via.placeholder.com/380x200/1e3a5f/d4af37?text=No+Image';
                    
                    const content = `
                        <div class="info-window-card">
                            <img src="${imageUrl}" alt="${property.title || 'Property'}" class="info-window-image" onerror="this.src='https://via.placeholder.com/380x200/1e3a5f/d4af37?text=No+Image'">
                            <div class="info-window-content">
                                <div class="info-window-header">
                                    <h3 class="info-window-title">${property.title || 'Property Details'}</h3>
                                    <div class="info-window-price">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 6v12M15 9H9.5a2.5 2.5 0 0 0 0 5h5a2.5 2.5 0 0 1 0 5H9"/>
                                        </svg>
                                        ${property.formatted_price || property.price || 'Price not available'}
                                    </div>
                                </div>
                                <div class="info-window-details">
                                    ${property.location ? `
                                        <div class="info-window-detail-item">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                            </svg>
                                            <span><strong>Location:</strong> ${property.location}</span>
                                        </div>
                                    ` : ''}
                                    ${property.property_type ? `
                                        <div class="info-window-detail-item">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path d="M9 22V12h6v10"/>
                                            </svg>
                                            <span><strong>Type:</strong> ${property.property_type}</span>
                                        </div>
                                    ` : ''}
                                    ${property.couples_allowed ? `
                                        <div class="info-window-detail-item">
                                            <svg viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                            </svg>
                                            <span><strong>Couples:</strong> ${property.couples_allowed === 'Yes' ? '✓ Allowed' : '✗ Not Allowed'}</span>
                                        </div>
                                    ` : ''}
                                </div>
                                <div class="info-window-footer">
                                    <a href="/properties/${property.id}" class="info-window-btn">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        View Full Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    infoWindow.setContent(content);
                    infoWindow.open(map, marker);
                });

                markers.push(marker);
            });
            
            // Update global markers reference
            window.markers = markers;
        }

        function fitMapToProperties(properties) {
            if (properties.length === 0) return;

            const bounds = new google.maps.LatLngBounds();
            properties.forEach(property => {
                const lat = parseFloat(property.latitude);
                const lng = parseFloat(property.longitude);
                bounds.extend({ lat, lng });
            });

            map.fitBounds(bounds);
            
            // Don't zoom in too much for single property
            const listener = google.maps.event.addListener(map, "idle", function() {
                if (map.getZoom() > 16) map.setZoom(16);
                google.maps.event.removeListener(listener);
            });
        }

        function toggleMapView(mapType) {
            map.setMapTypeId(mapType);
            
            // Update button states
            const mapBtn = document.getElementById('mapViewBtn');
            const satBtn = document.getElementById('satelliteViewBtn');
            
            if (mapType === 'roadmap') {
                mapBtn.classList.add('active');
                satBtn.classList.remove('active');
            } else {
                satBtn.classList.add('active');
                mapBtn.classList.remove('active');
            }
        }

        function toggleMapFilters() {
            const text = document.getElementById('filterToggleText');
            const chevron = document.getElementById('filterChevron');
            const filtersContent = document.getElementById('mapFiltersContent');
            
            if (text.textContent === 'Show Filters') {
                text.textContent = 'Hide Filters';
                chevron.style.transform = 'rotate(180deg)';
                filtersContent.classList.add('active');
            } else {
                text.textContent = 'Show Filters';
                chevron.style.transform = 'rotate(0deg)';
                filtersContent.classList.remove('active');
            }
        }
        
        function applyMapFilters() {
            const location = document.getElementById('filterLocation').value.toLowerCase();
            const propertyType = document.getElementById('filterPropertyType').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('filterMinPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('filterMaxPrice').value) || Infinity;
            const couplesAllowed = document.getElementById('filterCouplesAllowed').value.toLowerCase();
            @auth
            const agentName = document.getElementById('filterAgentName')?.value.toLowerCase() || '';
            @endauth
            
            let visibleCount = 0;
            
            // Filter markers
            if (window.markers) {
                window.markers.forEach((marker, index) => {
                    const property = window.allProperties[index];
                    if (!property) return;
                    
                    let visible = true;
                    
                    // Location filter
                    if (location && property.location?.toLowerCase() !== location) {
                        visible = false;
                    }
                    
                    // Property type filter
                    if (propertyType && property.property_type?.toLowerCase() !== propertyType) {
                        visible = false;
                    }
                    
                    // Price filter
                    const price = parseFloat(property.price) || 0;
                    if (price < minPrice || price > maxPrice) {
                        visible = false;
                    }
                    
                    // Couples allowed filter
                    if (couplesAllowed && property.couples_allowed?.toLowerCase() !== couplesAllowed) {
                        visible = false;
                    }
                    
                    @auth
                    // Agent name filter (only for authenticated users)
                    if (agentName && property.agent_name?.toLowerCase() !== agentName) {
                        visible = false;
                    }
                    @endauth
                    
                    marker.setVisible(visible);
                    if (visible) visibleCount++;
                });
            }
            
            // Update property count
            const countElement = document.getElementById('propertyCount');
            if (countElement) {
                countElement.textContent = `${visibleCount} properties visible`;
            }
            
            // Optionally close the filters after applying
            toggleMapFilters();
        }
        
        function clearMapFilters() {
            // Reset all filter inputs
            document.getElementById('filterLocation').value = '';
            document.getElementById('filterPropertyType').value = '';
            document.getElementById('filterMinPrice').value = '';
            document.getElementById('filterMaxPrice').value = '';
            document.getElementById('filterCouplesAllowed').value = '';
            @auth
            const agentFilter = document.getElementById('filterAgentName');
            if (agentFilter) agentFilter.value = '';
            @endauth
            
            // Show all markers
            if (window.markers) {
                window.markers.forEach(marker => marker.setVisible(true));
            }
            
            // Update property count
            const countElement = document.getElementById('propertyCount');
            if (countElement && window.allProperties) {
                countElement.textContent = `${window.allProperties.length} properties loaded`;
            }
        }
    </script>
</body>
</html>
