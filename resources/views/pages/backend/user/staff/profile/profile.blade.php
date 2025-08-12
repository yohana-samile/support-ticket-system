@extends('layouts.backend.app')
@section('title', __('label.users_profile'))
@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-border-top nav-border-top-primary mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link fw-medium active" data-bs-toggle="tab" href="#general_tab" role="tab" aria-selected="true">
                        General
                    </a>
                </li>

            </ul>
            <div class="tab-content text-muted">
                <div class="tab-pane active show" id="general_tab" role="tabpanel">

                    {{--action section--}}
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-end flex-wrap">
                                @if(access()->allow('manage_staff') || access()->allow('update_staff'))
                                    <a href="{{ route('backend.user.edit',$user->uid) }}"
                                       class="btn btn-sm btn-primary mr-2 mb-2">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> {{ __('label.edit') }}
                                    </a>

                                    <a href="javascript:void(0)" class="btn btn-sm btn-danger mr-2 mb-2" data-toggle="modal" data-target="#changePasswordModal">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> {{ __('label.change_password_instead') }}
                                    </a>

                                    <form action="{{ route('backend.user.resend_resend_temp_password') }}" method="POST" class="resend_resend_temp_password d-inline mr-2 mb-2">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $user->email }}" required>
                                        <button type="submit" class="btn btn-sm btn-warning">@lang('label.resend_password')</button>
                                    </form>
                                @endif

                                <a href="{{ route('backend.user.index') }}" class="btn btn-sm btn-dark mb-2">  <!-- Added mb-2 -->
                                    <i class="ri-close align-bottom me-2 text-muted"></i> {{ __('label.close') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @include('pages.backend.user.staff.profile.includes.general_info')
                </div>

                {{--document tab--}}
                <div class="tab-pane" id="document_center_tab" role="tabpanel">
                    <div class="d-flex">

                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="changePasswordModal" class="modal fade zoomIn" data-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">@lang('label.change_password')</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="changePasswordForm" action="{{ route('backend.user.change_password_instead') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $user->email }}" required>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">@lang('label.new_password')</label>
                            <input type="password" class="form-control" id="new_password" name="password" required minlength="8">
                            <div class="form-text">@lang('label.password_min_length')</div>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">@lang('label.confirm_password')</label>
                            <input type="password" class="form-control" id="confirm_password" name="password_confirmation" required>
                            <div class="invalid-feedback" id="password-match-error">
                                @lang('label.passwords_do_not_match')
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('label.cancel')</button>
                        <button type="submit" class="btn btn-danger" id="submit-password-change">@lang('label.change_password')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        document.addEventListener('DOMContentLoaded', function () {
            // Resend Temporary Password Form Handling
            const resendTempPasswordForms = document.querySelectorAll('form.resend_resend_temp_password');
            resendTempPasswordForms.forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Confirm Resend Temporary Password',
                        text: 'Are you sure you want to resend a temporary password to this staff member?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Resend',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const loadingSwal = Swal.fire({
                                title: 'Processing',
                                html: 'Resending temporary password...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(form);
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    loadingSwal.close();
                                    if(data.success) {
                                        toastMessage('success', data.message || 'Temporary password has been resent' );
                                    } else {
                                        toastMessage('error', data.message || 'An error occurred');
                                    }
                                })
                                .catch(error => {
                                    loadingSwal.close();
                                    toastMessage('error', error.message || 'Failed to resend temporary password');
                                    //console.error('Error:', error);
                                });
                        }
                    });
                });
            });

            // Password Change Form Handling
            const changePasswordForm = document.getElementById('changePasswordForm');
            if (changePasswordForm) {
                const newPasswordInput = document.getElementById('new_password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                const passwordMatchError = document.getElementById('password-match-error');

                // Real-time password validation
                function validatePassword() {
                    if (newPasswordInput.value !== confirmPasswordInput.value) {
                        confirmPasswordInput.classList.add('is-invalid');
                        passwordMatchError.style.display = 'block';
                        return false;
                    }

                    if (newPasswordInput.value.length < 8) {
                        newPasswordInput.classList.add('is-invalid');
                        return false;
                    }

                    confirmPasswordInput.classList.remove('is-invalid');
                    newPasswordInput.classList.remove('is-invalid');
                    passwordMatchError.style.display = 'none';
                    return true;
                }

                confirmPasswordInput.addEventListener('input', validatePassword);
                newPasswordInput.addEventListener('input', validatePassword);

                // Form submission handler
                changePasswordForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    if (!validatePassword()) {
                        if (newPasswordInput.value.length < 8) {
                            toastMessage('error', 'Password must be at least 8 characters long');
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Confirm Password Change',
                        text: 'Are you sure you want to change this staff member\'s password?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, Change',
                        cancelButtonText: 'Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const loadingSwal = Swal.fire({
                                title: 'Processing',
                                html: 'Updating password...',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            const formData = new FormData(changePasswordForm);
                            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                            fetch(changePasswordForm.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                },
                                body: formData
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    loadingSwal.close();

                                    // Close modal if exists
                                    const modal = $('#changePasswordModal').data('bs.modal');
                                    if (modal) {
                                        modal.hide();
                                    }

                                    if (data.success) {
                                        toastMessage('success', data.message || 'Password changed successfully');
                                        changePasswordForm.reset();
                                    } else {
                                        toastMessage('error', data.message || 'Failed to change password');
                                    }
                                })
                                .catch(error => {
                                    loadingSwal.close();
                                    toastMessage('error', error.message || 'An error occurred while changing password');
                                    //console.error('Error:', error);
                                });
                        }
                    });
                });
            }
        });

        $(document).on('change', '.user-status-toggle', function () {
            let userId = $(this).data('id');
            let isChecked = $(this).is(':checked');
            let switchElem = $(this);

            if (!isChecked) {
                // Confirm before disabling
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will disable the user account.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, disable it'
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateUserStatus(userId, 0, switchElem);
                    } else {
                        switchElem.prop('checked', true); // revert if cancelled
                    }
                });
            } else {
                // Enable directly
                updateUserStatus(userId, 1, switchElem);
            }
        });

        function updateUserStatus(userId, is_active, switchElem) {
            $.ajax({
                url: '/backend/user/toggle_status',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    is_active: is_active
                },
                success: function (res) {
                    if (res.success) {
                        toastMessage("success", res.message);
                    } else {
                        toastMessage("error", res.message);
                        switchElem.prop('checked', !is_active); // revert toggle
                    }
                },
                error: function (xhr, status, error) {
                    let response = xhr.responseJSON;

                    if (response && response.message) {
                        toastMessage("success", response.message); // show backend message
                    } else if (xhr.status === 403) {
                        toastMessage("error", "{{ __('messages.unauthorized_action') }}")
                    } else {
                        toastMessage("error", "{{ __('messages.something_went_wrong') }}")
                    }
                    switchElem.prop('checked', !is_active); // revert if error
                }
            });
        }

        async function toastMessage(type = 'error', message) {
            toastr[type](message, '', {
                timeOut: 3000,
                positionClass: 'toast-top-right',
                progressBar: true,
                closeButton: true
            });
        }
    </script>
@endpush
