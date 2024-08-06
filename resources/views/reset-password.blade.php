<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/reset-password.css') }}" rel="stylesheet">
    <title>Reset Pasword</title>
    <style>
        .form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 400px;
            width: 300px;
            padding: 70px 40px;
            padding-bottom: 95px;
            border-radius: 10px;
            border: 1px solid rgb(19, 92, 226);
        }
    </style>
</head>


<body>
    <div class="form-container">
        <img src="{{asset("logo.png")}}" alt="VendorX Logo" height="150" style="margin-top:20px">
        <form id="reset-password">
            @csrf
            <h2>Reset Password</h2>
            <label for="Email">Email</label>
            <input type="email" name="email" value="{{$email}}" required readonly>
            <label for="password">Password</label>
            <input type="password" name="password" required>
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="password_confirmation" required>
            <button type="submit">Change Password</button>
        </form>

        <div class="message-container" style="margin-top:30px;">

            <p style="font-size: 1vw; color:green;  " id="success"><i></i></p>
            <p style="font-size: 1vw; color:red;" id="errors"><i></i></p>
        </div>

    </div>
    <script>
        // Pass CSRF token and route to JavaScript
        const csrfToken = '{{ csrf_token() }}';
        const resetPasswordRoute = '{{ route('resetPassword') }}';

    </script>
    <script type="text/javascript" src="{{ asset('JS/reset-password.js') }}"></script>
</body>

</html>