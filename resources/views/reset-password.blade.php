<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Pasword</title>
</head>
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        font-family: sans-serif;
    }

    .form-container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 300px;
        width: 300px;
        padding: 70px 40px;
        border-radius: 10px;
        background-color: rgb(214, 224, 241);
        border: 1px solid rgb(19, 92, 226);
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        width: 100%;
    }

    form label {
        font-size: 1.3vw;
        font-family: sans-serif;
        font-weight: 400;
    }

    form input {
        /* width: 100%; */
        font-family: sans-serif;
        border-radius: 6px;
        padding: 8px;
        border: 1px solid rgb(19, 92, 226);
    }

    form button:hover {
        background-color: white;
        border: 1px solid rgb(19, 92, 226);
        color: rgb(19, 92, 226);
    }

    form button {
        background-color: rgb(19, 92, 226);
        border-radius: 6px;
        border: none;
        padding: 8px 0px;
        color: white;
        cursor: pointer;
    }

    form p {
        font-family: sans-serif;
    }
</style>

<body>
    <div class="form-container">
        <form action="{{route('resetPassword')}}" method="POST">
            @csrf
            <h2>Reset Password</h2>
            <label for="Email">Email</label>
            <input type="email" name="email" value="{{$email}}" required placeholder="eg:example@gmail.com">
            <label for="password">Password</label>
            <input type="password" name="password" required>
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" name="password_confirmation" required>
            <button type="submit">Change Password</button>
        </form>
        @if (session('success'))
            <p style="font-size: 1vw; color:green; margin-top:30px;"><i>{{ session('success') }}</i></p>
        @endif
        @if ($errors->any())
            <p style="font-size: 1vw; color:red; margin-top:30px;">{{ $errors->first() }}</p>
        @endif
    </div>

</body>

</html>