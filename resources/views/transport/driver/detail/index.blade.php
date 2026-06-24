@extends('transport.layout.index')
@section('main-transport-container')
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
                        <!--begin::Content-->
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <!--begin::Content container-->
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                <!--begin::Layout-->
                                <div class="d-flex flex-column flex-lg-row">
                                    <!--begin::Sidebar-->
                                    <div class="flex-column flex-lg-row-auto w-100 w-lg-300px w-xl-400px mb-10 mb-lg-0">
                                        <!--begin::Contacts-->
                                        <div class="card card-flush">
                                            <!--begin::Card header-->
                                            <div class="card-header pt-7" id="kt_chat_contacts_header">
                                                <!--begin::Form-->
                                                <a href="{{ route('driver.index', [request()->lang]) }}">
                                                    <div class="btn btn-light-primary">Back</div>
                                                </a>
                                                <!--end::Form-->
                                            </div>
                                            <!--end::Card header-->
                                            <!--begin::Card body-->
                                            <div class="card-body pt-5" id="kt_chat_contacts_body">
                                                <!--begin::List-->
                                                <div class="scroll-y me-n5 pe-5 h-200px h-lg-auto" data-kt-scroll="true"
                                                    data-kt-scroll-activate="{default: false, lg: true}"
                                                    data-kt-scroll-max-height="auto"
                                                    data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_toolbar, #kt_app_toolbar, #kt_footer, #kt_app_footer, #kt_chat_contacts_header"
                                                    data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_contacts_body"
                                                    data-kt-scroll-offset="5px">
                                                    <!--begin::User-->
                                                    <div class="py-4">
                                                        <!--begin:: Vehicle Details-->
                                                        <div class="fs-1 fw-bold text-gray-900 mb-2">
                                                            {{ $driver->first_name }} {{ $driver->last_name }}
                                                        </div>
                                                        <div class="fw-semibold text-muted mb-2"><i class="fa fa-phone"></i>
                                                            {{ $driver->mobile_no }}
                                                        </div>
                                                        <div class="fw-semibold text-muted mb-2"><i class='fas fa-car'></i>
                                                            {{ $vechile->name }}
                                                        </div>
                                                        <div class="fw-semibold text-muted mb-2"><i
                                                                class='fas fa-map-marker'></i> {{ $driver->address }}</div>
                                                        <!--end:: Vehicle Details-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="separator separator-dashed"></div>
                                                    <div class="py-4">
                                                        <!--begin:: Vehicle Details-->
                                                        <div class="fs-3 fw-bold text-gray-900 mb-2">Details</div>
                                                        <div class="d-flex flex-stack">
                                                            <div class="fw-semibold text-muted mb-2">Driver License</div>
                                                            <div class="fw-semibold text-muted mb-2">
                                                                {{ $userInfo->licenseNumber }}
                                                            </div>
                                                        </div>
                                                        <!--end:: Vehicle Details-->
                                                    </div>
                                                    <!--end::User-->
                                                    <!--begin::Separator-->
                                                    <div class="separator separator-dashed"></div>
                                                    <div class="container py-4 ">
                                                        <!--begin::Vehicle Details-->
                                                        <div class="fs-3 fw-bold text-gray-900 mb-2">Hours of Service</div>
                                                        <div class="d-flex justify-content-between mb-4 mt-4">
                                                            <div class="fw-semibold text-muted mb-2">Log</div>
                                                            <a href="#" id="viewLogLink"
                                                                class="view-log-details fw-semibold text-muted mb-2">View
                                                                Log
                                                                Details</a>
                                                        </div>
                                                        <style>
                                                            /* Custom CSS to vertically center the modal */
                                                            .modal-dialog-centered {
                                                                display: flex;
                                                                align-items: center;
                                                                min-height: calc(100% - 1rem);
                                                            }

                                                            .modal-content {
                                                                margin: auto;
                                                            }

                                                            /* Increase the size of the new modal */
                                                            .modal-dialog-lg {
                                                                max-width: 50%;
                                                                /* Increase width */
                                                                max-height: 80%;
                                                                /* Increase height */
                                                            }

                                                            /* Remove black border around the close icon */
                                                            .close {
                                                                border: none;
                                                                outline: none;
                                                            }

                                                            .close:hover,
                                                            .close:focus {
                                                                color: black;
                                                                text-decoration: none;
                                                                cursor: pointer;
                                                                border: none;
                                                                outline: none;
                                                            }

                                                            /* Style for log entries */
                                                            .log-entry {
                                                                display: flex;
                                                                justify-content: space-between;
                                                                align-items: center;
                                                                padding: 10px;
                                                                cursor: pointer;
                                                                border-bottom: 1px solid #ddd;
                                                            }

                                                            .log-entry:hover {
                                                                background-color: #f1f1f1;
                                                            }

                                                            .log-entry .arrow {
                                                                font-size: 1.2em;
                                                            }
                                                        </style>
                                                        <div class="modal fade" id="logModal" tabindex="-1" role="dialog"
                                                            aria-labelledby="logModalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="logModalLabel">Log
                                                                            History</h5>
                                                                        <button type="button" class="close"
                                                                            aria-label="Close" data-dismiss="modal">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <!-- Log entries -->
                                                                        @foreach ($logDetails as $value)
                                                                            <div class="log-entry"
                                                                                data-log-id="{{ $value['date'] }}"
                                                                                data-log-driver="{{ $driver->id }}"
                                                                                data-toggle="modal"
                                                                                data-target="#logEntryModal">
                                                                                <span>{{ $value['date'] }}
                                                                                    <br />
                                                                                    @if ($value['total_time'] == '00hr 00min')
                                                                                        <div
                                                                                            class="d-flex fs-8 gap-14 align-items-center">
                                                                                            <span class="w-100px">
                                                                                                <i
                                                                                                    class="bg-success fs-9 ki-check ki-outline rounded text-bg-info"></i>
                                                                                                <i
                                                                                                    class="ki-left ki-outline text-success"></i>
                                                                                                <span
                                                                                                    class="text-success">1m</span>
                                                                                            </span>
                                                                                            <span>
                                                                                                <i
                                                                                                    class="bg-danger fs-9 ki-cross ki-outline rounded text-bg-info"></i>
                                                                                                <span
                                                                                                    class="text-danger">Form</span>
                                                                                            </span>
                                                                                            <span>
                                                                                                <i
                                                                                                    class="bg-danger fs-9 ki-cross ki-outline rounded text-bg-info"></i>
                                                                                                <span
                                                                                                    class="text-danger">Clarity</span>

                                                                                            </span>
                                                                                        </div>
                                                                                    @else
                                                                                        <div
                                                                                            class="d-flex gap-14 fs-8 align-items-center">
                                                                                            <span class="w-100px">
                                                                                                <i
                                                                                                    class="bg-success fs-9 ki-check ki-outline rounded text-bg-info"></i>
                                                                                                <span
                                                                                                    class="text-success">{{ $value['total_time'] }}</span>
                                                                                            </span>
                                                                                            <span>
                                                                                                <i
                                                                                                    class="bg-danger fs-9 ki-cross ki-outline rounded text-bg-info"></i>
                                                                                                <span
                                                                                                    class="text-danger">Form</span>
                                                                                            </span>
                                                                                            <span>
                                                                                                <i
                                                                                                    class="bg-danger fs-9 ki-cross ki-outline rounded text-bg-info"></i>
                                                                                                <span
                                                                                                    class="text-danger">Clarity</span>

                                                                                            </span>
                                                                                        </div>
                                                                                    @endif
                                                                                </span>
                                                                                <span class="arrow">&rarr;</span>

                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Log Entry Details Modal -->
                                                        <div class="modal fade" id="logEntryModal" tabindex="-1"
                                                            role="dialog" aria-labelledby="logEntryModalLabel"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-lg"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="logEntryModalLabel">
                                                                            Log Entry Details
                                                                        </h5>
                                                                        <button type="button" class="close"
                                                                            aria-label="Close" data-dismiss="modal">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div id="logEntriesContainer"></div>
                                                                        <!-- Container for log entries -->
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <!-- jQuery, Popper.js, and Bootstrap JS -->
                                                        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
                                                        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                                                        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                                                        <script>
                                                            $(document).ready(function() {
                                                                // When the link is clicked, open the main modal without reloading the page
                                                                document.getElementById('viewLogLink').addEventListener('click', function(event) {
                                                                    event.preventDefault();
                                                                    $('#logModal').modal('show');
                                                                });

                                                                // Close main modal on clicking close icon or close button
                                                                $('#logModal .close, #logModal .btn-secondary').on('click', function() {
                                                                    $('#logModal').modal('hide');
                                                                });

                                                                $('.log-entry').click(function() {
                                                                    var logId = $(this).data('log-id');
                                                                    var driver_id = $(this).data('log-driver');
                                                                    // AJAX request to fetch data
                                                                    $.ajax({
                                                                        url: '{{ route('data.dates.index') }}',
                                                                        type: 'GET',
                                                                        data: {
                                                                            logId: logId,
                                                                            driver_id: driver_id
                                                                        },
                                                                        success: function(response) {
                                                                            // Clear previous entries
                                                                            $('#logEntriesContainer').empty();

                                                                            if (response.length === 0) {
                                                                                // Show message if response array is empty
                                                                                $('#logEntriesContainer').text('No data received');
                                                                            } else {
                                                                                // Loop through response and create log entry elements
                                                                                response.forEach(function(log) {
                                                                                    var statusText;
                                                                                    var color;
                                                                                    switch (log.status) {
                                                                                        case 1:
                                                                                            statusText = 'Off';
                                                                                            color = '#A9A9A9'; // Dark grey
                                                                                            break;
                                                                                        case 2:
                                                                                            statusText = 'SB';
                                                                                            color = '#000000'; // Black
                                                                                            break;
                                                                                        case 3:
                                                                                            statusText = 'D';
                                                                                            color = '#008000'; // Dark green
                                                                                            break;
                                                                                        case 4:
                                                                                            statusText = 'ON';
                                                                                            color = '#ADD8E6'; // Light blue
                                                                                            break;
                                                                                        default:
                                                                                            statusText = 'Unknown';
                                                                                            color = '#FFFFFF'; // Default color (white)
                                                                                    }

                                                                                    var logEntry = `
                                                                                        <div class="log-entry d-flex justify-content-between">
                                                                                            <span class="align-items-center d-flex gap-2 w-150px">
                                                                                                <div style="width: 4px; height: 15px; background-color: ${color};"></div>
                                                                                                ${statusText}
                                                                                            </span>
                                                                                            <span class="w-150px ml-1">${log.created}</span>
                                                                                            <span class="w-200px ml-1">${log.time}</span>
                                                                                        </div>
                                                                                    `;

                                                                                    $('#logEntriesContainer').append(logEntry);
                                                                                });
                                                                            }

                                                                            // Show the modal
                                                                            $('#logEntryModal').modal('show');
                                                                        },

                                                                        error: function(xhr) {
                                                                            console.error('Error fetching log details:', xhr.statusText);
                                                                        }
                                                                    });
                                                                });
                                                                // Close log entry details modal on clicking close icon or close button
                                                                $('#logEntryModal .close, #logEntryModal .btn-secondary').on('click', function() {
                                                                    $('#logEntryModal').modal('hide');
                                                                });
                                                            });
                                                        </script>
                                                        <!--begin:: HOS Map-->

                                                        <!--end::HOS Map-->
                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Duty status</div>
                                                            @if (isset($curr_stat) && $curr_stat)
                                                                @php
                                                                    $statuses = [
                                                                        'Off' => [
                                                                            'color' => 'white',
                                                                            'background' => 'grey',
                                                                        ],
                                                                        'ON' => [
                                                                            'color' => 'white',
                                                                            'background' => 'orange',
                                                                        ],
                                                                        'Driving' => [
                                                                            'color' => 'white',
                                                                            'background' => 'green',
                                                                        ],
                                                                        'SB' => [
                                                                            'color' => 'white',
                                                                            'background' => 'black',
                                                                        ],
                                                                    ];
                                                                @endphp
                                                                @if (isset($curr_stat->title) && array_key_exists($curr_stat->title, $statuses))
                                                                    <div class="fw-semibold text-muted mb-2">
                                                                        <span class="badge badge-secondary"
                                                                            style="background-color: {{ $statuses[$curr_stat->title]['background'] }}; color: {{ $statuses[$curr_stat->title]['color'] }};">
                                                                            {{ $curr_stat->title }}
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="fw-semibold text-muted mb-2">
                                                                    @if (!isset($curr_stat->title))
                                                                        <span class="badge badge-secondary"
                                                                            style="background-color: black; color: white;">
                                                                            Off
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Time in current
                                                                status
                                                            </div>
                                                            <div class="fw-semibold text-muted mb-2">
                                                                {{ $timeDifference }}</div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Time untill break
                                                            </div>
                                                            <div class="fw-semibold text-muted mb-2">
                                                                {{ $breakTimeFormatted }}</div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Drive remaining</div>
                                                            <div class="fw-semibold text-muted mb-2">
                                                                {{ $remainingDriveTimeFormatted }}</div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Shift remaining</div>
                                                            <div class="fw-semibold text-muted mb-2">
                                                                {{ $shiftRemainingFormat }}</div>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <div class="fw-semibold text-muted mb-2">Cycle remaining</div>
                                                            <div class="fw-semibold text-muted mb-2">{{ $remainingTime }}
                                                            </div>
                                                        </div>
                                                        {{--
                                       <div class="d-flex justify-content-between">
                                          <div class="fw-semibold text-muted mb-2">Cycle tommorow</div>
                                          <div class="fw-semibold text-muted mb-2">00:00</div>
                                       </div>
                                       --}}
                                                        <!--end::Vehicle Details-->
                                                    </div>
                                                    <!--end::User-->
                                                </div>
                                                <!--end::List-->
                                            </div>
                                            <!--end::Card body-->
                                        </div>
                                    </div>
                                    <!--end::Sidebar-->
                                    <!--begin::Content-->
                                    <div class="flex-lg-row-fluid ms-lg-7 ms-xl-10">
                                        <!--begin::Messenger-->
                                        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
                                        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                                        <div id="map" class="mt-4"
                                            style="width: 100%; height: calc(100vh - 144px ); margin:0;"></div>
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

                                                function displayLocationPopup(latlng) {
                                                    var url = 'https://nominatim.openstreetmap.org/reverse?format=json&lat=' + latlng.lat + '&lon=' +
                                                        latlng.lng;

                                                    fetch(url)
                                                        .then(response => response.json())
                                                        .then(data => {
                                                            var locationName = data.display_name;
                                                            var popupContent = "<div class='popup-content'><div class='lat-lng'><b>Latitude:</b> " +
                                                                latlng.lat + "<br><b>Longitude:</b> " + latlng.lng +
                                                                "</div><div class='location-name'>" + locationName + "</div></div>";
                                                            L.popup()
                                                                .setLatLng(latlng)
                                                                .setContent(popupContent)
                                                                .openOn(map);
                                                        })
                                                        .catch(error => console.error('Error:', error));
                                                }

                                                map.on('click', function(e) {
                                                    var latlng = e.latlng;
                                                    displayLocationPopup(latlng);
                                                });



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
                                                var source = new EventSource('{{ route('sse.send.index') }}');
                                                source.onmessage = function(event) {
                                                    var newData = JSON.parse(event.data);
                                                    if (newData.length > 0) {
                                                        displayRouteData(newData);
                                                    }
                                                };
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

                                            .popup - content {
                                                width: 200 px;
                                            }

                                            .lat - lng {
                                                margin - bottom: 5 px;
                                            }

                                            .location - name {
                                                font - weight: bold;
                                            }
                                        </style>
                                        <!--end::Messenger-->
                                    </div>
                                    <!--end::Content-->
                                </div>
                                <!--end::Layout-->
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
