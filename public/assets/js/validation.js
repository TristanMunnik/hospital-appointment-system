function validateLogin() {
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;

    if (email === '') {
        alert('Please enter an email');
        return false;
    }

    if (password === '') {
        alert('Please enter a password');
        return false;
    }

    if (password.length < 6) {
        alert('Password must have at least 6 characters');
        return false;
    }

    return true;
}

function validateAppointment() {
    const doctorID = document.getElementById('doctorID').value;
    const date = document.getElementById('date').value;
    const time = document.getElementById('time').value;

    if (doctorID === '') {
        alert('Please select a doctor');
        return false;
    }

    if (date === '') {
        alert('Please enter a date');
        return false;
    }

    if (time === '') {
        alert('Please enter a time');
        return false;
    }

    return true;
}
