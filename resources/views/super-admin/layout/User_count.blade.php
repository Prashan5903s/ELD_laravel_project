<div class="col-md-6 col-xl-6 mb-xxl-10">
                                                <!--begin::Card widget 8-->
                                                <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                                    <!--begin::Card body-->
                                                    <div
                                                        class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                                        <!--begin::Statistics-->
                                                        <div class="mb-4 px-9">
                                                            <!--begin::Info-->
                                                            <div class="d-flex align-items-center mb-2">
                                                                <!--begin::Currency-->
                                                                <a href="{{route('admin.view.total', ['white-label'])}}">
                                                                    <span class="fs-6 fw-semibold text-gray-500">Total WhiteLabel company</span>
                                                                </a>
                                                                <!--end::Value-->
                                                            </div>
                                                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">{{$wcCount}}</span>
                                                        </div>
                                                        <!--end::Statistics-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Card widget 8-->
                                                <!--begin::Card widget 5-->
                                                <div class="card card-flush h-md-50 mb-1 mb-xl-10">
                                                    <!--begin::Header-->
                                                    <div class="card-header pt-5">
                                                        <!--begin::Title-->
                                                        <div class="card-title d-flex flex-column">
                                                            <!--begin::Info-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Amount-->
                                                                <a href="{{route('admin.view.total', ['company'])}}">
                                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total company</span>
                                                                </a>
                                                                <!--end::Amount-->
                                                            </div>
                                                            <!--end::Info-->
                                                            <!--begin::Subtitle-->
                                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$ecCount}}</span>
                                                            <!--end::Subtitle-->
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <div class="card-header pt-5">
                                                        <!--begin::Title-->
                                                        <div class="card-title d-flex flex-column">
                                                            <!--begin::Info-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Amount-->
                                                                <a href="{{route('admin.view.total', ['user'])}}">
                                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total user</span>
                                                                </a>
                                                                <!--end::Amount-->
                                                            </div>
                                                            <!--end::Info-->
                                                            <!--begin::Subtitle-->
                                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$userCount}}</span>
                                                            <!--end::Subtitle-->
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Header-->
                                                </div>
                                                <!--end::Card widget 5-->
                                            </div>
                                            <!--end::Col-->
                                            <!--begin::Col-->
                                            <div class="col-md-6 col-xl-6 mb-xxl-10">
                                                <!--begin::Card widget 9-->
                                                <div class="card overflow-hidden h-md-50 mb-5 mb-xl-10">
                                                    <!--begin::Card body-->
                                                    <div
                                                        class="card-body d-flex justify-content-between flex-column px-0 pb-0">
                                                        <!--begin::Statistics-->
                                                        <div class="mb-4 px-9">
                                                            <!--begin::Statistics-->
                                                            <div class="d-flex align-items-center mb-2">
                                                                <!--begin::Value-->
                                                                <a href="{{route('admin.view.total', ['reseller'])}}">
                                                                    <span class="fs-6 fw-semibold text-gray-500">Total reseller</span>
                                                                </a>
                                                                <!--end::Value-->
                                                            </div>
                                                            <!--end::Statistics-->
                                                            <!--begin::Description-->
                                                            <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1">{{$rsCount}}</span>
                                                            <!--end::Description-->
                                                        </div>
                                                        <!--end::Statistics-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Card widget 9-->
                                                <!--begin::Card widget 7-->
                                                <!--begin::Card widget 5-->
                                                <div class="card card-flush h-md-50 mb-xl-10">
                                                    <!--begin::Header-->
                                                    <div class="card-header pt-5">
                                                        <!--begin::Title-->
                                                        <div class="card-title d-flex flex-column">
                                                            <!--begin::Info-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Amount-->
                                                                <a href="{{route('admin.view.total', ['transport'])}}">
                                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total transport</span>
                                                                </a>
                                                                <!--end::Amount-->
                                                            </div>
                                                            <!--end::Info-->
                                                            <!--begin::Subtitle-->
                                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$trCount}}</span>
                                                            <!--end::Subtitle-->
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <div class="card-header pt-5">
                                                        <!--begin::Title-->
                                                        <div class="card-title d-flex flex-column">
                                                            <!--begin::Info-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Amount-->
                                                                <a href="{{route('admin.view.total', ['driver'])}}">
                                                                    <span class="text-gray-500 pt-1 fw-semibold fs-6">Total driver</span>
                                                                </a>
                                                                <!--end::Amount-->
                                                            </div>
                                                            <!--end::Info-->
                                                            <!--begin::Subtitle-->
                                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{$driverCount}}</span>
                                                            <!--end::Subtitle-->
                                                        </div>
                                                        <!--end::Title-->
                                                    </div>
                                                    <!--end::Header-->
                                                </div>
                                                <!--end::Card widget 7-->
                                            </div>