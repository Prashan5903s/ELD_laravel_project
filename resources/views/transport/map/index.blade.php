@extends('transport.layout.index')
@section('main-transport-container')
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            @include('transport.layout.navbar')
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                @include('transport.layout.left-slidebar')
                <!--end::Sidebar-->
                <!--begin::Main-->
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <!--begin::Content wrapper-->
                    <div class="d-flex flex-column flex-column-fluid">
                        <!--begin::Toolbar-->
                        <div id="kt_app_toolbar" class="app-toolbar pt-6 pb-2">
                            <!--begin::Toolbar container-->
                            <div id="kt_app_toolbar_container"
                                class="app-container container-fluid d-flex align-items-stretch">
                                <!--begin::Toolbar wrapper-->
                                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                                    <!--begin::Page title-->
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <!--begin::Title-->
                                        <h1
                                            class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                                            @lang('lang.cMap')</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->

                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('transport.dashboard', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">@lang('lang.home')</a>
                                            </li>

                                            <!--end::Item-->
                                            <!--begin::Item-->

                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="{{ route('view.overview.map', [request()->lang]) }}"
                                                    class="text-muted text-hover-primary">
                                                    @lang('lang.cMap')
                                                </a>
                                            </li>
                                            <!--begin::Item-->

                                            <!--end::Item-->
                                        </ul>
                                        <!--end::Breadcrumb-->
                                    </div>
                                    <!--end::Page title-->
                                </div>
                                <!--end::Toolbar wrapper-->
                            </div>
                            <!--end::Toolbar container-->
                        </div>
                        <!--end::Toolbar-->
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
                                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

                                <!-- Form to submit starting and ending dates -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        {{-- <div class="form-group w-50 mb-4 mt-4"> --}}
                                        <label for="">Choose time period</label>
                                        <select class="form-control" name="timeframe" id="timeframe">
                                            <option value="" disabled selected>Select the time period</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="last_7_days">Last 7 days</option>
                                            <option value="this_month">This month</option>
                                            <option value="last_month">Last month</option>
                                        </select>
                                        {{-- </div> --}}
                                    </div>
                                    <div class="col-md-6">
                                        {{-- <div class="form-group w-50 mb-4 mt-4"> --}}
                                        <label for="">Select driver</label>
                                        <select class="form-control" name="" id="">
                                            <option value="" disabled selected>Select the Driver</option>
                                            @foreach ($driver as $data)
                                                <option value="{{ $data->id }}">{{ $data->first_name }}
                                                    {{ $data->last_name }}</option>
                                            @endforeach
                                        </select>
                                        {{-- </div> --}}
                                    </div>
                                </div>

                                <script>
                                    document.addEventListener("DOMContentLoaded", function() {
                                        // Get the stored selected option from local storage
                                        var selectedOption = localStorage.getItem("selectedOption");
                                        if (selectedOption) {
                                            document.getElementById("timeframe").value = selectedOption;
                                        }
                                    });

                                    document.getElementById('timeframe').addEventListener('change', function() {
                                        var selectedValue = this.value;
                                        var url;

                                        switch (selectedValue) {
                                            case 'today':
                                                url = "{{ route('view.overview.map', [request()->lang]) }}";
                                                break;
                                            case 'yesterday':
                                                var yesterday = new Date();
                                                yesterday.setDate(yesterday.getDate() - 1);
                                                var formattedDate = yesterday.toISOString().slice(0, 10);
                                                url = `{{ route('view.overview.map', [request()->lang]) }}?prev-day=${formattedDate}`;
                                                break;
                                            case 'last_7_days':
                                                // Calculate start and end dates for last 7 days
                                                var endDate = new Date();
                                                var startDate = new Date(endDate);
                                                startDate.setDate(startDate.getDate() - 6); // Subtract 6 days to get 7-day range
                                                var formattedStartDate = startDate.toISOString().slice(0, 10);
                                                var formattedEndDate = endDate.toISOString().slice(0, 10);
                                                url =
                                                    `{{ route('view.overview.map', [request()->lang]) }}?start-date=${formattedStartDate}&end-date=${formattedEndDate}`;
                                                break;
                                            case 'this_month':
                                                // Calculate start and end dates for this month
                                                var today = new Date();
                                                var startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                                                var endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                                                var formattedStartDate = startDate.toISOString().slice(0, 10);
                                                var formattedEndDate = endDate.toISOString().slice(0, 10);
                                                url =
                                                    `{{ route('view.overview.map', [request()->lang]) }}?start-date=${formattedStartDate}&end-date=${formattedEndDate}`;
                                                break;
                                            case 'last_month':
                                                // Calculate start and end dates for last month
                                                var today = new Date();
                                                var startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                                                var endDate = new Date(today.getFullYear(), today.getMonth(), 0);
                                                var formattedStartDate = startDate.toISOString().slice(0, 10);
                                                var formattedEndDate = endDate.toISOString().slice(0, 10);
                                                url =
                                                    `{{ route('view.overview.map', [request()->lang]) }}?start-date=${formattedStartDate}&end-date=${formattedEndDate}`;
                                                break;
                                            default:
                                                break;
                                        }

                                        // Redirect to the constructed URL
                                        if (url) {
                                            // Store the selected option in local storage
                                            localStorage.setItem("selectedOption", selectedValue);
                                            window.location.href = url;
                                        }
                                    });
                                </script>



                                <form id="dateForm" method="GET">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="full-time">
                                                <div class="starting-date form-group">
                                                    <label for="start-date">Starting Date:</label>
                                                    <input type="date" id="start-date" name="start-date"
                                                        class="form-control" onchange="updateUrl()">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="full-time">
                                                <div class="ending-date form-group">
                                                    <label for="end-date">Ending Date:</label>
                                                    <input type="date" id="end-date" name="end-date"
                                                        class="form-control" onchange="updateUrl()">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>


                                <div id="map" class="mt-4" style="width: 100%; height: 620px;"></div>
                                <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                                <script>
                                    $(document).ready(function() {
                                        var map = L.map('map', {
                                            zoomControl: true, // Enable zoom control
                                            maxZoom: 18 // Allow users to zoom in further
                                        }).setView([37.0902, -95.7129], 3); // Initial center and zoom to the US

                                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

                                        // Function to determine the rotation angle based on directionAlpha
                                        function getRotationAngle(direction) {
                                            switch (direction) {
                                                case 'N':
                                                    return 0;
                                                case 'E':
                                                    return 90;
                                                case 'S':
                                                    return 180;
                                                case 'W':
                                                    return 270;
                                                default:
                                                    return 0; // Default to no rotation if direction is not recognized
                                            }
                                        }

                                        // Custom icon for truck marker with rotation
                                        function createTruckIcon(directionAlpha, isStartingPoint) {
                                            var iconUrl = isStartingPoint ? '{{ asset('logo/start_here.png') }}' :
                                                '{{ asset('logo/truck_icon.png') }}';
                                            return L.icon({
                                                iconUrl: iconUrl,
                                                iconSize: [64, 64], // Increase the size of the icon
                                                iconAnchor: [32, 32], // Adjust anchor to center the larger icon
                                                className: isStartingPoint ? 'rotated-icon starting-point-icon' :
                                                    'rotated-icon ending-point-icon', // Add custom class
                                                popupAnchor: [0, -32] // Adjust popup anchor for larger icon
                                            });
                                        }

                                        // Apply rotation to the ending point marker
                                        function applyRotation(marker, angle) {
                                            var iconElement = marker.getElement();
                                            if (iconElement) {
                                                iconElement.style.transform += ` rotate(${angle}deg)`;
                                            }
                                        }

                                        // Initial display of default routeData
                                        var routeData = {!! json_encode($routeData) !!};
                                        var routePolyline;
                                        var startMarker;
                                        var currentMarker;
                                        var initialLoad = true;

                                        displayRouteData(routeData);

                                        // Function to display route data on the map
                                        function displayRouteData(data) {
                                            // Save the current zoom level and center
                                            var currentZoom = map.getZoom();
                                            var currentCenter = map.getCenter();

                                            // Clear previous layers except the starting point
                                            if (routePolyline) {
                                                map.removeLayer(routePolyline);
                                            }
                                            if (currentMarker) {
                                                map.removeLayer(currentMarker);
                                            }

                                            if (data.length > 0) {
                                                // Add polyline representing the route to the map
                                                routePolyline = L.polyline(data.map(function(point) {
                                                    return [point.latitude, point.longitude];
                                                }), {
                                                    color: 'red'
                                                }).addTo(map);

                                                // Add markers and popups for each coordinate
                                                var startPoint = data[0];
                                                var currentPoint = data[data.length - 1];

                                                // If starting marker doesn't exist, create it
                                                if (!startMarker) {
                                                    startMarker = L.marker([startPoint.latitude, startPoint.longitude], {
                                                        icon: createTruckIcon(startPoint.directionAlpha,
                                                            true) // Use starting point icon
                                                    }).addTo(map).bindPopup('Starting Point');
                                                }

                                                // Update the ending point marker
                                                currentMarker = L.marker([currentPoint.latitude, currentPoint.longitude], {
                                                    icon: createTruckIcon(currentPoint.directionAlpha, false) // Use default truck icon
                                                }).addTo(map).bindPopup('Current Place of vehicle');

                                                // Apply rotation to the ending point marker immediately after it is added
                                                var rotationAngle = getRotationAngle(currentPoint.directionAlpha);
                                                applyRotation(currentMarker, rotationAngle);

                                                // Ensure rotation is reapplied after map zoom
                                                map.on('zoomend', function() {
                                                    applyRotation(currentMarker, rotationAngle);
                                                });

                                                if (initialLoad) {
                                                    // Fit the map to display all points and add the polyline on initial load
                                                    var bounds = L.latLngBounds(data.map(function(point) {
                                                        return [point.latitude, point.longitude];
                                                    }));

                                                    // Add a buffer to the bounds to ensure points are clearly visible
                                                    bounds = bounds.pad(0.1);

                                                    map.fitBounds(bounds, {
                                                        padding: [20, 20] // Increase padding to provide more space around points
                                                    });

                                                    initialLoad = false;
                                                } else {
                                                    // Restore the map to the previous zoom level and center
                                                    map.setView(currentCenter, currentZoom);
                                                }
                                            } else {
                                                // If routeData is empty, center the map on the United States
                                                map.setView([37.0902, -95.7129], 3); // Center on US with zoom
                                            }
                                        }

                                        // Track if the user is interacting with the map
                                        var userInteracting = false;
                                        map.on('zoomstart movestart', function() {
                                            userInteracting = true;
                                        });
                                        map.on('zoomend moveend', function() {
                                            setTimeout(function() {
                                                userInteracting = false;
                                            }, 1000);
                                        });

                                        // Dynamic update using SSE
                                        // var source = new EventSource('{{ route('sse.send.index') }}');
                                        // source.onmessage = function(event) {
                                        //     var newData = JSON.parse(event.data);
                                        //     if (newData.length > 0) {
                                        //         displayRouteData(newData);
                                        //     }
                                        // };
                                    });

                                    // Add markers and popups for each coordinate
                                    // routeData.forEach(function(coord) {
                                    //     // Fetch location name using Nominatim API
                                    //     var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + coord[0] + '&lon=' + coord[
                                    //         1];

                                    //     fetch(url)
                                    //         .then(response => response.json())
                                    //         .then(data => {
                                    //             var locationName = data.display_name;
                                    //             // Add marker with popup
                                    //             var marker = L.marker(coord).addTo(map);
                                    //             marker.bindPopup(locationName).openPopup();
                                    //         })
                                    //         .catch(error => console.error('Error:', error));
                                    // });

                                    // Function to update URL with selected dates
                                    function updateUrl() {
                                        var startDate = document.getElementById("start-date").value;
                                        var endDate = document.getElementById("end-date").value;

                                        // Parse dates into JavaScript Date objects
                                        var startDateObj = new Date(startDate);
                                        var endDateObj = new Date(endDate);

                                        // Check if start date is after end date, if so, swap them
                                        if (startDateObj > endDateObj) {
                                            var temp = startDate;
                                            startDate = endDate;
                                            endDate = temp;

                                            document.getElementById("start-date").value = startDate;
                                            document.getElementById("end-date").value = endDate;
                                        }

                                        var url = window.location.href.split("?")[0]; // Get current URL without query parameters
                                        var params = new URLSearchParams();

                                        // Set start-date parameter if it's selected and clear end-date parameter
                                        if (startDate) {
                                            params.set("start-date", startDate);
                                        } else {
                                            params.delete("start-date");
                                        }

                                        // Set end-date parameter if it's selected and clear start-date parameter
                                        if (endDate) {
                                            params.set("end-date", endDate);
                                        } else {
                                            params.delete("end-date");
                                        }

                                        // Update URL without reloading the page
                                        window.history.replaceState({}, '', `${url}?${params}`);

                                        location.reload(); // This line is commented out to prevent page reload
                                    }

                                    // Function to parse URL parameters and set input values
                                    function setInitialDateValues() {
                                        var urlParams = new URLSearchParams(window.location.search);
                                        var startDate = urlParams.get("start-date");
                                        var endDate = urlParams.get("end-date");

                                        // Set input values based on URL parameters
                                        document.getElementById("start-date").value = startDate;
                                        document.getElementById("end-date").value = endDate;
                                    }

                                    // Function to clear the selected dates
                                    function clearDates() {
                                        window.location.href = "{{ route('view.overview.map', [request()->lang]) }}";
                                    }



                                    // Call setInitialDateValues() function to set input values on page load
                                    window.addEventListener("load", setInitialDateValues);
                                </script>
                                <style>
                                    /* Custom CSS for rotating icon */
                                    .rotated-icon {
                                        transform-origin: center;
                                        transform-box: fill-box;
                                    }

                                    .starting-point-icon {
                                        /* Add any specific styles for the starting point icon here */
                                    }

                                    .ending-point-icon {
                                        /* Add any specific styles for the ending point icon here */
                                    }
                                </style>
                            </div>
                            <!--end::Content container-->
                        </div>
                        <!--end::Content-->
                    </div>
                    <!--end::Content wrapper-->
                </div>
                <!--end:::Main-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::App-->
@endsection
