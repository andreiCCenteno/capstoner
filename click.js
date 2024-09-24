document.addEventListener('DOMContentLoaded', function() {
    var sound = document.getElementById('click-sound');
    // Check if audio element is loaded
    if (sound) {
        sound.addEventListener('canplaythrough', function() {
            console.log('Audio file is ready to play.');
        });
        sound.addEventListener('error', function() {
            console.error('Error loading audio file.');
        });
    } else {
        console.error('Audio element not found.');
    }
});

function playSound() {
    var sound = document.getElementById('click-sound');
    if (sound) {
        sound.play().catch(function(error) {
            console.error('Error playing sound:', error);
        });
    } else {
        console.error('Audio element not found.');
    }
}