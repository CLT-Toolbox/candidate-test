@extends('layouts.components.auth')
@section('title', 'Sign Up')
@section('content')
    <div class="p-2 mt-4">
        <form id="registerForm">
            <div class="mb-3">
                <label for="name" class="form-label">Name <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
                <small class="text-danger errorName mt-2"></small>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Enter email">
                <small class="text-danger errorEmail mt-2"></small>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password <span style="color:red">*</span></label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                <small class="text-danger errorPassword mt-2"></small>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password <span
                        style="color:red">*</span></label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Enter confirm password">
                <small class="text-danger errorPasswordConfirmation mt-2"></small>
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

            $('#name').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorName').html('');
            });

            $('#email').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorEmail').html('');
            });

            $('#password').on('input', function() {
                $(this).removeClass('is-invalid');
                $('.errorPassword').html('');
            });

            $('#registerForm').submit(function(e) {
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
                    url: "{{ route('register') }}",
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('.form-control').removeClass('is-invalid');
                        $('.text-danger').html('');
                    },
                    complete: function() {
                        $('#register').prop('disabled', false).text('Register');
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful',
                            text: 'You have successfully registered. Please log in.',
                        }).then(() => {
                            window.location.href = '{{ route('login') }}';
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            Swal.close();
                            let errors = xhr.responseJSON.errors;
                            if (errors.name) {
                                $('#name').addClass('is-invalid');
                                $('.errorName').html(errors.name.join('<br>'));
                            }
                            if (errors.phone) {
                                $('#phone').addClass('is-invalid');
                                $('.errorPhone').html(errors.phone.join('<br>'));
                            }
                            if (errors.email) {
                                $('#email').addClass('is-invalid');
                                $('.errorEmail').html(errors.email.join('<br>'));
                            }
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('.errorPassword').html(errors.password.join('<br>'));
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                $('.errorPasswordConfirmation').html(errors
                                    .password_confirmation.join('<br>'));
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
