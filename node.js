const express = require('express');
const bodyParser = require('body-parser');
const app = express();
const port = 3000;

// Mock target image label for comparison
const targetImageLabel = 'pattern2';

app.use(bodyParser.json());

app.post('/compare', (req, res) => {
    const userSelection = req.body.label;

    // Compare the user's selection with the target image label
    if (userSelection === targetImageLabel) {
        res.json({ message: 'Correct! You selected the right image.' });
    } else {
        res.json({ message: 'Incorrect. Try again!' });
    }
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
