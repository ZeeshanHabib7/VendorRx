<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Pasword</title>
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
        min-height: max-content;
        padding: 40px 40px;
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
        font-size: 1.2vw;
    }
</style>

<body>
    <div class="form-container">
        <form action="{{route('sendEmail')}}" method="POST">
            @csrf
            <h2>Forget Password</h2>
            <p>Please enter your email address.</br> A password reset link will be sent to your email.</p>
            <label for="Email">Email</label>
            <input type="email" name="email" required placeholder="eg:example@gmail.com">

            <button type="submit">Send Email</button>
        </form>
        @if (session('success'))
            <p style="font-size: 1vw; color:green; margin-top:30px;" id="success"><i>{{ session('success') }}</i></p>
        @endif
        @if ($errors->any())
            <p style="font-size: 1vw; color:red; margin-top:30px;">{{ $errors->first() }}</p>
        @endif
        <p id="timerr"></p>
    </div>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const successMsg = document.querySelector("#success");
            // submitButton.onClick = myfun;
            if (successMsg != null) {
                runTimer();
            }

            function runTimer() {
                const timerr = document.querySelector("#timerr");
                console.log("clicked");
                let timer;
                let totalTime = 90; // 90 seconds
                let isRunning = false;

                startTimer();

                function updateDisplay(time) {
                    const minutes = Math.floor(time / 60);
                    const seconds = time % 60;
                    const formattedTime = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    timerr.textContent = formattedTime;
                }

                function startTimer() {
                    if (isRunning) return; // Prevent multiple timers
                    isRunning = true;

                    submitButton.disabled = true;

                    updateDisplay(totalTime); // Initial display

                    timer = setInterval(() => {
                        totalTime--;

                        updateDisplay(totalTime);

                        if (totalTime <= 0) {
                            clearInterval(timer);
                            alert("Time's up!");
                            isRunning = false;
                            submitButton.disabled = false;
                        }
                    }, 1000);
                }
            }
        })

    </script>
</body>

</html>