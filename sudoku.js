let timer;
let timeLeft;
let score = 0;
let paused = false;
let board = [];
let solution = [];
let activeCell = null;
let hintCells = new Set();
let currentDifficulty = "easy";
let difficultySettings = {
    easy:   { emptyCells: 20, winPoints: 25, timeLimit: 900 },
    medium: { emptyCells: 30, winPoints: 50, timeLimit: 1200 },
    hard:   { emptyCells: 40, winPoints: 100, timeLimit: 1800 }
};

/* the puzle- method /array ------------------ */
function generateValidPuzzle(emptyCells = 20) {
    const solvedBoard = [
        [5, 3, 4, 6, 7, 8, 9, 1, 2],
        [6, 7, 2, 1, 9, 5, 3, 4, 8],
        [1, 9, 8, 3, 4, 2, 5, 6, 7],
        [8, 5, 9, 7, 6, 1, 4, 2, 3],
        [4, 2, 6, 8, 5, 3, 7, 9, 1],
        [7, 1, 3, 9, 2, 4, 8, 5, 6],
        [9, 6, 1, 5, 3, 7, 2, 8, 4],
        [2, 8, 7, 4, 1, 9, 6, 3, 5],
        [3, 4, 5, 2, 8, 6, 1, 7, 9]
    ];

    const puzzle = JSON.parse(JSON.stringify(solvedBoard));
    let removed = 0;
    while (removed < emptyCells) {
        let row = Math.floor(Math.random() * 9);
        let col = Math.floor(Math.random() * 9);
        if (puzzle[row][col] !== 0) {
            puzzle[row][col] = 0;
            removed++;
        }
    }
    return puzzle;
}

/* settings - timer ------------------ */
function startTimer() {
    clearInterval(timer);
    const settings = difficultySettings[currentDifficulty];

    timer = setInterval(() => {
        if (!paused && timeLeft > 0) {
            timeLeft--;
            updateTimerDisplay();
        } else if (timeLeft <= 0) {
            clearInterval(timer);
            showPopup("Time Out! Game Over!");
            sendLossToServer(settings.timeLimit);
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById("time").textContent =
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
}

function updateScoreDisplay() {
    document.getElementById("scoreValue").textContent = score;
}

/* game controls ------------------ */
function pauseGame() { paused = !paused; }

function addTime() {
    if (score >= 10) {
        timeLeft += 300;
        score -= 10;
        updateScoreDisplay();
    } else showPopup("Not enough points!");
}

function resetGame() { fillBoard(board); }

function exitGame() { window.location.href = "index.php"; }

/* change level difficulty */
function changeDifficulty() {
    const select = document.getElementById("difficultySelect");
    currentDifficulty = select.value;
    showPopup(`🎚 Difficulty set to: ${currentDifficulty.toUpperCase()}`);
    newGame();
}

/* time - diff */
function newGame() {
    const settings = difficultySettings[currentDifficulty];
    board = generateValidPuzzle(settings.emptyCells);
    solution = solveSudoku(JSON.parse(JSON.stringify(board)));
    fillBoard(board);
	timeLeft = settings.timeLimit;
    score = 100;
    paused = false;
    hintCells.clear();
    updateTimerDisplay();
    updateScoreDisplay();
    clearInterval(timer);
    startTimer();
    showPopup(`New ${currentDifficulty.toUpperCase()} Game!`);
}

/* popup */
function showPopup(message) {
    const popup = document.getElementById("popup");
    popup.textContent = message;
    popup.style.display = "block";
    setTimeout(() => (popup.style.display = "none"), 10000);
}

/* board - functions ------------------ */
function fillBoard(board) {
    const cells = document.querySelectorAll(".cell");
    cells.forEach((cell, i) => {
        const row = Math.floor(i / 9);
        const col = i % 9;
        cell.setAttribute("data-row", row);
        cell.setAttribute("data-col", col);
        cell.classList.remove("hinted", "correct", "wrong");

        if (board[row][col] !== 0) {
            cell.value = board[row][col];
            cell.disabled = true;
            cell.classList.add("fixed");
        } else {
            cell.value = "";
            cell.disabled = false;
            cell.classList.remove("fixed");
        }
    });
}

/* win - check ------------------ */
function checkWin() {
    const cells = document.querySelectorAll(".cell");
    for (let i = 0; i < 81; i++) {
        const row = Math.floor(i / 9);
        const col = i % 9;
        const val = parseInt(cells[i].value);
        if (isNaN(val) || val !== solution[row][col]) return false;
    }
    return true;
}

/* hint - function ------------------ */
function showHint() {
    if (!activeCell) {
        showPopup("Select a cell first!");
        return;
    }

    const row = parseInt(activeCell.getAttribute("data-row"));
    const col = parseInt(activeCell.getAttribute("data-col"));
    const correctValue = solution[row][col];

    if (!correctValue || board[row][col] !== 0) {
        showPopup("Hint not available for this cell!");
        return;
    }

    if (score < 5) {
        showPopup("Not enough points for hint!");
        return;
    }

    score -= 5;
    updateScoreDisplay();

    let expression = "";
    let result = 0;
    const ops = ['+', '-', '*'];
    const difficulty = currentDifficulty;

    // math - hint
    for (let tries = 0; tries < 200; tries++) {
        const a = Math.floor(Math.random() * 5) + 1;
        const b = Math.floor(Math.random() * 5) + 1;
        const c = Math.floor(Math.random() * 5) + 1;
        const op1 = ops[Math.floor(Math.random() * ops.length)];
        const op2 = ops[Math.floor(Math.random() * ops.length)];

        let expr;
        if (difficulty === "easy") {
            expr = `${a} ${op1} ${b}`;
        } else if (difficulty === "medium") {
            expr = `${a} ${op1} (${b} ${op2} ${c})`;
        } else {
            const hardForms = [
                `(${a} ${op1} ${b}) ${op2} ${c}`,
                `${a} * (${b} ** 2 - ${c})`,
                `(${a} + ${b}) ** 2 - ${c}`
            ];
            expr = hardForms[Math.floor(Math.random() * hardForms.length)];
        }

        try {
            result = Math.floor(eval(expr));
        } catch {
            result = -1;
        }

        if (result === correctValue && result >= 1 && result <= 9) {
            expression = expr.replaceAll("**", "^");
            break;
        }
    }

    if (!expression) expression = `${correctValue} = ?`;

    showPopup(`Hint: ${expression} = ?`);

    activeCell.classList.add("hinted");
    hintCells.add(activeCell);
}


/*cell-input ------------------ */
function handleCellInput(e) {
    const cell = e.target;
    const row = parseInt(cell.getAttribute("data-row"));
    const col = parseInt(cell.getAttribute("data-col"));
    const val = parseInt(cell.value);

    if (!isNaN(val)) {
        if (val === solution[row][col]) {
            showPopup("Correct answer!");
            cell.classList.remove("hinted");
            cell.classList.add("correct");
            setTimeout(() => cell.classList.remove("correct"), 800);
            hintCells.delete(cell);
        } else {
            showPopup("Incorrect answer!");
            cell.classList.add("wrong");
            setTimeout(() => cell.classList.remove("wrong"), 800);
        }
    }

    if (checkWin()) {
        clearInterval(timer);
        const timeTaken = 1800 - timeLeft;
        const winPoints = difficultySettings[currentDifficulty].winPoints;
        score += winPoints;
        updateScoreDisplay();
        sendResultToServer(score, timeTaken, true);
        showPopup(`You won! +${winPoints} points`);
    }
}

/* solver------------------ */
function solveSudoku(board) {
    if (solveHelper(board)) return board;
    return null;
}

function solveHelper(board) {
    const emptyCell = findEmptyCell(board);
    if (!emptyCell) return true;

    const [row, col] = emptyCell;
    for (let num = 1; num <= 9; num++) {
        if (isValidMove(board, row, col, num)) {
            board[row][col] = num;
            if (solveHelper(board)) return true;
            board[row][col] = 0;
        }
    }
    return false;
}

function findEmptyCell(board) {
    for (let r = 0; r < 9; r++)
        for (let c = 0; c < 9; c++)
            if (board[r][c] === 0) return [r, c];
    return null;
}

function isValidMove(board, row, col, num) {
    for (let i = 0; i < 9; i++)
        if (board[row][i] === num || board[i][col] === num) return false;

    const startRow = Math.floor(row / 3) * 3;
    const startCol = Math.floor(col / 3) * 3;
    for (let i = startRow; i < startRow + 3; i++)
        for (let j = startCol; j < startCol + 3; j++)
            if (board[i][j] === num) return false;

    return true;
}

/* server connection ------------------ */
function sendResultToServer(score, timeTaken, isWin) {
    const error = isWin ? 0 : 1;
    const win = isWin ? 1 : 0;
    fetch('save_result.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `score=${score}&time=${timeTaken}&error=${error}&win=${win}`
    }).then(r => r.text()).then(console.log).catch(console.error);
}

/* dom events ------------------------------------------------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    newGame();

    document.querySelectorAll(".cell").forEach(cell => {
        cell.addEventListener("focus", () => (activeCell = cell));
        cell.addEventListener("input", handleCellInput);
    });
});
