<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    $(document).ready(function () {
        $(".user-item").click(function () {
            $(".user-item").removeClass("active"); // Remove highlight from others
            $(this).addClass("active"); // Add highlight to the clicked user
        });
    });


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  
    
        $(document).ready(function () {
            $(".toggle-sidebar").click(function () {
                $(".sidebar").toggleClass("active");
            });
        });
    
    


    function scrollToBottom() {
        var chatMessages = document.getElementById("chatMessages");
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    $(document).ready(function () {
        scrollToBottom(); // Scroll on page load

        // Also scroll down when a new message is received
        setInterval(scrollToBottom, 1000);
    });

