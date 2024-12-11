document.addEventListener("DOMContentLoaded", function() {
    // Modal elements
    const addRoomModal = document.getElementById('addRoomModal');
    const editRoomModal = document.getElementById('editRoomModal');
    const bookingFormModal = document.getElementById('bookingForm');
    const closeButtons = document.querySelectorAll('.close');
    
    // Open Add Room Modal
    const addRoomLink = document.querySelector('.add-room-link a');
    addRoomLink.addEventListener('click', function(e) {
        e.preventDefault();
        addRoomModal.style.display = "block";
    });

    // Open Booking Modal
    const bookButtons = document.querySelectorAll('.book-room');
    bookButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            // Set the room ID in the booking form
            document.getElementById('booking_room_id').value = roomId;
            // Show the booking modal
            bookingFormModal.style.display = "block";
        });
    });

    // Open Edit Room Modal
    const editButtons = document.querySelectorAll('.edit-room');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            
            // Fetch room details via AJAX or set up a way to populate the edit modal
            fetch(`getRoomDetails.php?room_id=${roomId}`)
                .then(response => response.json())
                .then(room => {
                    // Populate edit modal fields
                    document.getElementById('edit_room_id').value = roomId;
                    document.getElementById('edit_room_number').value = room.RoomNumber;
                    document.getElementById('edit_room_type').value = room.RoomType;
                    document.getElementById('edit_price_per_night').value = room.PricePerNight;
                    
                    // Show the edit modal
                    editRoomModal.style.display = "block";
                })
                .catch(error => {
                    console.error('Error fetching room details:', error);
                    alert('Failed to load room details');
                });
        });
    });

    // Close modals when clicking on the close button
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            addRoomModal.style.display = "none";
            editRoomModal.style.display = "none";
            bookingFormModal.style.display = "none";
        });
    });

    // Close modals when clicking outside the modal
    window.addEventListener('click', function(e) {
        if (e.target === addRoomModal || e.target === editRoomModal || e.target === bookingFormModal) {
            addRoomModal.style.display = "none";
            editRoomModal.style.display = "none";
            bookingFormModal.style.display = "none";
        }
    });
});