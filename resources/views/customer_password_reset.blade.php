<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>Todo - Reset User Password</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{asset('vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <link href="{{asset('build/css/custom.min.css')}}" rel="stylesheet">
</head>
<body class="login">
<div>
    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <form method="POST" action="{{ route('change.customer.password') }}">
                    <input type="hidden" name="code" value="{{ $customer->password_reset_token }}" />
                    <div class="row">
                        @csrf
                        <h1>Reset your password</h1>
                        @if($errors->any())
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li class="alert alert-danger">{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="form-group">
                            <input id="email" type="email" placeholder="{{ __('auth.provide_email') }}" class="form-control" name="email" value="{{ $customer->email }}" disabled/>
                        </div>
                        <div class="form-group">
                            <input type="password" min="6" placeholder="{{ __('auth.provide_password') }}" class="form-control @error('password') is-invalid @enderror" name="password" required/>
                        </div>
                    </div>
                    <div class="row">
                        <button type="submit" class="btn btn-primary">Reset Now</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
</body>
</html>
