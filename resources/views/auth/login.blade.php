@extends('layouts.components.auth')
@section('title', 'Sign In')
@section('content')
    <div class="p-2 mt-4">
        <form id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter email">
                <small class="text-danger errorEmail mt-2"></small>
            </div>

            <div class="mb-3">
                <div class="float-end">
                    <a href="{{ route('password.request') }}" class="text-muted">Forgot
                        password?</a>
                </div>
                <label class="form-label" for="password-input">Password <span style="color:red">*</span></label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" class="form-control pe-5 password-input" placeholder="Enter password"
                        id="password" name="password">
                    <button
                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                        type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                    <small class="text-danger errorPassword mt-2"></small>
                </div>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="remember_me" name="remember">
                <label class="form-check-label" for="remember_me">Remember
                    me</label>
            </div>

            <div class="mt-4">
                <button class="btn btn-success w-100" type="submit">Sign In</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#email').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorEmail').html('');
            });

            $('#password').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorPassword').html('');
            });

            $('#loginForm').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait a moment',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    data: $(this).serialize(),
                    url: "{{ route('login') }}",
                    type: "POST",
                    dataType: 'json',
                    beforeSend: function() {
                        $('#login').prop('disabled', true).html(
                            '<i class="mdi mdi-loading mdi-spin me-2"></i> Processing...'
                        );

                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#login').prop('disabled', false).text('Sign In');
                        Swal.close();
                    },
                    success: function(response) {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('.errorEmail').html(errors.email.join('<br>'));
                            }
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('.errorPassword').html(errors.password.join('<br>'));
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred, please try again.',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        }
                    }
                });
            });
        });
    </script>
@endsection
