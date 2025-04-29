function showDoctorSearch() {
    document.getElementById("search-section").style.display = "block";
    document.getElementById("doctor-search").style.display = "block";
    document.getElementById("hospital-search").style.display = "none";
}

function showHospitalSearch() {
    document.getElementById("search-section").style.display = "block";
    document.getElementById("doctor-search").style.display = "none";
    document.getElementById("hospital-search").style.display = "block";
}

function searchDoctors() {
    let input = document.getElementById("doctor-input").value.toLowerCase();
    let doctorList = [
        "Cardiology", "Neurology", "Dentistry", "Pediatrics", "Orthopedics"
    ];
    let filteredDoctors = doctorList.filter(doc => doc.toLowerCase().includes(input));
    
    let doctorListElement = document.getElementById("doctor-list");
    doctorListElement.innerHTML = "";
    filteredDoctors.forEach(doc => {
        let li = document.createElement("li");
        li.textContent = doc;
        doctorListElement.appendChild(li);
    });
}

function searchHospitals() {
    let input = document.getElementById("hospital-input").value.toLowerCase();
    let hospitalList = [
        "City Hospital - Downtown", "Greenfield Hospital - Suburbs", "Maple Street Hospital"
    ];
    let filteredHospitals = hospitalList.filter(hosp => hosp.toLowerCase().includes(input));
    
    let hospitalListElement = document.getElementById("hospital-list");
    hospitalListElement.innerHTML = "";
    filteredHospitals.forEach(hosp => {
        let li = document.createElement("li");
        li.textContent = hosp;
        hospitalListElement.appendChild(li);
    });
}

function copyReferralCode() {
    let code = document.getElementById("referral-code");
    code.select();
    document.execCommand("copy");
    alert("Referral code copied!");
}

function shareOnSocial(platform) {
    alert(`Sharing on ${platform}`);
}
