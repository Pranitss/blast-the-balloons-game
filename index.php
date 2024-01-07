<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['click_count'])) {
        $_SESSION['click_count'] = 0;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if ($data['action'] === 'blast') {
        $_SESSION['click_count']++;
    }

    echo json_encode(['click_count' => $_SESSION['click_count']]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blast Shapes Game</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #87CEEB; /* Sky Blue */
        }

        #game-container {
            position: relative;
            width: 800px;
            height: 600px;
            border: 2px solid #000;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            animation: floatUp 4s linear infinite;
        }

        .balloon {
            background-color: #FF6347; /* Tomato */
        }

        .circle {
            background-color: #00FF7F; /* MediumSpringGreen */
        }

        .triangle {
            background-color: #4682B4; /* SteelBlue */
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
        }

        #reset-btn {
            margin-top: 20px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
        }

        @keyframes floatUp {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-100px);
            }
            100% {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div id="game-container">
        <button id="reset-btn" onclick="resetGame()">Reset Game</button>
    </div>
    <script>
        const shapeTypes = ['balloon', 'circle', 'triangle'];

        function createShape() {
            const shapeType = getRandomShape();
            const shape = document.createElement('div');
            shape.classList.add('shape', shapeType);
            shape.style.left = `${Math.random() * 750}px`;
            shape.style.animationDelay = `${Math.random()}s`;
            shape.onclick = () => blastShape(shape, shapeType);
            document.getElementById('game-container').appendChild(shape);
        }

        function getRandomShape() {
            return shapeTypes[Math.floor(Math.random() * shapeTypes.length)];
        }

        function blastShape(shape, shapeType) {
            if (shapeType === 'balloon') {
                alert("Blasted!");
                shape.remove();
                updateScore();
            } else {
                alert("Game Over! You clicked a non-balloon shape.");
                resetGame();
            }
        }

        function updateScore() {
            fetch('index.php', {
                method: 'POST',
                body: JSON.stringify({ action: 'blast' }),
                headers: {
                    'Content-Type': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                // Update the score on the UI (if needed)
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function resetGame() {
            document.getElementById('game-container').innerHTML = '';
            document.getElementById('reset-btn').blur(); // Remove focus from the button
            createShapes();
        }

        function createShapes() {
            for (let i = 0; i < 10; i++) {
                createShape();
            }
        }

        createShapes();
    </script>
</body>
</html>
