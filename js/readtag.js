let lastRFID = "";

function simulateRFIDScan() {
    // Simulate a new RFID tag every 10 seconds
    const tags = ["RFID12345", "RFID98765", "RFID54321"];
    lastRFID = tags[Math.floor(Math.random() * tags.length)];

    // Display it in the RFID input field (optional)
    const rfidInput = document.getElementById("rfidInput");
    if (rfidInput) {
        rfidInput.value = lastRFID;
    }

    console.log("Simulated RFID:", lastRFID);
}

// Start simulating every 10 seconds
setInterval(simulateRFIDScan, 10000);
