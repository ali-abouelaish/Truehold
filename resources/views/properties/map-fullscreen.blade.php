<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Map - Full Screen - LET CONNECT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .fullscreen-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
        }
        
        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .nav-left {
            display: flex;
            align-items: center;
            gap: 2rem;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            text-decoration: none;
        }
        
        .nav-link {
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        
        .nav-link:hover {
            color: #1f2937;
            background: rgba(0, 0, 0, 0.05);
        }
        
        .nav-button {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .nav-button:hover {
            background: #2563eb;
        }
        
        .map-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        .property-count {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 1.125rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .loading-subtext {
            font-size: 0.875rem;
            opacity: 0.8;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .fullscreen-nav {
                padding: 0.75rem 1rem;
            }
            
            .nav-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-left, .nav-right {
                width: 100%;
                justify-content: center;
            }
            
            .nav-link, .nav-button {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loadingScreen" class="loading-screen">
        <div class="loading-spinner"></div>
        <div class="loading-text">Loading Map</div>
        <div class="loading-subtext">Preparing your property search...</div>
    </div>

    <!-- Full Screen Navigation -->
    <nav class="fullscreen-nav">
        <div class="nav-content">
            <div class="nav-left">
                <a href="{{ route('properties.index') }}" class="logo">
                    <i class="fas fa-building mr-2"></i>LET CONNECT
                </a>
                <div class="property-count">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    <span id="propertyCount">Loading properties...</span>
                </div>
            </div>
            <div class="nav-right">
                <a href="{{ route('properties.map') }}" class="nav-link">
                    <i class="fas fa-filter mr-1"></i>Filtered Map
                </a>
                <a href="{{ route('properties.index') }}" class="nav-link">
                    <i class="fas fa-list mr-1"></i>List View
                </a>
                @auth
                <a href="{{ route('admin.dashboard') }}" class="nav-link">
                    <i class="fas fa-cog mr-1"></i>Admin
                </a>
                @else
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Map Container -->
    <div class="map-container">
        <div id="map"></div>
    </div>

    <!-- Properties Data -->
    <div id="properties-data" style="display: none;">{!! json_encode($properties) !!}</div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key', 'YOUR_GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap&v=weekly&loading=async"></script>
    
    <script>
        // Global variables
        let map;
        let markers = [];
        let infoWindow;
        let properties = [];
        let filteredProperties = [];
        let agentStats = {};
        let agentColors = {};

        // Initialize map when Google Maps API loads
        function initMap() {
            try {
                console.log('🗺️ Initializing full screen map...');
                
                // Hide loading screen
                document.getElementById('loadingScreen').style.display = 'none';
                
                // Create map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 51.5074, lng: -0.1278 }, // London center
                    zoom: 12,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    styles: [
                        {
                            featureType: 'poi',
                            elementType: 'labels',
                            stylers: [{ visibility: 'off' }]
                        }
                    ]
                });
                
                // Create info window
                infoWindow = new google.maps.InfoWindow();

                // Load properties
                loadProperties();
                
            } catch (error) {
                console.error('❌ Error initializing map:', error);
                document.getElementById('loadingScreen').innerHTML = 
                    '<div class="loading-text">Error loading map</div>' +
                    '<div class="loading-subtext">Please refresh the page</div>';
            }
        }

        // Load properties from server
        function loadProperties() {
            try {
                const propertiesData = document.getElementById('properties-data').textContent;
                properties = JSON.parse(propertiesData);
                filteredProperties = [...properties];
                
                console.log(`📍 Loaded ${properties.length} properties`);
                
                // Update property count
                document.getElementById('propertyCount').textContent = `${properties.length} properties`;
                
                // Create markers
                createMarkers();
                
            } catch (error) {
                console.error('❌ Error loading properties:', error);
            }
        }

        // Create markers for all properties
        function createMarkers() {
            // Clear existing markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];
            
            filteredProperties.forEach(property => {
                if (property.latitude && property.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(property.latitude), lng: parseFloat(property.longitude) },
                        map: map,
                        title: property.title || 'Property',
                        icon: {
                            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                                <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                    <circle cx="16" cy="16" r="12" fill="#3b82f6" stroke="white" stroke-width="2"/>
                                    <text x="16" y="20" text-anchor="middle" fill="white" font-family="Arial" font-size="12" font-weight="bold">£</text>
                                </svg>
                            `),
                            scaledSize: new google.maps.Size(32, 32),
                            anchor: new google.maps.Point(16, 16)
                        }
                    });
                    
                    // Add click listener
                    marker.addListener('click', () => {
                        showPropertyInfo(property, marker);
                    });
                    
                    markers.push(marker);
                }
            });
            
            console.log(`📍 Created ${markers.length} markers`);
        }

        // Show property information
        function showPropertyInfo(property, marker) {
            const content = `
                <div class="property-info" style="max-width: 300px;">
                    <div style="margin-bottom: 12px;">
                        <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937;">
                            ${property.title || 'Property'}
                        </h3>
                        <p style="margin: 0 0 8px 0; color: #6b7280; font-size: 14px;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i>
                            ${property.location || 'Location not specified'}
                        </p>
                        <p style="margin: 0 0 12px 0; font-size: 18px; font-weight: 700; color: #3b82f6;">
                            ${property.price || 'Price not available'}
                        </p>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="/properties/${property.id}" 
                           style="background: #3b82f6; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 14px; font-weight: 500;"
                           onmouseover="this.style.background='#2563eb'"
                           onmouseout="this.style.background='#3b82f6'">
                            View Details
                        </a>
                    </div>
                </div>
            `;
            
            infoWindow.setContent(content);
            infoWindow.open(map, marker);
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        });
    </script>
</body>
</html>
