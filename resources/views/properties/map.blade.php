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
    position: relative;
    z-index: 10;
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
   FILTERS SECTION
   ========================================== */

.filters-section {
    padding: 24px 0;
    background-color: var(--white);
    border-bottom: 1px solid var(--light-gray);
    position: relative;
    z-index: 10;
}

.filters-toggle {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 24px;
    background-color: var(--white);
    border: 2px solid var(--light-gray);
    border-radius: 10px;
    color: var(--primary-navy);
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    width: fit-content;
    cursor: pointer;
}

.filters-toggle:hover {
    border-color: var(--primary-navy);
    background-color: var(--off-white);
}

.filters-toggle svg:first-child {
    color: var(--gold);
}

.filters-toggle .chevron {
    margin-left: 8px;
    transition: transform 0.3s ease;
}

.filters-content {
    display: none;
    margin-top: 24px;
    padding: 32px;
    background: linear-gradient(135deg, #475569 0%, #3f4a5c 100%);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 100;
}

.filters-content.active {
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

/* Paying Agents Only Checkbox */
.paying-filter-checkbox {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    padding: 14px 16px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 8px;
    background-color: rgba(30, 41, 59, 0.5);
    cursor: pointer;
    transition: var(--transition);
}

.paying-filter-checkbox:hover {
    border-color: var(--gold);
    background-color: rgba(30, 41, 59, 0.7);
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
}

.paying-filter-checkbox input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.paying-filter-checkbox .checkbox-icon {
    color: rgba(255, 255, 255, 0.4);
    transition: var(--transition);
}

.paying-filter-checkbox:hover .checkbox-icon {
    color: rgba(255, 255, 255, 0.6);
}

.paying-filter-checkbox input[type="checkbox"]:checked ~ .checkbox-icon {
    color: var(--gold);
    filter: drop-shadow(0 0 8px rgba(212, 175, 55, 0.6));
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
    position: fixed;
    top: 285px;
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


/* ==========================================
   MAP CONTAINER
   ========================================== */

.map-container {
    position: fixed;
    top: 270px;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--light-gray);
    z-index: 1;
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
    border-radius: 10px;
    padding: 0 !important;
    max-width: 240px !important;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.2);
    overflow: hidden !important;
}

.gm-style .gm-style-iw-d {
    overflow: hidden !important;
    padding: 0 !important;
    max-width: 240px !important;
}

.gm-style .gm-style-iw-t::after {
    background: linear-gradient(45deg, var(--white) 50%, transparent 51%, transparent) !important;
    box-shadow: -2px 2px 2px 0 rgba(0, 0, 0, 0.1) !important;
}

.gm-style-iw-tc::after {
    background: linear-gradient(45deg, var(--white) 50%, transparent 51%, transparent) !important;
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
    height: 120px;
    object-fit: cover;
    background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
    display: block;
}

.info-window-content {
    padding: 12px;
}

.info-window-header {
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}

.info-window-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--primary-navy);
    margin-bottom: 4px;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
            overflow: hidden;
}

.info-window-price {
    font-size: 15px;
    font-weight: 700;
    color: var(--gold);
    display: flex;
    align-items: center;
    gap: 2px;
}

.info-window-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 8px;
}

.info-window-detail-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 9px;
    color: var(--gray);
}

.info-window-detail-item svg {
    width: 11px;
    height: 11px;
    color: var(--gold);
    flex-shrink: 0;
}

.info-window-detail-item strong {
    color: var(--text-dark);
    font-weight: 600;
}

.info-window-footer {
    padding-top: 8px;
    border-top: 1px solid #e5e7eb;
}

.info-window-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    width: 100%;
    padding: 7px 14px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    color: var(--white);
    border-radius: 6px;
    font-weight: 600;
    font-size: 10px;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
}

.info-window-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 16px rgba(212, 175, 55, 0.4);
}

.info-window-btn svg {
    width: 11px;
    height: 11px;
}

/* Close button styling */
.gm-style-iw-c button.gm-ui-hover-effect,
.gm-style-iw button.gm-ui-hover-effect {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    background: var(--white) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15) !important;
    opacity: 1 !important;
    top: 6px !important;
    right: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s ease !important;
    padding: 0 !important;
    border: 1px solid rgba(30, 58, 95, 0.1) !important;
}

.gm-style-iw-c button.gm-ui-hover-effect:hover,
.gm-style-iw button.gm-ui-hover-effect:hover {
    background: var(--primary-navy) !important;
    box-shadow: 0 4px 12px rgba(30, 58, 95, 0.3) !important;
    transform: scale(1.08) !important;
    border-color: var(--primary-navy) !important;
}

/* Style the close button icon span (mask-image) */
.gm-style-iw-c button.gm-ui-hover-effect span,
.gm-style-iw button.gm-ui-hover-effect span {
    background-color: var(--primary-navy) !important;
    width: 20px !important;
    height: 20px !important;
    margin: 6px !important;
    transition: background-color 0.2s ease !important;
}

.gm-style-iw-c button.gm-ui-hover-effect:hover span,
.gm-style-iw button.gm-ui-hover-effect:hover span {
    background-color: var(--white) !important;
}

/* Ensure button is properly positioned */
.gm-style-iw-c {
    position: relative !important;
}

.gm-style-iw-c button[aria-label="Close"],
.gm-style-iw button[aria-label="Close"] {
    position: absolute !important;
    top: 6px !important;
    right: 6px !important;
    z-index: 1000 !important;
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
        top: 265px;
        left: 16px;
    }
    
    .map-control-btn {
        padding: 9px 14px;
        font-size: 13px;
    }
    
    
    .map-container {
        top: 250px;
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
    
    /* Filters */
    .filters-section {
        padding: 16px 0;
    }
    
    .filters-toggle {
        width: 100%;
        justify-content: center;
        padding: 14px 20px;
        font-size: 15px;
    }
    
    .filters-content {
        padding: 20px 16px;
        margin-top: 12px;
    }
    
    .filters-header h3 {
        font-size: 18px;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .filter-label {
        font-size: 13px;
    }
    
    .filter-input {
        padding: 12px 14px;
        font-size: 14px;
    }
    
    .paying-filter-checkbox {
        padding: 12px 14px;
    }
    
    .paying-filter-checkbox .checkbox-icon {
        width: 20px;
        height: 20px;
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
        max-width: 210px !important;
    }
    
    .info-window-image {
        height: 100px;
    }
    
    .info-window-content {
        padding: 10px;
    }
    
    .info-window-title {
        font-size: 11px;
    }
    
    .info-window-price {
        font-size: 13px;
    }
    
    .info-window-detail-item {
        font-size: 8px;
        gap: 5px;
    }
    
    .info-window-btn {
        padding: 6px 12px;
        font-size: 9px;
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

    <!-- Filters -->
    <section class="filters-section">
        <div class="container">
            <button class="filters-toggle" onclick="toggleMapFilters()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                </svg>
                    <span id="filterToggleText">Show Filters</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="chevron" id="filterChevron">
                    <path d="M6 9l6 6 6-6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                </button>
                
            <!-- Filters Content -->
            <div class="filters-content" id="mapFiltersContent">
                <div class="filters-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z"/>
                    </svg>
                    <h3>Search Filters</h3>
                </div>
                
                <div class="filter-grid">
                    @auth
                    <div class="filter-group">
                        <label class="filter-label">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="color: var(--gold);">
                                <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z"/>
                            </svg>
                        </label>
                        <label class="paying-filter-checkbox" for="filterPayingOnly" title="Show only paying agents">
                            <input type="checkbox" id="filterPayingOnly">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="checkbox-icon">
                                <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z"/>
                            </svg>
                        </label>
                    </div>
                    @endif
                    
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
                                        @if(isset($agentsWithPaying) && (is_array($agentsWithPaying) ? in_array($agentName, $agentsWithPaying) : $agentsWithPaying->contains($agentName)))
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
    </section>

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

        // Generate a consistent color for each landlord/agent
        function getColorForAgent(agentName) {
            if (!agentName || agentName === 'N/A') {
                return '#6b7280'; // Gray for unknown
            }
            
            // Generate hash from agent name
            let hash = 0;
            for (let i = 0; i < agentName.length; i++) {
                hash = agentName.charCodeAt(i) + ((hash << 5) - hash);
            }
            
            // Define a palette of distinct, vibrant colors
            const colorPalette = [
                { main: '#3b82f6', hover: '#60a5fa' },  // Blue
                { main: '#10b981', hover: '#34d399' },  // Green
                { main: '#8b5cf6', hover: '#a78bfa' },  // Purple
                { main: '#f59e0b', hover: '#fbbf24' },  // Orange
                { main: '#ef4444', hover: '#f87171' },  // Red
                { main: '#06b6d4', hover: '#22d3ee' },  // Cyan
                { main: '#ec4899', hover: '#f472b6' },  // Pink
                { main: '#14b8a6', hover: '#2dd4bf' },  // Teal
                { main: '#f97316', hover: '#fb923c' },  // Deep Orange
                { main: '#6366f1', hover: '#818cf8' },  // Indigo
                { main: '#84cc16', hover: '#a3e635' },  // Lime
                { main: '#a855f7', hover: '#c084fc' },  // Violet
                { main: '#22c55e', hover: '#4ade80' },  // Emerald
                { main: '#eab308', hover: '#facc15' },  // Yellow
                { main: '#dc2626', hover: '#ef4444' }   // Crimson
            ];
            
            // Use hash to select color from palette
            const index = Math.abs(hash) % colorPalette.length;
            return colorPalette[index];
        }

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
                
                // Create info window with pixel offset to position it right above marker
                // Negative Y offset moves it up, centered on X axis
                infoWindow = new google.maps.InfoWindow({
                    pixelOffset: new google.maps.Size(0, -40)
                });

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
                
                // Determine marker color based on agent/landlord and premium status
                let markerColor, hoverColor, markerShape, markerScale;
                
                if (isPremium) {
                    // Premium properties - Gold star
                    markerShape = google.maps.SymbolPath.CIRCLE;
                    markerScale = 10;
                    markerColor = '#FFD700'; // Bright gold
                    hoverColor = '#FFC107';
                } else {
                    // Different colors for different landlords/agents
                    const agentName = property.agent_name || property.landlord || 'N/A';
                    const agentColors = getColorForAgent(agentName);
                    
                    markerColor = agentColors.main;
                    hoverColor = agentColors.hover;
                    markerShape = google.maps.SymbolPath.CIRCLE;
                    markerScale = 7;
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
                        strokeWeight: isPremium ? 3 : 2
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
                    strokeWeight: isPremium ? 3 : 2
                };
                
                marker.hoverIcon = {
                    path: markerShape,
                    scale: markerScale + 2,
                    fillColor: hoverColor,
                    fillOpacity: 1,
                    strokeColor: isPremium ? '#b8941f' : '#1e3a5f',
                    strokeWeight: isPremium ? 4 : 3
                };
                
                // Add hover effect
                marker.addListener('mouseover', function() {
                    this.setIcon(this.hoverIcon);
                });
                
                marker.addListener('mouseout', function() {
                    this.setIcon(this.originalIcon);
                });

                marker.addListener('click', () => {
                    // Close any open info windows
                    if (infoWindow) {
                        infoWindow.close();
                    }
                    
                    // Get marker position
                    const markerPosition = marker.getPosition();
                    
                    // Only pan if marker is not in view, otherwise just open info window
                    const bounds = map.getBounds();
                    if (!bounds || !bounds.contains(markerPosition)) {
                        map.panTo(markerPosition);
                    }
                    
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
                                        <span style="font-weight: 700; font-size: 16px;">£</span>
                                        ${(property.formatted_price || property.price || 'Price not available').toString().replace('£', '').trim()}
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
                                    <a href="/properties/${property.id}" class="info-window-btn" onclick="sessionStorage.setItem('propertyListingUrl', window.location.href);">
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
                    
                    // Calculate position to place info window right above marker
                    const markerPosition = marker.getPosition();
                    const projection = map.getProjection();
                    const scale = Math.pow(2, map.getZoom());
                    const markerPoint = projection.fromLatLngToPoint(markerPosition);
                    
                    // Offset upward by approximately 200 pixels (info window height + gap)
                    const offsetPixels = 200;
                    const offsetPoint = new google.maps.Point(
                        markerPoint.x,
                        markerPoint.y - (offsetPixels / scale)
                    );
                    const offsetLatLng = projection.fromPointToLatLng(offsetPoint);
                    
                    // Open info window at calculated position
                    setTimeout(() => {
                        infoWindow.setPosition(offsetLatLng);
                        infoWindow.open(map);
                    }, 50);
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
            const propertyType = document.getElementById('filterPropertyType').value.toLowerCase();
            const minPrice = parseFloat(document.getElementById('filterMinPrice').value) || 0;
            const maxPrice = parseFloat(document.getElementById('filterMaxPrice').value) || Infinity;
            const couplesAllowed = document.getElementById('filterCouplesAllowed').value.toLowerCase();
            @auth
            const agentName = document.getElementById('filterAgentName')?.value.toLowerCase() || '';
            const payingOnly = document.getElementById('filterPayingOnly')?.checked || false;
            @endauth
            
            let visibleCount = 0;
            
            // Filter markers
            if (window.markers) {
                window.markers.forEach((marker, index) => {
                    const property = window.allProperties[index];
                    if (!property) return;
                    
                    let visible = true;
                    
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
                    
                    // Paying agents only filter (only for authenticated users)
                    if (payingOnly) {
                        const isPaying = property.paying === 'Yes' || 
                                       property.paying === 'yes' || 
                                       property.paying === '1' || 
                                       property.paying === 1 || 
                                       property.paying === true;
                        if (!isPaying) {
                            visible = false;
                        }
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
            document.getElementById('filterPropertyType').value = '';
            document.getElementById('filterMinPrice').value = '';
            document.getElementById('filterMaxPrice').value = '';
            document.getElementById('filterCouplesAllowed').value = '';
            @auth
            const agentFilter = document.getElementById('filterAgentName');
            if (agentFilter) agentFilter.value = '';
            const payingFilter = document.getElementById('filterPayingOnly');
            if (payingFilter) payingFilter.checked = false;
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
