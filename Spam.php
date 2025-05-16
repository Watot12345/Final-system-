<?php
// index.php
session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $cooldown = 0; // 30-second cooldown
    
    // Check if last attempt was too recent
    if (isset($_SESSION['last_attempt']) && (time() - $_SESSION['last_attempt'] < $cooldown)) {
        $remaining = $cooldown - (time() - $_SESSION['last_attempt']);
        
        // Return JSON response for AJAX
        header('Content-Type: application/json');
        die(json_encode([
            'status' => 'cooldown',
            'message' => "Please wait $remaining seconds",
            'remaining' => $remaining
        ]));
    }
    
    // If cooldown passed, process registration
    $_SESSION['last_attempt'] = time();
    // [Your registration logic here...]
    
    // Success response
    header('Content-Type: application/json');
    die(json_encode(['status' => 'success', 'message' => 'Registered!']));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration with Cooldown</title>
    <style>
        #countdown { 
            color: red; margin-top: 10px; 
        }
        .hidden { 
            display: none; 
        }
    </style>
</head>
<body>

    <script>
    const form = document.getElementById('registerForm');
    const countdownEl = document.getElementById('countdown');
    const successEl = document.getElementById('successMessage');
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        
        try {
            const response = await fetch('index.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            
            if (result.status === 'cooldown') {
                // Show countdown
                countdownEl.classList.remove('hidden');
                let seconds = result.remaining;
                
                const timer = setInterval(() => {
                    countdownEl.textContent = `Please wait ${seconds} seconds...`;
                    seconds--;
                    
                    if (seconds < 0) {
                        clearInterval(timer);
                        countdownEl.classList.add('hidden');
                    }
                }, 1000);
                
            } else if (result.status === 'success') {
                successEl.classList.remove('hidden');
                form.reset();
            }
            
        } catch (error) {
            console.error('Error:', error);
        }
    });
    </script>
</body>
</html>