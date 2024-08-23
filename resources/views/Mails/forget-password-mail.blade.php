<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailData['title'] }}</title>
    <link href="{{ asset('css/mail.css') }}" rel="stylesheet">
</head>
<style>
    body {
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: sans-serif;
    }

    .container {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 400px;
        width: 400px;
        padding: 0 40px;
        border-radius: 10px;
        border: 1px solid rgb(19, 92, 226);
        gap: 10px;
    }

    button:hover {
        background-color: white;
        border: 1px solid rgb(19, 92, 226);
        color: rgb(19, 92, 226);
    }

    button {
        background-color: rgb(19, 92, 226);
        border-radius: 6px;
        border: none;
        padding: 8px 8px;
        color: white;
        cursor: pointer;
    }

    a {
        text-decoration: none;
    }
</style>

<body>
    <div class="container">
        <img src="{{ asset('logo.png') }}" alt="logo" height="100">
        <h1>Forget Passoword</h1>
        <p>{{ $mailData['body'] }}</p>
        <a href="{{ $mailData['url'] }}"><button>Reset Passoword</button></a>

    </div>
</body>

</html>
