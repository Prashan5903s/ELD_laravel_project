@foreach ($users as $user)
    @if ($user->user_type == 'U')
        <tr class="descendant-item child-row row-all" style="display: none; background-color:#F5F5F5;" data-user-id="{{ $user->id }}"
            data-user-master-id="{{ $user->master_id }}">
            <td><i class="fa fa-angle-up"></i></td>
            <td class="d-flex align-items-center">{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>
                @if ($user->user_type == 'WC')
                    White label company
                @endif
                @if ($user->user_type == 'EC')
                    Company
                @endif
                @if ($user->user_type == 'RS')
                    Reseller
                @endif
                @if ($user->user_type == 'TR')
                    Transport company
                @endif
                @if ($user->user_type == 'U')
                    User/Driver
                @endif
            </td>
            <td>{{ trim(explode(',', $user->country_code)[0]) }} {{$user->mobile_no}}</td>
            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('h:i A d-m-Y') }}</td>
            <td>
                <!--<button class="btn btn-primary shadowLoginBtn" data-user-type="{{ $user->user_type }}"-->
                <!--    data-user-id="{{ $user->id }}">-->
                <!--    <i class="fa fa-sign-in"></i>-->
                <!--</button>-->
            </td>

            <div class="modal fade" id="shadowLoginModal" tabindex="-1" aria-labelledby="shadowLoginModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="shadowLoginModalLabel">
                                Shadow Login Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: red;"></i>
                            <p style="margin-top: 10px">Are you sure you
                                want to do shadow login?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary shadowLogin_btn" id="shadowLoginYes"
                                data-user-type="{{ $user->user_type }}" data-user-id="{{ $user->id }}">Yes,
                                Shadow
                                Login</button>
                            <button type="button" class="btn btn-secondary" id="shadowLoginCancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">
                                Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fa fa-close" style="font-size: 48px; color: red;"></i>
                            <p>Shadow login not happening</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" id="okGotIt" data-bs-dismiss="modal">Ok,
                                got it</button>
                        </div>
                    </div>
                </div>
            </div>
        </tr>
    @endif
    @if ($user->user_type != 'U')
        <tr class="descendant-item child-row row-all " style="display: none; background-color:#F8F8F8;" data-user-id="{{ $user->id }}"
            data-user-master-id="{{ $user->master_id }}">
            <td><i class="fa fa-angle-up"></i></td>
            <td class="d-flex align-items-center">{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>
                @if ($user->user_type == 'WC')
                    White label company
                @endif
                @if ($user->user_type == 'EC')
                    Company
                @endif
                @if ($user->user_type == 'RS')
                    Reseller
                @endif
                @if ($user->user_type == 'TR')
                    Transport company
                @endif
                @if ($user->user_type == 'U')
                    User/Driver
                @endif
            </td>
            <td>{{ trim(explode(',', $user->country_code)[0]) }} {{$user->mobile_no}}</td>
            <td>{{ \Carbon\Carbon::parse($user->created_at)->format('h:i A d-m-Y') }}</td>
            <td>
                <button class="btn btn-primary shadowLoginBtn" data-user-type="{{ $user->user_type }}"
                    data-user-id="{{ $user->id }}">
                    <i class="fa fa-sign-in"></i>
                </button>
            </td>

            <div class="modal fade" id="shadowLoginModal" tabindex="-1" aria-labelledby="shadowLoginModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="shadowLoginModalLabel">
                                Shadow Login Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fas fa-exclamation-circle" style="font-size: 48px; color: red;"></i>
                            <p style="margin-top: 10px">Are you sure you
                                want to do shadow login?</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary shadowLogin_btn" id="shadowLoginYes"
                                data-user-type="{{ $user->user_type }}" data-user-id="{{ $user->id }}">Yes,
                                Shadow
                                Login</button>
                            <button type="button" class="btn btn-secondary" id="shadowLoginCancel"
                                data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Confirmation Modal -->
            <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmationModalLabel">
                                Confirmation
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <i class="fa fa-close" style="font-size: 48px; color: red;"></i>
                            <p>Shadow login not happening</p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" id="okGotIt"
                                data-bs-dismiss="modal">Ok,
                                got it</button>
                        </div>
                    </div>
                </div>
            </div>
        </tr>
    @endif
    @include('super-admin.layout.user_row', ['users' => $user->descendants])
@endforeach
