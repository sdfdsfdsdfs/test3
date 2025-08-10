const size = 10;
const mines = 10;
const board = [];
const container = document.getElementById('minesweeper');

function init() {
  for (let y = 0; y < size; y++) {
    board[y] = [];
    for (let x = 0; x < size; x++) {
      board[y][x] = { mine: false, revealed: false, flagged: false, count: 0 };
    }
  }
  let placed = 0;
  while (placed < mines) {
    const x = Math.floor(Math.random() * size);
    const y = Math.floor(Math.random() * size);
    if (!board[y][x].mine) {
      board[y][x].mine = true;
      placed++;
    }
  }
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      if (board[y][x].mine) continue;
      let count = 0;
      for (let j = -1; j <= 1; j++) {
        for (let i = -1; i <= 1; i++) {
          if (i === 0 && j === 0) continue;
          const nx = x + i, ny = y + j;
          if (nx >= 0 && ny >= 0 && nx < size && ny < size && board[ny][nx].mine) count++;
        }
      }
      board[y][x].count = count;
    }
  }
  container.innerHTML = '';
  container.style.display = 'grid';
  container.style.gridTemplateColumns = `repeat(${size}, 30px)`;
  container.style.gridTemplateRows = `repeat(${size}, 30px)`;
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const cell = document.createElement('div');
      cell.className = 'cell';
      cell.dataset.x = x;
      cell.dataset.y = y;
      cell.addEventListener('click', onCellClick);
      cell.addEventListener('contextmenu', onCellRightClick);
      container.appendChild(cell);
    }
  }
}

function onCellClick(e) {
  const x = parseInt(e.target.dataset.x);
  const y = parseInt(e.target.dataset.y);
  reveal(x, y);
}

function onCellRightClick(e) {
  e.preventDefault();
  const x = parseInt(e.target.dataset.x);
  const y = parseInt(e.target.dataset.y);
  const data = board[y][x];
  if (data.revealed) return;
  data.flagged = !data.flagged;
  e.target.classList.toggle('flagged', data.flagged);
}

function reveal(x, y) {
  const data = board[y][x];
  const cell = container.children[y * size + x];
  if (data.revealed || data.flagged) return;
  data.revealed = true;
  cell.classList.add('revealed');
  if (data.mine) {
    cell.textContent = '💣';
    alert('Game Over');
    revealAll();
    return;
  }
  if (data.count > 0) {
    cell.textContent = data.count;
  } else {
    for (let j = -1; j <= 1; j++) {
      for (let i = -1; i <= 1; i++) {
        if (i === 0 && j === 0) continue;
        const nx = x + i, ny = y + j;
        if (nx >= 0 && ny >= 0 && nx < size && ny < size) {
          reveal(nx, ny);
        }
      }
    }
  }
  checkWin();
}

function revealAll() {
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const data = board[y][x];
      const cell = container.children[y * size + x];
      if (data.mine) {
        cell.textContent = '💣';
      } else if (data.count > 0) {
        cell.textContent = data.count;
      }
      cell.classList.add('revealed');
    }
  }
}

function checkWin() {
  let safe = 0;
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      if (!board[y][x].mine && board[y][x].revealed) safe++;
    }
  }
  if (safe === size * size - mines) {
    alert('You win!');
    revealAll();
  }
}

init();
