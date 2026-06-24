
<?php $__env->startSection('main-section'); ?>
    <!--end::Theme mode setup on page load-->
    <!--begin::App-->
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <!--begin::Page-->
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <!--begin::Header-->
            <?php echo $__env->make('super-admin.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!--end::Header-->
            <!--begin::Wrapper-->
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <!--begin::Sidebar-->
                <?php echo $__env->make('super-admin.layout.left-slidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
                                            User List</h1>
                                        <!--end::Title-->
                                        <!--begin::Breadcrumb-->
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="<?php echo e(url('/')); ?>" class="text-muted text-hover-primary">Home</a>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <!--end::Item-->
                                            <!--begin::Item-->
                                            <li class="breadcrumb-item text-muted">
                                                <a href="<?php echo e(route('user.view')); ?>" class="text-muted text-hover-primary">
                                                    View User
                                                </a>

                                            </li>
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
                                <!--begin::Card-->
                                <div class="card">
                                    <!--begin::Card header-->
                                    <div class="card-header border-0 pt-6">
                                        <!--begin::Card title-->
                                        <div class="card-title">
                                            <!--begin::Search-->
                                            <div class="d-flex align-items-center position-relative my-1">
                                                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-5"></i>
                                                <input type="text" data-kt-user-table-filter="search"
                                                    class="form-control form-control-solid w-250px ps-13"
                                                    placeholder="Search user" />
                                            </div>
                                            <!--end::Search-->
                                        </div>
                                        <!--begin::Card title-->
                                        <!--begin::Card toolbar-->
                                        <div class="card-toolbar">
                                            <!--begin::Group actions-->
                                            <div class="d-flex justify-content-end align-items-center d-none"
                                                data-kt-user-table-toolbar="selected">
                                                <div class="fw-bold me-5">
                                                    <span class="me-2"
                                                        data-kt-user-table-select="selected_count"></span>Selected
                                                </div>
                                                <button type="button" class="btn btn-danger"
                                                    data-kt-user-table-select="delete_selected">Delete Selected</button>
                                            </div>
                                            <!--end::Group actions-->
                                            <!--begin::Modal - Adjust Balance-->
                                            <div class="modal fade" id="kt_modal_export_users" tabindex="-1"
                                                aria-hidden="true">
                                                <!--begin::Modal dialog-->
                                                <div class="modal-dialog modal-dialog-centered mw-650px">
                                                    <!--begin::Modal content-->

                                                    <!--end::Modal content-->
                                                </div>
                                                <!--end::Modal dialog-->
                                            </div>
                                            <!--end::Modal - New Card-->
                                            <!--begin::Modal - Add task-->

                                            <!--end::Modal - Add task-->
                                        </div>
                                        <!--end::Card toolbar-->
                                    </div>
                                    <!--end::Card header-->
                                    <!--begin::Card body-->

                                    <div class="card-body py-4">
                                        <!--begin::Table-->
                                        <div class="table-responsive" id="userTable">
                                            <table class="table align-middle table-row-dashed fs-6 gy-5"
                                                id="kt_table_users">
                                                <thead>
                                                    <tr class=" fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-100px"><i class='far fa-arrow-alt-circle-up'></i>
                                                        </th>
                                                        <th class="min-w-100px">User</th>
                                                        <th class="min-w-100px">User type</th>
                                                        <th class="min-w-100px">Mobile no</th>
                                                        <th class="min-w-100px">Joined Date</th>
                                                        <th class="min-w-100px">Login</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600 fw-semibold">
                                                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr class="parent-item parent-row"data-user-id="<?php echo e($user->id); ?>"
                                                            data-user-master-id="<?php echo e($user->master_id); ?>">
                                                            <td class=" row-all"><i class="fa fa-angle-up"></i></td>
                                                            <td><?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?> </td>
                                                            <td>
                                                                <?php if($user->user_type == 'WC'): ?>
                                                                    White label company
                                                                <?php endif; ?>
                                                                <?php if($user->user_type == 'EC'): ?>
                                                                    Company
                                                                <?php endif; ?>
                                                                <?php if($user->user_type == 'RS'): ?>
                                                                    Reseller
                                                                <?php endif; ?>
                                                                <?php if($user->user_type == 'TR'): ?>
                                                                    Transport company
                                                                <?php endif; ?>
                                                                <?php if($user->user_type == 'U'): ?>
                                                                    User / Driver
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo e(trim(explode(',', $user->country_code)[0])); ?> <?php echo e($user->mobile_no); ?></td>
                                                            <td><?php echo e(\Carbon\Carbon::parse($user->created_at)->format('h:i A d-m-Y')); ?>

                                                            </td>
                                                            <!-- Button to trigger modal -->
                                                            <td>
                                                                <button class="btn btn-primary shadowLoginBtn"
                                                                    data-user-type="<?php echo e($user->user_type); ?>"
                                                                    data-user-id="<?php echo e($user->id); ?>">
                                                                    <i class="fa fa-sign-in"></i>
                                                                </button>
                                                            </td>

                                                            <div class="modal fade" id="shadowLoginModal" tabindex="-1"
                                                                aria-labelledby="shadowLoginModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="shadowLoginModalLabel">
                                                                                Shadow Login Confirmation
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <i class="fas fa-exclamation-circle"
                                                                                style="font-size: 48px; color: red;"></i>
                                                                            <p style="margin-top: 10px">Are you sure you
                                                                                want to do shadow login?</p>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button"
                                                                                class="btn btn-primary shadowLogin_btn"
                                                                                id="shadowLoginYes"
                                                                                data-user-type="<?php echo e($user->user_type); ?>"
                                                                                data-user-id="<?php echo e($user->id); ?>">Yes,
                                                                                Shadow
                                                                                Login</button>
                                                                            <button type="button" class="btn btn-secondary"
                                                                                id="shadowLoginCancel"
                                                                                data-bs-dismiss="modal">Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Confirmation Modal -->
                                                            <div class="modal fade" id="confirmationModal" tabindex="-1"
                                                                aria-labelledby="confirmationModalLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="confirmationModalLabel">
                                                                                Confirmation
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-bs-dismiss="modal"
                                                                                aria-label="Close"></button>
                                                                        </div>
                                                                        <div class="modal-body text-center">
                                                                            <i class="fa fa-close"
                                                                                style="font-size: 48px; color: red;"></i>
                                                                            <p>Shadow login not happening</p>
                                                                        </div>
                                                                        <div class="modal-footer justify-content-center">
                                                                            <button type="button" class="btn btn-primary"
                                                                                id="okGotIt" data-bs-dismiss="modal">Ok,
                                                                                got it</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </tr>
                                                        <?php echo $__env->make('super-admin.layout.user_row', [
                                                            'users' => $user->descendants,
                                                        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </tbody>
                                            </table>
                                            <style>
                                                /* Custom CSS for rectangular shape */
                                                .modal-content {
                                                    border-radius: 0;
                                                }

                                                /* Center modal vertically and horizontally */
                                                /* Custom CSS for centering modals */
                                                .modal-dialog {
                                                    display: flex;
                                                    justify-content: center;
                                                    align-items: center;
                                                    min-height: calc(100vh - 60px);
                                                    /* Adjust according to your layout */
                                                }

                                                .modal-content {
                                                    width: 80%;
                                                    /* Adjust as needed */
                                                }

                                                .modal-content {
                                                    border-radius: 15px;
                                                    /* Add some curvature */
                                                    border: none;
                                                    /* Remove border */
                                                }

                                                .modal-header .btn-close {
                                                    display: none;
                                                }
                                            </style>

                                            <!-- Include jQuery library -->
                                            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                                            <!-- Include Bootstrap bundle -->
                                            <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha1/js/bootstrap.bundle.min.js"></script>

                                            <!-- JavaScript for modal functionality -->
                                            <script>
                                                $(document).ready(function() {
                                                    // Event listener for Shadow Login button click
                                                    $('.shadowLoginBtn').on('click', function() {
                                                        // Retrieve user type and user ID from the clicked button's data attributes
                                                        var userType = $(this).data('user-type');
                                                        var userId = $(this).data('user-id');


                                                        // Populate the modal with the correct user information
                                                        $('#shadowLoginYes').data('user-type', userType);
                                                        $('#shadowLoginYes').data('user-id', userId);

                                                        $('#shadowLoginModal').modal('show'); // Show the shadow login modal
                                                    });


                                                    $('#shadowLoginYes').on('click', function() {

                                                        var userType = $(this).data('user-type');
                                                        var userId = $(this).data('user-id');

                                                        var routeUrl =
                                                            "<?php echo e(route('admin.user.change', ['user_type_placeholder', 'user_id_placeholder'])); ?>";
                                                        routeUrl = routeUrl.replace('user_type_placeholder', userType);
                                                        routeUrl = routeUrl.replace('user_id_placeholder', userId);

                                                        // // Redirect to the specified route
                                                        window.location.href = routeUrl;
                                                    });

                                                    // Event listener for Cancel button click
                                                    $('#shadowLoginCancel').on('click', function() {
                                                        $('#shadowLoginModal').modal('hide'); // Hide the shadow login modal
                                                        $('#confirmationModal').modal('show'); // Show the confirmation modal
                                                    });

                                                    // Event listener for Ok, got it button click
                                                    $('#okGotIt').on('click', function() {
                                                        $('#confirmationModal').modal('hide'); // Hide the confirmation modal
                                                    });
                                                });
                                            </script>

                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {

                                                    var noUserShown = false;
                                                    var noUserRow = null;
                                                    var user_Id = null;
                                                    var child_Rows;
                                                    var rowVal = null;
                                                    var noUserRow = document.createElement('tr');

                                                    const rows = document.querySelectorAll('.row-all');
                                                    var allRows = document.querySelectorAll('#kt_table_users tbody tr');

                                                    allRows.forEach(function(row) {


                                                        row.addEventListener('click', function(event) {


                                                            var val_current = event.currentTarget;
                                                            var existingNoUserRow = document.getElementById('noUserRow');

                                                            user_Id = val_current.getAttribute('data-user-id');


                                                            child_Rows = document.querySelectorAll('tr[data-user-master-id="' + user_Id +
                                                                '"]');

                                                            const arrowIcon = row.querySelector('.fa');

                                                            arrowIcon.classList.toggle('fa-angle-up');

                                                            arrowIcon.classList.toggle('fa-angle-down');
                                                            if (child_Rows.length == 0) {
                                                                toastr.options = {
                                                                    "closeButton": false,
                                                                    "debug": false,
                                                                    "newestOnTop": false,
                                                                    "progressBar": false,
                                                                    "positionClass": "toastr-top-center",
                                                                    "preventDuplicates": false,
                                                                    "onclick": null,
                                                                    "showDuration": "300",
                                                                    "hideDuration": "500",
                                                                    "timeOut": "1000",
                                                                    "extendedTimeOut": "1000",
                                                                    "showEasing": "swing",
                                                                    "hideEasing": "linear",
                                                                    "showMethod": "fadeIn",
                                                                    "hideMethod": "fadeOut"
                                                                };

                                                                toastr.error("No User Created!");
                                                                arrowIcon.classList.toggle('fa-angle-up');
                                                                // alert('No user created')
                                                            }


                                                            if (event.target.tagName.toLowerCase() === 'td') {

                                                                var userId = this.getAttribute('data-user-id');

                                                                function findMatchingRows(userId) {

                                                                    var childRows = document.querySelectorAll('tr[data-user-master-id="' +
                                                                        userId + '"]');

                                                                    childRows.forEach(function(row) {

                                                                        var child_id = row.getAttribute('data-user-id');

                                                                        findMatchingRows(child_id);
                                                                    });
                                                                }

                                                                findMatchingRows(userId);
                                                            }
                                                        });
                                                    });

                                                    var clickCounts = {};

                                                    allRows.forEach(function(row) {
                                                        // Initialize click count for each row to 0
                                                        clickCounts[row.getAttribute('data-user-id')] = 0;

                                                        row.addEventListener('click', function(event) {
                                                            // Check if the clicked element is a td
                                                            if (event.target.tagName.toLowerCase() === 'td') {
                                                                // Get the data-user-id of the clicked row
                                                                var userId = this.getAttribute('data-user-id');

                                                                // Increment the click count for the clicked row
                                                                clickCounts[userId]++;

                                                                // If the click count is odd (single click), toggle visibility of immediate child rows
                                                                if (clickCounts[userId] % 2 === 1) {
                                                                    var childRows = document.querySelectorAll('tr[data-user-master-id="' +
                                                                        userId + '"]');
                                                                    childRows.forEach(function(childRow) {
                                                                        childRow.style.display = (
                                                                            childRow.style.display ===
                                                                            'none') ? 'table-row' : 'none';
                                                                    });
                                                                } else {

                                                                    if (event.target.tagName.toLowerCase() ===
                                                                        'td') {
                                                                            
                                                                        var userId = this.getAttribute(
                                                                            'data-user-id');

                                                                        // Define a function to recursively find matching descendant rows
                                                                        function findMatchingRows(userId) {
                                                                            // Find the descendant rows with matching data-user-master-id
                                                                            var childRows = document
                                                                                .querySelectorAll(
                                                                                    'tr[data-user-master-id="' +
                                                                                    userId + '"]');

                                                                            // Log the child ids
                                                                            childRows.forEach(function(row) {

                                                                                // Set the style attribute to display: none;
                                                                                row.style.display = 'none';

                                                                                var child_id = row
                                                                                    .getAttribute(
                                                                                        'data-user-id');

                                                                                // Recursively call the function to find matching descendant rows
                                                                                findMatchingRows(child_id);
                                                                            });
                                                                        }

                                                                        // Call the function to start the chain
                                                                        findMatchingRows(userId);
                                                                    }
                                                                    hideDescendantRows(userId);
                                                                }
                                                            }
                                                        });
                                                    });

                                                    // Function to hide all descendant rows below the clicked row
                                                    function hideDescendantRows(userId) {
                                                        // Find all descendant rows below the clicked row
                                                        var descendantRows = document.querySelectorAll('tr[data-user-master-id="' + userId + '"]');

                                                        // Hide all descendant rows
                                                        descendantRows.forEach(function(descendantRow) {
                                                            descendantRow.style.display = 'none';
                                                        });

                                                        // Reset click count for the clicked row
                                                        clickCounts[userId] = 0;
                                                    }

                                                });
                                            </script>
                                            <style>
                                                .arrow-icon {
                                                    transition: transform 0.3s ease;
                                                    /* Add smooth transition */
                                                }

                                                .arrow-icon.up {
                                                    transform: rotate(180deg);
                                                    /* Rotate the arrow icon for the up position */
                                                }
                                            </style>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('super-admin.layout.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\xampp\htdocs\eld_new_soff\resources\views/super-admin/user/view.blade.php ENDPATH**/ ?>