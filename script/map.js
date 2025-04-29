let map;
let autocomplete;
let directionsService;
let directionsRenderer;
let userMarker;
let markers = []; // Store hospital markers

function initMap() {
    // Create the map centered on Nairobi
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -1.286389, lng: 36.817223 },
        zoom: 12
    });
    
    // Initialize Directions Service and Renderer
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer();
    directionsRenderer.setMap(map);

    // Autocomplete search
    const input = document.getElementById('pac-input');
    autocomplete = new google.maps.places.Autocomplete(input);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry) {
            console.log("No details available for the input: '" + place.name + "'");
            return;
        }

        map.setCenter(place.geometry.location);
        map.setZoom(17);

        new google.maps.Marker({
            position: place.geometry.location,
            map: map
        });
    });

    // Button to get user location
    document.getElementById('locate-me').addEventListener('click', getUserLocation);

    // Fetch hospitals and display markers
    fetchHospitals();
}

// Function to get user location
function getUserLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                if (userMarker) {
                    userMarker.setMap(null);
                }

                userMarker = new google.maps.Marker({
                    position: userLocation,
                    map: map,
                    title: "Your Location",
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                });

                map.setCenter(userLocation);
                map.setZoom(14);
            },
            () => {
                alert("Geolocation failed. Please allow location access.");
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

// Function to fetch hospitals and display them
function fetchHospitals() {
    fetch('fetch_hospitals.php') // Ensure this returns JSON data
        .then(response => response.json())
        .then(data => {
            data.forEach(hospital => {
                let marker = new google.maps.Marker({
                    position: { lat: parseFloat(hospital.latitude), lng: parseFloat(hospital.longitude) },
                    map: map,
                    title: hospital.name
                });

                markers.push(marker);

                let infoWindow = new google.maps.InfoWindow({
                    content: `<strong>${hospital.name}</strong><br>${hospital.location}
                              <br><button onclick="getDirections(${hospital.latitude}, ${hospital.longitude})">Get Directions</button>`
                });

                marker.addListener("click", () => {
                    infoWindow.open(map, marker);
                });
            });
        })
        .catch(error => console.error('Error fetching hospitals:', error));
}

function getDirections(lat, lng) {
    if (!userMarker) {
        alert("Please enable location first.");
        return;
    }

    const travelMode = document.getElementById("travel-mode").value; // Get selected travel mode

    const request = {
        origin: userMarker.getPosition(),
        destination: { lat: lat, lng: lng },
        travelMode: google.maps.TravelMode[travelMode] // Use selected mode
    };

    directionsService.route(request, (result, status) => {
        if (status === google.maps.DirectionsStatus.OK) {
            directionsRenderer.setDirections(result);
        } else {
            alert("Could not get directions.");
        }
    });
}
