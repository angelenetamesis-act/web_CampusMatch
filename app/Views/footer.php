<!-- app/Views/footer.php -->
    <script>
        // Real-time Notification Check (Every 10 seconds)
        setInterval(function() {
            // We only check if the user is actually logged in
            fetch('index.php?action=check_notifications')
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        // Display the most recent notification
                        alert("New Notification: " + data[0].actor_name + " " + data[0].message);
                        
                        // Optional: Reload the page or update a notification badge here
                    }
                })
                .catch(err => console.log("Notification error: ", err));
        }, 10000);
    </script>
</body>
</html>