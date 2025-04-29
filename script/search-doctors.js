$(document).ready(function () {
    let $doctorResults = $("#doctor-results");
    let $searchInput = $("#doctor-search");
    let $mapElement = $("#map");

    // Handle doctor search input
    $searchInput.on("input", function () {
        let query = $(this).val().trim();
        if (query === "") {
            $doctorResults.html("").hide();
            $mapElement.removeClass("disable-interaction"); // Re-enable map clicks when list is empty
            return;
        }

        $.ajax({
            url: "search_doctors.php",
            type: "GET",
            data: { query: query },
            dataType: "json",
            success: function (response) {
                let resultsHtml = "";

                if (response.length === 0) {
                    resultsHtml = "<p>No doctors found.</p>";
                } else {
                    response.forEach(function (doctor) {
                        resultsHtml += `
                            <div class="doctor-option" data-id="${doctor.doctor_id}">
                                <p><strong>${doctor.full_name}</strong></p>
                                <p>Specialization: ${doctor.specialization}</p>
                                <button class="book-appointment" data-id="${doctor.doctor_id}">Book Appointment</button>
                            </div>
                        `;
                    });
                }

                // Update results without flickering
                $doctorResults.html(resultsHtml).show();
                $mapElement.addClass("disable-interaction"); // Disable map clicks when list is open
            },
            error: function () {
                console.error("Error fetching doctors.");
            }
        });
    });

    // Prevent hiding results when interacting with them
    $doctorResults.on("mouseenter", function () {
        $(this).data("hovering", true);
    }).on("mouseleave", function () {
        $(this).data("hovering", false);
    });

    // Hide results only when clearing search
    $searchInput.on("input", function () {
        if ($(this).val().trim() === "") {
            // Only hide results if the user is not hovering over the results
            if (!$doctorResults.data("hovering")) {
                $doctorResults.hide();
                $mapElement.removeClass("disable-interaction"); // Re-enable map clicks when list is hidden
            }
        }
    });

    // Handle booking button clicks
    $(document).on("click", ".book-appointment", function (event) {
        event.stopPropagation();
        let doctorId = $(this).data("id");

        if (!doctorId) {
            alert("Doctor ID not found. Please try again.");
            return;
        }

        window.location.href = "book_appointment.php?doctor_id=" + doctorId;
    });

    // Ensure the map is interactive again when clicking outside the list
    $(document).on("click", function (event) {
        // Hide the doctor results if clicked outside
        if (!$doctorResults.is(event.target) && !$doctorResults.has(event.target).length && !$searchInput.is(event.target)) {
            $doctorResults.hide();
            $mapElement.removeClass("disable-interaction"); // Re-enable map clicks when list is closed
        }
    });
});
