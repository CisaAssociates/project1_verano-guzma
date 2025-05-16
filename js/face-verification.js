function verifyFace(userId, imageData, mode) {
    $.ajax({
        url: 'verify_face.php',
        type: 'POST',
        data: {
            user_id: userId,
            image_data: imageData,
            mode: mode
        },
        success: function(response) {
            if (response.success) {
                showToast('Success', response.message, 'success');
            } else {
                showToast('Error', response.message, 'error');
            }
        },
        error: function() {
            showToast('Error', 'Failed to process verification. Please try again.', 'error');
        }
    });
}

function showToast(title, message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-header">
            <strong>${title}</strong>
        </div>
        <div class="toast-body">
            ${message}
        </div>
    `;

    // Add to document
    document.body.appendChild(toast);

    // Remove after animation
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function handleViewRecords(mode) {
    window.location.href = mode === 'attendance' ? 'attendance_logs.php' : 'gatepass_logs.php';
}