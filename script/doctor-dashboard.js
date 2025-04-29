
      function copyCode() {
    var code = document.getElementById("referralCode").innerText;
    navigator.clipboard.writeText(code);
    alert("Referral Code Copied!");
}

 function showRescheduleForm(appointmentId) {
        document.getElementById("reschedule-form-" + appointmentId).style.display = "block";
    }

    function hideRescheduleForm(appointmentId) {
        document.getElementById("reschedule-form-" + appointmentId).style.display = "none";
    }

    