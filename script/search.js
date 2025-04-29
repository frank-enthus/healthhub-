document.addEventListener("DOMContentLoaded", function () {
    console.log("🔍 Script loaded successfully!");

    const findDoctorBtn = document.getElementById("findDoctorBtn");
    const locateFacilityBtn = document.getElementById("locateFacilityBtn");
    const facilitySearchContainer = document.getElementById("facilitySearchContainer");
    const searchInput = document.getElementById("searchInput");
    const resultsDropdown = document.getElementById("resultsDropdown");

    let searchType = "";

    function showSearch(type) {
        searchType = type;
        facilitySearchContainer.style.display = "block";
        searchInput.value = "";
        resultsDropdown.innerHTML = "";
        searchInput.placeholder = (type === "doctor") 
            ? "Search Doctor by Specialization or Clinic Location" 
            : "Search Hospital by Name, Location, or Specialization";
        searchInput.focus();
        console.log(`🔍 Searching for ${type}s`);
    }

    findDoctorBtn.addEventListener("click", function () {
        console.log("🩺 Find a Doctor button clicked!");
        showSearch("doctor");
    });

    locateFacilityBtn.addEventListener("click", function () {
        console.log("🏥 Locate a Facility button clicked!");
        showSearch("hospital");
    });

    searchInput.addEventListener("keyup", function () {
        let searchTerm = searchInput.value.trim();
        if (searchTerm.length > 1) {
            console.log(`🔍 Searching for: ${searchTerm} in ${searchType}`);

            fetch(`search_results.php?query=${searchTerm}&type=${searchType}`)
                .then(response => response.json())
                .then(data => {
                    console.log("📄 Data received from backend:", data);
                    resultsDropdown.innerHTML = "";

                    if (data.length > 0) {
                        data.forEach(item => {
                            console.log("🔎 Doctor Data:", item);

                            let location = item.clinic_location && item.clinic_location !== "Unknown" 
                                ? item.clinic_location 
                                : "Unknown Location"; // Fix Undefined Issue

                            let itemDiv = document.createElement("div");
                            itemDiv.classList.add("dropdown-item");

                            if (searchType === "doctor") {
                                itemDiv.innerHTML = `${item.full_name} - ${item.specialization} (${location})`;
                                itemDiv.addEventListener("click", function () {
                                    window.location.href = `doctor_details.php?id=${item.id}`;
                                });
                            } else {
                                itemDiv.innerHTML = `${item.name} - ${item.location} (${item.specialization})`;
                                itemDiv.addEventListener("click", function () {
                                    window.location.href = `hospital_details.php?id=${item.id}`;
                                });
                            }

                            resultsDropdown.appendChild(itemDiv);
                        });
                    } else {
                        resultsDropdown.innerHTML = "<div class='dropdown-item'>No results found</div>";
                    }
                })
                .catch(error => console.error("⚠ Error fetching results:", error));
        } else {
            resultsDropdown.innerHTML = "";
        }
    });
});
