$(document).ready(function() {
    $("#find-hospitals").click(function() {
        let hospitalName = $("#hospital-search").val().trim();
        let location = $("#location-filter").val().trim();

        $.ajax({
            url: "hospital_search.php",
            type: "GET",
            data: { search: hospitalName, location: location },
            success: function(response) {
                let hospitals = JSON.parse(response);
                let resultsContainer = $("#hospital-results");
                resultsContainer.empty();

                if (hospitals.length > 0) {
                    hospitals.forEach(function(hospital) {
                        resultsContainer.append(`
                            <li class="hospital-item">
                                <a href="hospital_details.php?id=${hospital.id}" class="hospital-link">
                                    <strong>${hospital.name}</strong> - ${hospital.location}
                                </a>
                            </li>
                        `);
                    });
                } else {
                    resultsContainer.append("<li class='no-results'>No hospitals found.</li>");
                }

                // Show popup inside the hospital card
                $("#hospital-popup").fadeIn();
            },
            error: function() {
                alert("Error fetching hospitals.");
            }
        });
    });

    // Close popup when clicking the close button
    $(".close-btn").click(function() {
        $("#hospital-popup").fadeOut();
    });
});
