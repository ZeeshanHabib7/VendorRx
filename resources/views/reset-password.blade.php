<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="{{ asset('css/reset-password.css') }}" rel="stylesheet">
    <title>Reset Pasword</title>
    <style>

    </style>
</head>


<body>
    <div class="form-container">
        <img src="{{asset("logo.png")}}" alt="VendorX Logo" height="150" style="margin-top:20px">
        <form id="reset-password">
            @csrf
            <h2 style="text-align:center;">Reset Password</h2>
            <div class="input-box password" style="margin-bottom:10px">
                <input type="password" name="password" required id="password-input" />
                <label>Password</label>
                <p style="font-size: 1vw; color:red;" id="password"><i></i></p>
            </div>
            <div class="input-box confirm-password">
                <input type="password" name="password_confirmation" required id="confirm-password-input" />
                <label>Confirm Password</label>
                <p style="font-size: 1vw; color:red;" id="confirm-password"><i></i></p>
            </div>
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
        <?php 
            $domain = URL::to('/');
$url = $domain . '/api/users/reset-password/' . $id;
        ?>

        const resetPasswordRoute = '{{ $url }}';
    </script>
    <script type="text/javascript" src="{{ asset('JS/reset-password.js') }}"></script>
</body>

</html>