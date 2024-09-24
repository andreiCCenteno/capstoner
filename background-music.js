document.addEventListener('DOMContentLoaded', () => {
    const audio = new Audio('music/background-music.mp3');
    audio.loop = true;

    // Function to play the audio
    function playAudio() {
        audio.play().catch(error => {
            console.error('Error playing audio:', error);
        });
    }

    // Start playback on user interaction
    document.addEventListener('click', () => {
        if (audio.paused) {
            playAudio();
        }
    });

    // Function to stop the music if needed
    window.stopBackgroundMusic = () => {
        if (!audio.paused) {
            audio.pause();
        }
    };

    // Optionally, play audio immediately if user interaction is not a requirement
    // Uncomment the following line to attempt automatic playback
    // playAudio();
});
