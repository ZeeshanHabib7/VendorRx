
document.getElementById('reset-password').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    const submitBtn = document.querySelector("form button");
    const formData = new FormData(this);
    console.log("Form Data: ");
    // for (let [key, value] of formData.entries()) {
    //     console.log(`${key}: ${value}`);
    // }
    const successElement = document.getElementById('success');
    const errorsElement = document.getElementById('errors');


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
                errorsElement.innerText = data.message.password || data.message.email;
                setTimeout(() => {
                    errorsElement.innerText = "";
                }, 3000)

            }
        }).catch(error => {
            errorsElement.innerText = "An error occured please try again";
            setTimeout(() => {
                errorsElement.innerText = "";
            }, 3000)

        })

});
