
document.getElementById('reset-password').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const submitBtn = document.querySelector("form button");
    const formData = new FormData(this);
    console.log("Form Data: ");
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }
    const successElement = document.getElementById('success');
    const errorsElement = document.getElementById('errors');
    const passwordError = document.getElementById('password');
    const confirmPasswordError = document.getElementById('confirm-password');
    const passwordInput = document.querySelector(".password input");
    const confirmpasswordInput = document.querySelector(".confirm-password input");
    const confirmpasswordLabel = document.querySelector(".confirm-password label");
    const passwordLabel = document.querySelector(".password label");


    // Send the form data using fetch
    fetch(resetPasswordRoute, {
        method: "POST",
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken // Include CSRF token
        }
    })
        .then(response => response.json())
        .then(data => {
            console.log("Data 1 : ", data);
            if (data.success == true) {

                //If success is true we display success message
                console.log("Data: ", data);
                successElement.innerText = data.message;
                submitBtn.disabled = true;

                // // Disable form resubmission
                // window.history.replaceState(null, null, window.location.href);

            } else {
                // If success is false we display error message
                console.error('Error:', data);
                if (data.message.password == "The password field confirmation does not match.") {
                    confirmPasswordError.innerText = data.message.password;
                    confirmpasswordInput.style.border = "1px solid red";
                    confirmpasswordLabel.style.color = "red";
                    setTimeout(() => {
                        confirmPasswordError.innerText = "";
                        confirmpasswordInput.style.border = "1px solid rgb(19, 92, 226)";
                        confirmpasswordLabel.style.color = "rgb(19, 92, 226)";
                    }, 5000)
                }
                else if (data.message.password) {
                    passwordError.innerText = data.message.password;
                    passwordInput.style.border = "1px solid red";
                    passwordLabel.style.color = "red";
                    setTimeout(() => {
                        passwordError.innerText = "";
                        passwordInput.style.border = "1px solid rgb(19, 92, 226)";
                        passwordLabel.style.color = "rgb(19, 92, 226)";
                    }, 5000)
                }
                else {
                    errorsElement.innerText = data.message.password || data.message.email;
                    setTimeout(() => {
                        errorsElement.innerText = "";
                    }, 5000)
                }


            }
        }).catch(error => {
            errorsElement.innerText = "An error occured please try again";
            setTimeout(() => {
                errorsElement.innerText = "";
            }, 5000)

        })

});
