document.addEventListener('DOMContentLoaded', async () => {
    let selectedOutline = localStorage.getItem('selectedOutline') || null;
    let selectedPattern = localStorage.getItem('selectedPattern') || null;
    let selectedColor = localStorage.getItem('selectedColor') || null;
    let currentStage = localStorage.getItem('currentStage') || 'outlineOptions';
    let startTime = parseInt(localStorage.getItem('startTime')) || null;
    let timerInterval = null;

    // Load TensorFlow.js model
    const model = await mobilenet.load();
    console.log('Model loaded');

    // Get DOM elements
    const outlineOptions = document.querySelectorAll('#outlineOptions .option');
    const patternOptions = document.querySelectorAll('#patternOptions .option');
    const colorOptions = document.querySelectorAll('#colorOptions .option');
    const submitOutlineButton = document.getElementById('submitOutlineButton');
    const submitPatternButton = document.getElementById('submitPattern');
    const submitColorButton = document.getElementById('submitColor');
    const resultModal = document.getElementById('resultModal');
    const resultMessage = document.getElementById('resultMessage');
    const closeModal = document.getElementById('closeModal');
    const timerElement = document.getElementById('timer');
    const countdownContainer = document.getElementById('countdownContainer');
    const countdownText = document.getElementById('countdownText');

    // Timer functions
    function startTimer() {
        startTime = Date.now();
        localStorage.setItem('startTime', startTime);

        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            timerElement.textContent = `Time: ${elapsed}s`;
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
    }

    function startCountdown() {
        countdownContainer.style.display = 'flex';
        countdownText.textContent = 'Target Image';

        setTimeout(() => {
            countdownText.textContent = 'Ready!';
            setTimeout(() => {
                let countdown = 3;
                const countdownInterval = setInterval(() => {
                    countdownText.textContent = countdown;
                    countdown--;
                    if (countdown < 0) {
                        clearInterval(countdownInterval);
                        countdownContainer.style.display = 'none';
                        showStage('outlineOptions');
                        startTimer();
                    }
                }, 1000);
            }, 1000);
        }, 1000);
    }

    // Restore the game state
    if (currentStage === 'outlineOptions' && startTime) {
        startTimer();
    } else if (currentStage !== 'outlineOptions' && startTime) {
        const elapsed = Math.floor((Date.now() - startTime) / 1000);
        timerElement.textContent = `Time: ${elapsed}s`;
    } else if (currentStage === 'outlineOptions') {
        startCountdown();
    } else {
        showStage(currentStage);
    }

    // Function to show the current stage and hide others
    function showStage(stageId) {
        document.querySelectorAll('.stage-options').forEach(stage => {
            stage.style.display = 'none';
        });
        document.getElementById(stageId).style.display = 'block';
        currentStage = stageId;
        localStorage.setItem('currentStage', stageId);
    }

    // Function to handle image selection
    function handleOptionClick(options, setter, storageKey) {
        options.forEach(option => {
            option.addEventListener('click', () => {
                options.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                setter(option.src);
                localStorage.setItem(storageKey, option.src);
            });
        });
    }

    // Attach event listeners for options
    handleOptionClick(outlineOptions, src => selectedOutline = src, 'selectedOutline');
    handleOptionClick(patternOptions, src => selectedPattern = src, 'selectedPattern');
    handleOptionClick(colorOptions, src => selectedColor = src, 'selectedColor');

    // Add event listeners for buttons
    submitOutlineButton.addEventListener('click', async () => {
        if (!selectedOutline) {
            alert('Please select an outline.');
            return;
        }
        const correctOutline = 'images/target_outline.jpg';
        const isCorrect = await compareImages(correctOutline, selectedOutline);

        showResult(isCorrect, 'patternOptions');
    });

    submitPatternButton.addEventListener('click', async () => {
        if (!selectedPattern) {
            alert('Please select a pattern.');
            return;
        }
        const correctPattern = 'images/target_pattern.jpg';
        const isCorrect = await compareImages(correctPattern, selectedPattern);

        showResult(isCorrect, 'colorOptions');
    });

    submitColorButton.addEventListener('click', async () => {
        if (!selectedColor) {
            alert('Please select a color.');
            return;
        }
        const correctColor = 'images/target_color.jpg';
        const isCorrect = await compareImages(correctColor, selectedColor);

        showResult(isCorrect, null);
        stopTimer();
        localStorage.removeItem('startTime'); // Clear start time
    });

    closeModal.addEventListener('click', () => {
        resultModal.style.display = 'none';
    });

    // Function to compare images using TensorFlow.js
    async function compareImages(correctImageSrc, selectedImageSrc) {
        const correctImage = document.createElement('img');
        const selectedImage = document.createElement('img');

        correctImage.src = correctImageSrc;
        selectedImage.src = selectedImageSrc;

        const [correctTensor, selectedTensor] = await Promise.all([
            imageToTensor(correctImage),
            imageToTensor(selectedImage)
        ]);

        const prediction = await model.classify(selectedTensor);
        const correctPrediction = await model.classify(correctTensor);

        return prediction[0].className === correctPrediction[0].className;
    }

    // Function to convert image to Tensor
    async function imageToTensor(imageElement) {
        return tf.browser.fromPixels(imageElement).resizeNearestNeighbor([224, 224]).toFloat().expandDims();
    }

    // Function to show result and proceed to the next stage
    function showResult(isCorrect, nextStage) {
        resultMessage.textContent = isCorrect ? 'Correct!' : 'Incorrect!';
        resultModal.style.display = 'flex';

        setTimeout(() => {
            resultModal.style.display = 'none';
            if (nextStage) {
                showStage(nextStage);
                if (nextStage === 'outlineOptions') {
                    startTimer();
                }
            }
        }, 2000);
    }
});
