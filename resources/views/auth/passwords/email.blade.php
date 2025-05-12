<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>BUP - Reset Password</title>

    <style>
        .login-logo {
            display: flex;
            flex-direction: column;
            /* justify-content: space-evenly; */
            text-align: center;
            justify-items: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-box bg-light p-5 rounded my-5">
                    <div class="login-logo">
                        @if (@$bupLogo)
                            <a href="{{ route('index') }}">
                                <img src="{{ asset('public/upload/logo/' . $bupLogo->image) }}" alt="Logo" class="me-2" width="100px" />
                            </a>
                        @else
                            <a href="{{ route('index') }}">
                                <img src="{{ asset('public/frontend/assets/img/bup/logo.svg') }}" alt="Logo" class="me-2" width="100px" />
                            </a>
                        @endif
                        <p id="" class="text-center"
                            style="text-shadow: 0 1px 48px rgb(255 255 255 / 20%); font-size: 24px;">Bangladesh University of Professionals
                        </p>

                        {{-- <img style="height: 120px; width: 120px" id="profile-img" class="profile-img-card" src="{{ asset('public/images/logo/bup_logo.png') }}" /> --}}
                        {{-- <h2 class="text-center">Bangladesh
                            University of Professionals</h2> --}}
                    </div>
                    <div class="card">
                        <div class="card-body login-card-body">
                            <p class="login-box-msg">Reset Your Password</p>
                            @if (session('status'))
                                <div class="alert alert-success" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form action="{{ route('password.email') }}" method="post">
                                @csrf
                                <div class="form-group @error('email') has-error @enderror">
                                    <div class="input-group mb-3">
                                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                                    </div>
                                    @error('email')
                                        <span class="inavlid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-block">Send Password Reset Link</button>
                                    </div>
                                </div>
                            </form>
                            <p class="my-3 text-center">
                                <a href="{{ route('login') }}" class="btn btn-info btn-block">Go to Login</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>
