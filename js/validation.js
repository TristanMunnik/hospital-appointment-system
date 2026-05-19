function validateLogin(){

    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;

    // empty email
    if(email == '') {
        alert('Please enter an email');
        return false;
    }

    // password
    if(password == ''){
        alert("Please enter a password");
        return false;
    }

    // password length
    if(password.length < 6) {
        alert('Password must have at least 6 characters');
        return false;
    }

    return true;

}

function validateAppointment(){
    let doctorID = document.getElementById('doctorID').value;
    let date = document.getElementById('date').value;
    let time = document.getElementById('time').value;
    
    if(doctorID == '') {
        alert('Please select a doctor');
        return false;
    }

    if(date == '') {
        alert('Please enter a date');
        return false;
    }

    if(time == '') {
        alert('Please enter a time');
        return false;
    }

    addAppointment(doctorID, date, time);

    return true;

}

// array storing appointments
let appointments = [];

function addAppointment(doctorID, date, time) {
    let appointment = {
        doctorID: doctorID,
        date: date,
        time: time,
        status: 'requested'
    };

    appointments.push(appointment);

    displayAppointmentList();

}

// displaying appointment list
function displayAppointmentList() {
    let listDiv = document.getElementById('displayAppointmentList');

    if(!listDiv) return;

    listDiv.innerHTML = '';

    appointments.forEach(function (appointment) {
        listDiv.innerHTML += `
            <p>Doctor ID: ${appointment.doctorID} | 
            Date: ${appointment.date} | 
            Time: ${appointment.time} | 
            Status: ${appointment.status}</p>
        `;
    });
}